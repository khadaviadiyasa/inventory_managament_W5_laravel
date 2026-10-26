@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('products.update', $product) }}">
        @method('PUT')
        @include('products._form')
    </form>
</div>
@endsection
