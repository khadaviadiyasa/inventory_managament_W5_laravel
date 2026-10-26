@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="panel">
    <form method="GET" action="{{ route('categories.index') }}" class="toolbar">
        <div class="form-group">
            <label for="q">Cari kategori</label>
            <input type="text" id="q" name="q" value="{{ $search }}" placeholder="Nama atau deskripsi...">
        </div>
        <button type="submit" class="btn">Cari</button>
        <a href="{{ route('categories.index') }}" class="btn btn-light">Reset</a>
        <a href="{{ route('categories.create') }}" class="btn" style="margin-left:auto">+ Tambah Kategori</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Nama</th><th>Deskripsi</th><th class="num">Produk</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description ?: '-' }}</td>
                    <td class="num">{{ $category->products_count }}</td>
                    <td>{{ $category->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-light btn-sm">Edit</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                  onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $categories->links() }}
</div>
@endsection
