@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('users.update', $user) }}">
        @method('PUT')
        @include('users._form')
    </form>
</div>
@endsection
