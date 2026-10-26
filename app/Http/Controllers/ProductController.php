<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /** Whitelist kolom sorting (sama seperti $sortOptions di P4). */
    private const SORTS = [
        'sku'      => 'products.sku',
        'name'     => 'products.name',
        'category' => 'categories.name',
        'price'    => 'products.price',
        'stock'    => 'products.stock',
        'created'  => 'products.id',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $sort   = (string) $request->query('sort', 'created');
        $sort   = array_key_exists($sort, self::SORTS) ? $sort : 'created';
        $dir    = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $products = Product::query()
            ->select('products.*')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->with(['category:id,name', 'suppliers:id,name'])
            // Pencarian: nama ATAU SKU
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.name', 'like', "%{$search}%")
                      ->orWhere('products.sku', 'like', "%{$search}%");
                });
            })
            // Filter kategori
            ->when($request->integer('category'), function ($query, $categoryId) {
                $query->where('products.category_id', $categoryId);
            })
            ->orderBy(self::SORTS[$sort], $dir)
            ->orderByDesc('products.id')
            ->paginate(10)
            ->withQueryString();   // pencarian/filter/sort tidak hilang saat pindah halaman

        $categories = Category::orderBy('name')->get(['id', 'name']);

        return view('products.index', compact('products', 'categories', 'search', 'sort', 'dir'));
    }

    public function create(): View
    {
        return view('products.create', [
            'categories'        => Category::orderBy('name')->get(['id', 'name']),
            'suppliers'         => Supplier::orderBy('name')->get(['id', 'name']),
            'selectedSuppliers' => [],
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            // Produk baru selalu mulai dari stok 0; stok hanya berubah lewat transaksi stok.
            $product = Product::create($request->productData() + ['stock' => 0]);
            $product->suppliers()->sync($request->supplierSyncData());
        });

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $product->load('suppliers');

        return view('products.edit', [
            'product'           => $product,
            'categories'        => Category::orderBy('name')->get(['id', 'name']),
            'suppliers'         => Supplier::orderBy('name')->get(['id', 'name']),
            'selectedSuppliers' => $product->suppliers->pluck('pivot.supplier_price', 'id')->all(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->productData());   // stok tidak ikut diubah
            $product->suppliers()->sync($request->supplierSyncData());
        });

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Aturan P4: produk yang sudah punya histori transaksi tidak boleh dihapus.
        if ($product->stockMovements()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena masih memiliki histori transaksi stok.');
        }

        DB::transaction(function () use ($product) {
            $product->suppliers()->detach();
            $product->delete();
        });

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
