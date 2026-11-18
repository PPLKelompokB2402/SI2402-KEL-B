@extends('layouts.app')
@section('title', 'Masuk Administrator — KolaboraEO')
@section('content')
    <section class="auth-page container">
        <div class="auth-card">
            <span class="eyebrow">Portal administrator</span>
            <h1>Masuk Administrator</h1>
            <p>Gunakan akun administrator untuk mengelola verifikasi dan data vendor.</p>
            <form method="POST" action="{{ route('admin.login.store') }}" class="stack-form">@csrf
                <label>Email<input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></label>
                <label>Kata sandi<input type="password" name="password" required autocomplete="current-password"></label>
                <button class="button button-dark full-button" type="submit">Masuk ke portal admin</button>
            </form>
        </div>
    </section>
@endsection
