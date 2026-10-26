<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon">▣</div>
        <div class="brand-name">INVENTORY<small>Management System</small></div>
    </div>

    <p class="menu-label">MENU UTAMA</p>
    <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
    <a class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Produk</a>
    <a class="nav-item {{ request()->routeIs('stock.*') ? 'active' : '' }}" href="{{ route('stock.index') }}">Stok Masuk/Keluar</a>
    <a class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Laporan</a>

    @if(auth()->user()->isAdmin())
        <p class="menu-label">ADMIN</p>
        <a class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Kategori</a>
        <a class="nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">Supplier</a>
        <a class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Pengguna</a>
    @endif
</aside>
