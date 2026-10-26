@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
<div class="cards">
    <div class="stat"><span>Total Akun</span><strong>{{ $totals['users'] }}</strong></div>
    <div class="stat"><span>Admin</span><strong>{{ $totals['admins'] }}</strong></div>
    <div class="stat"><span>Staff</span><strong>{{ $totals['staff'] }}</strong></div>
</div>

<div class="panel">
    <form method="GET" action="{{ route('users.index') }}" class="toolbar">
        <div class="form-group">
            <label for="q">Cari pengguna</label>
            <input type="text" id="q" name="q" value="{{ $search }}" placeholder="Username, nama, role...">
        </div>
        <button type="submit" class="btn">Cari</button>
        <a href="{{ route('users.index') }}" class="btn btn-light">Reset</a>
        <a href="{{ route('users.create') }}" class="btn" style="margin-left:auto">+ Tambah Pengguna</a>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->username }} @if($user->id === auth()->id())<span class="muted">(Anda)</span>@endif</td>
                    <td>{{ $user->full_name }}</td>
                    <td><span class="role-tag">{{ $user->role }}</span></td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-light btn-sm">Edit</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada pengguna.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection
