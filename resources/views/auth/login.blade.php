@extends('layouts.app')
@section('title', 'Masuk — KolaboraEO')
@section('content')
    <section class="auth-page container">
        <div class="auth-card"><span class="eyebrow">Selamat datang kembali</span><h1>Masuk ke KolaboraEO</h1><p>Akses akun pelanggan, vendor, atau administrator kamu.</p>
            <form method="POST" action="{{ route('login.store') }}" class="stack-form">@csrf
                <label>Email<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
                <label>Kata sandi<input type="password" name="password" required autocomplete="current-password"></label>
                <button class="button button-dark full-button" type="submit">Masuk</button>
            </form>
            <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
        </div>
    </section>
@endsection
