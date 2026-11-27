@extends('layouts.super-admin')

@section('title', 'Administrator')

@section('portal-content')
    <section class="portal-page">
        <div class="admin-page-heading"><div><span class="section-kicker">Super administrator</span><h1>Kelola administrator</h1><p>Kelola akun administrator platform. Akun administrator tidak dapat didaftarkan melalui halaman registrasi publik.</p></div></div>
        <section class="portal-panel admin-list-panel">
            <div class="panel-heading"><div><h2>Tim administrator</h2><p>{{ $admins->count() }} akun terdaftar · {{ $jumlahAktif }} aktif</p></div></div>
            <div class="admin-form-actions">
                <details class="category-add-details" @if($errors->hasAny(['nama', 'email', 'password'])) open @endif><summary class="button button-dark">＋ Buat akun administrator</summary>
                    <form method="POST" action="{{ route('super-admin.administrator.store') }}" class="category-inline-form">@csrf
                        <input name="nama" required maxlength="120" placeholder="Nama administrator" value="{{ old('nama') }}">
                        <input name="email" type="email" required maxlength="255" placeholder="Email" value="{{ old('email') }}">
                        <input name="password" type="password" minlength="8" required placeholder="Kata sandi awal" autocomplete="new-password">
                        <input name="password_confirmation" type="password" minlength="8" required placeholder="Ulangi kata sandi" autocomplete="new-password">
                        <button class="button button-dark" type="submit">Buat akun</button>
                    </form>
                </details>
            </div>
            <div class="admin-account-rows">
                @forelse($admins as $admin)
                    <article class="admin-account-row" data-admin="{{ $admin->email }}">
                        <span class="avatar">{{ $admin->inisial }}</span>
                        <div class="admin-account-name"><strong>{{ $admin->nama }}</strong><small>{{ $admin->email }}</small></div>
                        <span class="status-tag {{ $admin->status_aktif ? 'terverifikasi' : 'ditolak' }}">{{ $admin->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                    </article>
                @empty
                    <div class="empty-cell">Belum ada akun administrator.</div>
                @endforelse
            </div>
        </section>
    </section>
@endsection
