@extends('layouts.portal')

@section('title', 'Ringkasan bisnis')

@section('portal-content')
    <section class="portal-page vendor-dashboard-page">
        <div class="portal-heading">
            <div><span class="section-kicker">Portal vendor</span><h1>Ringkasan bisnis</h1><p>Kelola profil dan pastikan informasi bisnis selalu terbaru.</p></div>
        </div>
        <div class="portal-stats vendor-overview-stats">
            <article><span class="metric-icon"><x-icon name="store" /></span><strong>{{ $stats['usaha'] }}</strong><span>Profil usaha</span></article>
            <article><span class="metric-icon"><x-icon name="clock" /></span><strong>{{ $stats['menunggu'] }}</strong><span>Menunggu verifikasi</span></article>
        </div>
        <div class="vendor-dashboard-grid">
            <section class="portal-panel completion-panel">
                <div class="panel-heading"><h2>Kelengkapan profil{{ $vendor ? ' · '.$vendor->nama_usaha : '' }}</h2><strong>{{ $kelengkapan }}%</strong></div>
                <div class="progress-track"><span style="width: {{ $kelengkapan }}%"></span></div>
                @foreach($checks as $check)
                    <div class="completion-row"><span>{{ $check['label'] }}</span><b class="{{ $check['complete'] ? '' : 'warning-mark' }}">{{ $check['complete'] ? '✓' : '!' }}</b></div>
                @endforeach
                @if(!$vendor)<a class="button button-dark" href="{{ route('vendor.usaha') }}">Tambahkan usaha</a>@endif
            </section>
        </div>
        <section class="dashboard-vendor-links"><div><span class="section-kicker">Kelola bisnis</span><h2>Informasi usaha</h2></div><div class="dashboard-link-row"><a href="{{ route('vendor.usaha') }}">Usaha saya <span>→</span></a><a href="{{ route('vendor.profil') }}">Profil & legalitas <span>→</span></a></div></section>
    </section>
@endsection
