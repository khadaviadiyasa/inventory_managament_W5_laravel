@extends('layouts.app')

@section('title', 'Laporan Inventori')

@section('content')
@if($error)
    <x-alert type="error">{{ $error }}</x-alert>
@endif

<div class="cards">
    <div class="stat"><span>Total Produk</span><strong>{{ $summary->total_products }}</strong></div>
    <div class="stat"><span>Total Stok</span><strong>{{ number_format($summary->total_stock, 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Nilai Inventori</span><strong>Rp {{ number_format($summary->inventory_value, 0, ',', '.') }}</strong></div>
    <div class="stat"><span>Stok Rendah</span><strong>{{ $summary->low_stock }}</strong></div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('reports.index') }}" class="toolbar">
        <div class="form-group">
            <label for="date_from">Dari tanggal</label>
            <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}">
        </div>
        <div class="form-group">
            <label for="date_to">Sampai tanggal</label>
            <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}">
        </div>
        <button type="submit" class="btn">Terapkan</button>
        <a href="{{ route('reports.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="cards">
        <div class="stat"><span>Transaksi pada periode</span><strong>{{ $movementSummary->total_transactions }}</strong></div>
        <div class="stat"><span>Total Masuk</span><strong>{{ number_format($movementSummary->total_in, 0, ',', '.') }}</strong></div>
        <div class="stat"><span>Total Keluar</span><strong>{{ number_format($movementSummary->total_out, 0, ',', '.') }}</strong></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th><th>Produk</th><th>Kategori</th>
                    <th class="num">Harga</th><th class="num">Stok</th>
                    <th class="num">Masuk</th><th class="num">Keluar</th><th class="num">Transaksi</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($reports as $row)
                <tr>
                    <td>{{ $row->sku }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->category_name }}</td>
                    <td class="num">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                    <td class="num">{{ $row->stock }}</td>
                    <td class="num">{{ $row->total_in }}</td>
                    <td class="num">{{ $row->total_out }}</td>
                    <td class="num">{{ $row->total_transactions }}</td>
                    <td><x-stock-badge :product="$row" /></td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty">Belum ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
