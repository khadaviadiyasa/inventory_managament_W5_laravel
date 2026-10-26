<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        // ---- Validasi filter tanggal (aturan P4) ----
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');
        $error    = null;

        $validator = Validator::make(
            ['date_from' => $dateFrom, 'date_to' => $dateTo],
            [
                'date_from' => ['nullable', 'date_format:Y-m-d'],
                'date_to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            ],
            [
                'date_from.date_format'   => 'Tanggal awal tidak valid.',
                'date_to.date_format'     => 'Tanggal akhir tidak valid.',
                'date_to.after_or_equal'  => 'Tanggal awal tidak boleh melewati tanggal akhir.',
            ]
        );

        if ($validator->fails()) {
            $error    = $validator->errors()->first();
            $dateFrom = $dateTo = null;
        }

        $from = $dateFrom ? Carbon::createFromFormat('!Y-m-d', $dateFrom)->startOfDay() : null;
        // batas atas eksklusif: awal hari berikutnya
        $to   = $dateTo ? Carbon::createFromFormat('!Y-m-d', $dateTo)->addDay()->startOfDay() : null;

        // ---- Ringkasan inventori saat ini ----
        $summary = Product::query()
            ->selectRaw('COUNT(*) AS total_products')
            ->selectRaw('COALESCE(SUM(stock), 0) AS total_stock')
            ->selectRaw('COALESCE(SUM(price * stock), 0) AS inventory_value')
            ->selectRaw('COALESCE(SUM(CASE WHEN stock <= minimum_stock THEN 1 ELSE 0 END), 0) AS low_stock')
            ->first();

        // ---- Ringkasan transaksi pada periode ----
        $movementSummary = StockMovement::query()
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<', $to))
            ->selectRaw('COUNT(*) AS total_transactions')
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'IN'  THEN quantity ELSE 0 END), 0) AS total_in")
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'OUT' THEN quantity ELSE 0 END), 0) AS total_out")
            ->first();

        // ---- Laporan agregat per produk (LEFT JOIN agar produk tanpa transaksi tetap muncul) ----
        $reports = Product::query()
            ->select('products.*', 'categories.name as category_name')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('stock_movements', function ($join) use ($from, $to) {
                $join->on('stock_movements.product_id', '=', 'products.id');
                if ($from) {
                    $join->where('stock_movements.created_at', '>=', $from);
                }
                if ($to) {
                    $join->where('stock_movements.created_at', '<', $to);
                }
            })
            ->selectRaw("COALESCE(SUM(CASE WHEN stock_movements.type = 'IN'  THEN stock_movements.quantity ELSE 0 END), 0) AS total_in")
            ->selectRaw("COALESCE(SUM(CASE WHEN stock_movements.type = 'OUT' THEN stock_movements.quantity ELSE 0 END), 0) AS total_out")
            ->selectRaw('COUNT(stock_movements.id) AS total_transactions')
            ->groupBy('products.id', 'categories.name')
            ->orderBy('products.name')
            ->get();

        return view('reports.index', compact('summary', 'movementSummary', 'reports', 'dateFrom', 'dateTo', 'error'));
    }
}
