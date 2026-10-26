@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @method('PUT')
        @include('suppliers._form')
    </form>
</div>
@endsection
