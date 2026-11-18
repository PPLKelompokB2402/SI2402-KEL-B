@extends('layouts.app')
@section('title', 'Masuk Super Administrator — KolaboraEO')
@section('content')
    <section class="auth-page container">
        <div class="auth-card">
            <span class="eyebrow">Akses khusus super administrator</span>
            <h1>Masuk Super Administrator</h1>
            <p>Kelola akun administrator dan pengaturan inti platform.</p>
            <form method="POST" action="{{ route('super-admin.login.store') }}" class="stack-form">@csrf
                <label>Email<input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"></label>
                <label>Kata sandi<input type="password" name="password" required autocomplete="current-password"></label>
                <button class="button button-dark full-button" type="submit">Masuk ke portal super admin</button>
            </form>
        </div>
    </section>
@endsection
