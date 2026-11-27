@extends('layouts.super-admin')

@section('title', 'Edit administrator')

@section('portal-content')
    <section class="portal-page">
        <div class="breadcrumbs"><a href="{{ route('super-admin.administrator') }}">Administrator</a><span>/</span><span>Edit akun</span></div>
        <div class="admin-page-heading"><div><span class="section-kicker">Super administrator</span><h1>Edit akun administrator</h1><p>Perbarui nama, email, atau kata sandi akun <strong>{{ $admin->email }}</strong>.</p></div></div>
        <section class="portal-panel admin-list-panel admin-edit-panel">
            <form method="POST" action="{{ route('super-admin.administrator.update', $admin) }}" class="stack-form">@csrf @method('PUT')
                <label>Nama administrator<input name="nama" required maxlength="120" value="{{ old('nama', $admin->nama) }}"></label>
                <label>Email<input name="email" type="email" required maxlength="255" value="{{ old('email', $admin->email) }}"></label>
                <label><span>Kata sandi baru <small class="muted">(kosongkan bila tidak diganti)</small></span><input name="password" type="password" minlength="8" autocomplete="new-password"></label>
                <label>Ulangi kata sandi baru<input name="password_confirmation" type="password" minlength="8" autocomplete="new-password"></label>
                <div class="form-actions">
                    <button class="button button-dark" type="submit">Simpan perubahan</button>
                    <a class="button button-outline" href="{{ route('super-admin.administrator') }}">Batal</a>
                </div>
            </form>
        </section>
    </section>
@endsection
