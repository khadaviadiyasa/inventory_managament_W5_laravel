@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="panel">
    {{-- Pencarian + filter + sorting (GET) --}}
    <form method="GET" action="{{ route('products.index') }}" class="toolbar">
        <div class="form-group">
            <label for="q">Cari (nama / SKU)</label>
            <input type="text" id="q" name="q" value="{{ $search }}" placeholder="Ketik kata kunci...">
        </div>
        <div class="form-group">
            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="sort">Urutkan</label>
            <select id="sort" name="sort">
                @foreach(['created' => 'Terbaru', 'sku' => 'SKU', 'name' => 'Nama', 'category' => 'Kategori', 'price' => 'Harga', 'stock' => 'Stok'] as $key => $text)
                    <option value="{{ $key }}" @selected($sort === $key)>{{ $text }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="dir">Arah</label>
            <select id="dir" name="dir">
                <option value="desc" @selected($dir === 'desc')>Menurun</option>
                <option value="asc" @selected($dir === 'asc')>Menaik</option>
            </select>
        </div>
        <button type="submit" class="btn">Terapkan</button>
        <a href="{{ route('products.index') }}" class="btn btn-light">Reset</a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" class="btn" style="margin-left:auto">+ Tambah Produk</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th><x-sort-link route="products.index" column="sku" label="SKU" /></th>
                    <th><x-sort-link route="products.index" column="name" label="Nama" /></th>
                    <th><x-sort-link route="products.index" column="category" label="Kategori" /></th>
                    <th>Supplier</th>
                    <th class="num"><x-sort-link route="products.index" column="price" label="Harga" /></th>
                    <th class="num"><x-sort-link route="products.index" column="stock" label="Stok" /></th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th>Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td>{{ $product->suppliers->pluck('name')->sort()->implode(', ') ?: '-' }}</td>
                    <td class="num">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="num">{{ $product->stock }} <span class="muted">(min {{ $product->minimum_stock }})</span></td>
                    <td><x-stock-badge :product="$product" /></td>
                    @if(auth()->user()->isAdmin())
                        <td>
                            <div class="actions">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-light btn-sm">Edit</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                      onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ auth()->user()->isAdmin() ? 8 : 7 }}" class="empty">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $products->links() }}
</div>
@endsection
