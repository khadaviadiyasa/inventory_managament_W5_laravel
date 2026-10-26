@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('users.store') }}">
        @include('users._form')
    </form>
</div>
@endsection
