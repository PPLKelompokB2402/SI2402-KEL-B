@extends('layouts.app')
@section('title', 'Daftar — KolaboraEO')
@section('content')
    <section class="auth-page container">
        <div class="auth-card"><span class="eyebrow">Mulai berkolaborasi</span><h1>Buat akun KolaboraEO</h1><p>Pilih peran akun. Administrator hanya dapat dibuat melalui portal internal.</p>
            <form method="POST" action="{{ route('register.store') }}" class="stack-form">@csrf
                <label>Nama lengkap<input type="text" name="nama" value="{{ old('nama') }}" required maxlength="120" autocomplete="name"></label>
                <label>Email<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
                <label>No. HP / WhatsApp<input type="tel" name="no_hp" value="{{ old('no_hp') }}" required maxlength="20" placeholder="08xxxxxxxxxx" autocomplete="tel"></label>
                <label>Daftar sebagai<select name="role" required><option value="pelanggan" @selected(old('role') === 'pelanggan')>Pelanggan</option><option value="vendor" @selected(old('role') === 'vendor')>Vendor</option></select></label>
                <label>Kata sandi<input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
                <label>Konfirmasi kata sandi<input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label>
                <button class="button button-dark full-button" type="submit">Buat akun</button>
            </form>
            <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
        </div>
    </section>
@endsection
