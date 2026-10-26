@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="login-wrap">
    <div class="login-card">
        <h1>Inventory Management</h1>
        <p class="muted">Silakan masuk untuk melanjutkan.</p>

        @if($errors->any())
            <x-alert type="error">{{ $errors->first() }}</x-alert>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <x-form.input name="username" label="Username" required autofocus autocomplete="username" />
            <x-form.input name="password" label="Password" type="password" required autocomplete="current-password" />
            <button type="submit" class="btn" style="width:100%">Masuk</button>
        </form>
    </div>
</div>
@endsection
