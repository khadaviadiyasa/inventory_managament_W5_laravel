<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('name')->get(['id', 'sku', 'name', 'stock', 'minimum_stock']);

        $summary = [
            'total_products' => $products->count(),
            'total_stock'    => (int) $products->sum('stock'),
            'low_stock'      => $products->filter(fn (Product $p) => $p->isLowStock())->count(),
        ];

        $history = StockMovement::with(['product:id,sku,name', 'user:id,full_name'])
            ->latest('created_at')->latest('id')
            ->limit(20)
            ->get();

        return view('stock.index', compact('products', 'summary', 'history'));
    }

    /**
     * Transaksi barang masuk (IN) / keluar (OUT).
     * Aturan P4 yang dipertahankan:
     *  - qty bilangan bulat >= 1
     *  - OUT tidak boleh melebihi stok
     *  - baris produk dikunci (FOR UPDATE) agar aman saat transaksi bersamaan
     *  - perubahan stok + pencatatan histori satu transaksi DB (atomic; rollback jika gagal)
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'       => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT])],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:2147483647'],
            'notes'      => ['nullable', 'string', 'max:5000'],
        ], [
            'type.required'       => 'Jenis transaksi tidak valid.',
            'type.in'             => 'Jenis transaksi tidak valid.',
            'product_id.required' => 'Silakan pilih produk yang valid.',
            'product_id.exists'   => 'Produk tidak ditemukan.',
            'quantity.required'   => 'Jumlah barang harus bilangan bulat lebih dari nol.',
            'quantity.integer'    => 'Jumlah barang harus bilangan bulat lebih dari nol.',
            'quantity.min'        => 'Jumlah barang harus bilangan bulat lebih dari nol.',
            'quantity.max'        => 'Jumlah barang terlalu besar.',
            'notes.max'           => 'Catatan maksimal 5000 karakter.',
        ]);

        $quantity = (int) $data['quantity'];

        // Jika closure melempar exception, Laravel otomatis rollBack().
        $product = DB::transaction(function () use ($data, $quantity) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);

            if ($data['type'] === StockMovement::TYPE_OUT) {
                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'quantity' => "Stok tidak mencukupi. Stok tersedia: {$product->stock} unit.",
                    ]);
                }

                // Pengaman tambahan: kurangi hanya jika stok masih cukup.
                $affected = Product::whereKey($product->id)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($affected !== 1) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Transaksi dibatalkan karena stok tidak mencukupi.',
                    ]);
                }
            } else {
                $product->increment('stock', $quantity);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'user_id'    => Auth::id(),
                'type'       => $data['type'],
                'quantity'   => $quantity,
                'notes'      => $data['notes'] ?? null,
            ]);

            return $product;
        });

        $label = $data['type'] === StockMovement::TYPE_IN ? 'Barang masuk' : 'Barang keluar';

        return redirect()->route('stock.index')
            ->with('success', "{$label} berhasil dicatat untuk {$product->name}. Jumlah: {$quantity} unit.");
    }
}
