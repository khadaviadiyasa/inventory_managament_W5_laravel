@props(['product'])
@if($product->isLowStock())
    <span class="badge badge-low">Stok Rendah</span>
@else
    <span class="badge badge-ok">Tersedia</span>
@endif
