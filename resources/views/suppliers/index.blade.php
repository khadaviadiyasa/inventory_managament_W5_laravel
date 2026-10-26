@extends('layouts.app')

@section('title', 'Supplier')

@section('content')
<div class="panel">
    <form method="GET" action="{{ route('suppliers.index') }}" class="toolbar">
        <div class="form-group">
            <label for="q">Cari supplier</label>
            <input type="text" id="q" name="q" value="{{ $search }}" placeholder="Nama, kontak, email, telepon...">
        </div>
        <button type="submit" class="btn">Cari</button>
        <a href="{{ route('suppliers.index') }}" class="btn btn-light">Reset</a>
        <a href="{{ route('suppliers.create') }}" class="btn" style="margin-left:auto">+ Tambah Supplier</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Kontak</th><th>Telepon</th><th>Email</th><th class="num">Produk</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->contact_person ?: '-' }}</td>
                    <td>{{ $supplier->phone ?: '-' }}</td>
                    <td>{{ $supplier->email ?: '-' }}</td>
                    <td class="num">{{ $supplier->products_count }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-light btn-sm">Edit</a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                  onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada supplier.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $suppliers->links() }}
</div>
@endsection
