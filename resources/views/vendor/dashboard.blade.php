@extends('layouts.portal')

@section('title', 'Ringkasan bisnis')

@section('portal-content')
    <section class="portal-page vendor-dashboard-page">
        <div class="portal-heading">
            <div><span class="section-kicker">Portal vendor</span><h1>Ringkasan bisnis</h1><p>Anda masuk sebagai vendor. Sesi login tersimpan sampai Anda keluar.</p></div>
        </div>
        <section class="portal-panel">
            <h2>Halo, {{ $user->nama }}</h2>
            <p class="muted">{{ $user->email }} · {{ $user->no_hp }}</p>
        </section>
    </section>
@endsection
