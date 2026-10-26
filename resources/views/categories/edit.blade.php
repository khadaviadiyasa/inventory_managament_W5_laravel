@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('categories.update', $category) }}">
        @method('PUT')
        @include('categories._form')
    </form>
</div>
@endsection
