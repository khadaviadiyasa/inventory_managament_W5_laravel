@extends('layouts.app')

@section('title', 'Tambah Supplier')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('suppliers.store') }}">
        @include('suppliers._form')
    </form>
</div>
@endsection
