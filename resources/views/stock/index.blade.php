@extends('layouts.app')

@section('title', 'Stok Masuk / Keluar')

@section('content')
<div class="cards">
    <div class="stat"><span>Total Produk</span><strong>{{ $summary['total_products'] }}</strong></div>
    <div class="stat"><span>Total Stok</span><strong>{{ number_format($summary['total_stock'], 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Produk Stok Rendah</span><strong>{{ $summary['low_stock'] }}</strong></div>
</div>

<div class="panel">
    <h2>Catat Transaksi</h2>
    <form method="POST" action="{{ route('stock.store') }}">
        @csrf
        <div class="grid-2">
            <x-form.select name="type" label="Jenis Transaksi"
                :options="['IN' => 'Barang Masuk (IN)', 'OUT' => 'Barang Keluar (OUT)']"
                placeholder="-- Pilih jenis --" />
            <x-form.select name="product_id" label="Produk"
                :options="$products->mapWithKeys(fn ($p) => [$p->id => $p->sku . ' — ' . $p->name . ' (stok: ' . $p->stock . ')'])->all()"
                placeholder="-- Pilih produk --" />
        </div>
        <x-form.input name="quantity" label="Jumlah" type="number" min="1" step="1" required />
        <x-form.textarea name="notes" label="Catatan (opsional)" />
        <button type="submit" class="btn">Simpan Transaksi</button>
    </form>
</div>

<div class="panel">
    <h2>20 Transaksi Terakhir</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>SKU</th><th>Produk</th><th>Jenis</th><th class="num">Jumlah</th><th>Catatan</th><th>Oleh</th></tr></thead>
            <tbody>
            @forelse($history as $m)
                <tr>
                    <td>{{ $m->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $m->product->sku }}</td>
                    <td>{{ $m->product->name }}</td>
                    <td><span class="badge {{ $m->type === 'IN' ? 'badge-in' : 'badge-out' }}">{{ $m->type === 'IN' ? 'Masuk' : 'Keluar' }}</span></td>
                    <td class="num">{{ $m->quantity }}</td>
                    <td>{{ $m->notes ?: '-' }}</td>
                    <td>{{ $m->user->full_name }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada transaksi stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
