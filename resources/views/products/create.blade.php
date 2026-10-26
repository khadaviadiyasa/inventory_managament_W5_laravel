@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('products.store') }}">
        @include('products._form')
    </form>
</div>
@endsection
