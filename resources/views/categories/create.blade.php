@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('categories.store') }}">
        @include('categories._form')
    </form>
</div>
@endsection
