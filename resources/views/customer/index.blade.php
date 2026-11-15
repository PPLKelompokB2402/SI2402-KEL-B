@extends('layouts.app')

@section('title', 'Temukan Vendor Acara — KolaboraEO')

@section('content')
    <section class="catalog-hero">
        <div class="container hero-content">
            <span class="eyebrow light-eyebrow">Satu tempat untuk semua momen</span>
            <h1>Acara hebat dimulai<br>bersama <em>partner yang tepat.</em></h1>
            <p>Jelajahi vendor terpercaya, lihat karya mereka, bandingkan layanan, dan temukan yang paling cocok untuk acaramu.</p>
        </div>
        <div class="hero-orb orb-one"></div><div class="hero-orb orb-two"></div>
    </section>

    <section class="container catalog-section">
        <div class="catalog-heading">
            <div><span class="section-kicker">Katalog pilihan</span><h2>Vendor untuk acaramu</h2><p>{{ $vendors->total() }} vendor terverifikasi</p></div>
        </div>

            <div class="catalog-grid-area">
                @if($vendors->isEmpty())
                    <div class="empty-state"><div>✧</div><h3>Belum ada vendor terverifikasi</h3><p>Vendor akan tampil di sini setelah profilnya diverifikasi administrator.</p></div>
                @else
                    <div class="vendor-grid">
                        @foreach($vendors as $vendor)
                            <article class="vendor-card">
                                <div class="vendor-image" @if($vendor->sampul_url) style="--cover: url('{{ $vendor->sampul_url }}')" @endif>
                                    <span class="image-label">✦ {{ $vendor->kategori->nama_kategori }}</span>
                                </div>
                                <div class="vendor-card-body">
                                    <div class="vendor-title-row"><h3>{{ $vendor->nama_usaha }}</h3></div>
                                    <div class="vendor-meta"><span>⌖ {{ $vendor->lokasi_layanan }}</span></div>
                                    <div class="vendor-card-bottom"><div><small>Mulai dari</small><strong>{{ $vendor->harga_format }}</strong></div><span class="verified-badge">✓ Profil terverifikasi</span></div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="pagination-wrap">{{ $vendors->links() }}</div>
                @endif
            </div>
    </section>

    <section class="container safe-banner">
        <div class="safe-icon">✓</div><div><strong>Informasi vendor yang ditinjau</strong><p>Profil yang tampil di katalog telah melalui proses verifikasi KolaboraEO.</p></div>
        <a href="{{ route('register') }}">Bergabung dengan KolaboraEO <span>→</span></a>
    </section>
@endsection
