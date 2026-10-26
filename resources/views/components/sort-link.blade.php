{{-- Link header kolom yang mempertahankan q/category dan menukar arah sort --}}
@props(['route', 'column', 'label'])
@php
    $currentSort = request('sort', 'created');
    $currentDir  = request('dir', 'desc') === 'asc' ? 'asc' : 'desc';
    $active      = $currentSort === $column;
    $nextDir     = ($active && $currentDir === 'asc') ? 'desc' : 'asc';
@endphp
<a href="{{ route($route, array_merge(request()->except('page'), ['sort' => $column, 'dir' => $nextDir])) }}">
    {{ $label }}@if($active) {{ $currentDir === 'asc' ? '▲' : '▼' }}@endif
</a>
