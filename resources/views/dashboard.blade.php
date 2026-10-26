@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="cards">
    <div class="stat"><span>Total Produk</span><strong>{{ number_format($summary['total_products'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Kategori</span><strong>{{ number_format($summary['total_categories'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Supplier</span><strong>{{ number_format($summary['total_suppliers'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Nilai Inventori</span><strong>Rp {{ number_format($summary['inventory_value'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Stok Rendah</span><strong>{{ number_format($summary['low_stock'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Total Barang Masuk</span><strong>{{ number_format($summary['total_in'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Total Barang Keluar</span><strong>{{ number_format($summary['total_out'], 0, ',', '.') }}</strong></div>
</div>

<div class="panel">
    <h2>Produk Terbaru</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>SKU</th><th>Nama</th><th>Kategori</th><th class="num">Harga</th><th class="num">Stok</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name }}</td>
                    <td class="num">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="num">{{ $product->stock }}</td>
                    <td><x-stock-badge :product="$product" /></td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <h2>Aktivitas Stok Terakhir</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Produk</th><th>Jenis</th><th class="num">Jumlah</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $m->product->name }}</td>
                    <td><span class="badge {{ $m->type === 'IN' ? 'badge-in' : 'badge-out' }}">{{ $m->type === 'IN' ? 'Masuk' : 'Keluar' }}</span></td>
                    <td class="num">{{ $m->quantity }}</td>
                    <td>{{ $m->user->full_name }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada transaksi stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
