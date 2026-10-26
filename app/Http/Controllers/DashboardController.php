<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $summary = [
            'total_products'   => Product::count(),
            'total_categories' => Category::count(),
            'total_suppliers'  => Supplier::count(),
            'inventory_value'  => (float) Product::selectRaw('COALESCE(SUM(price * stock), 0) AS value')->value('value'),
            'low_stock'        => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            'total_in'         => (int) StockMovement::where('type', StockMovement::TYPE_IN)->sum('quantity'),
            'total_out'        => (int) StockMovement::where('type', StockMovement::TYPE_OUT)->sum('quantity'),
        ];

        $products = Product::with('category:id,name')->latest('id')->limit(5)->get();

        $movements = StockMovement::with(['product:id,name', 'user:id,full_name'])
            ->latest('created_at')->latest('id')
            ->limit(5)->get();

        return view('dashboard', compact('summary', 'products', 'movements'));
    }
}
