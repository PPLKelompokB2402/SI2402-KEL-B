@extends('layouts.app')

@section('title', 'Temukan Vendor Acara — KolaboraEO')

@section('content')
    <section class="catalog-hero">
        <div class="container hero-content">
            <span class="eyebrow light-eyebrow">Satu tempat untuk semua momen</span>
            <h1>Acara hebat dimulai<br>bersama <em>partner yang tepat.</em></h1>
            <p>Jelajahi vendor terpercaya, lihat karya mereka, bandingkan layanan, dan temukan yang paling cocok untuk acaramu.</p>
            <form class="search-box" action="{{ route('vendors.index') }}" method="GET">
                <label class="search-field"><span>⌕</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari vendor atau layanan"></label>
                <label class="search-field location-field"><span>⌖</span><input type="search" name="lokasi" value="{{ $filters['lokasi'] ?? '' }}" placeholder="Kota atau lokasi"></label>
                <button class="button button-dark" type="submit">Cari Vendor <span>→</span></button>
            </form>
        </div>
        <div class="hero-orb orb-one"></div><div class="hero-orb orb-two"></div>
    </section>

    <section class="container catalog-section">
        <div class="catalog-heading">
            <div><span class="section-kicker">Katalog pilihan</span><h2>Vendor untuk acaramu</h2><p>{{ $vendors->total() }} vendor sesuai pencarian</p></div>
            <label class="sort-select">Urutkan:
                <select onchange="window.location.href=this.value">
                    <option value="{{ route('vendors.index', array_merge(request()->except('page'), ['urut' => 'relevan'])) }}">Paling relevan</option>
                    <option value="{{ route('vendors.index', array_merge(request()->except('page'), ['urut' => 'harga'])) }}" @selected(($filters['urut'] ?? '') === 'harga')>Harga terendah</option>
                </select>
            </label>
        </div>

        <div class="catalog-browser">
            <form class="catalog-sidebar" action="{{ route('vendors.index') }}" method="GET">
                <div class="catalog-filter-head"><span>Filter pencarian</span><a href="{{ route('vendors.index') }}">Reset</a></div>
                <label>Kata kunci<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari vendor..."></label>
                <label>Kategori layanan<select name="kategori"><option value="">Semua kategori</option>@foreach($kategori as $item)<option value="{{ $item->id_kategori }}" @selected((string) ($filters['kategori'] ?? '') === (string) $item->id_kategori)>{{ $item->nama_kategori }}</option>@endforeach</select></label>
                <label>Lokasi<input type="search" name="lokasi" value="{{ $filters['lokasi'] ?? '' }}" placeholder="Semua lokasi"></label>
                <div class="catalog-price-range">
                    <span>Kisaran harga (Rp)</span>
                    <div class="price-range-inputs">
                        <input type="number" name="harga_min" min="0" step="1000" value="{{ $filters['harga_min'] ?? '' }}" placeholder="Min" aria-label="Harga minimum">
                        <input type="number" name="harga_max" min="0" step="1000" value="{{ $filters['harga_max'] ?? '' }}" placeholder="Max" aria-label="Harga maksimum">
                    </div>
                </div>
                @if(!empty($filters['urut']))<input type="hidden" name="urut" value="{{ $filters['urut'] }}">@endif
                <button class="button button-dark full-button" type="submit">Terapkan filter</button>
            </form>
            <div class="catalog-grid-area">
                @if($vendors->isEmpty())
                    <div class="empty-state"><div>✧</div><h3>Belum ada vendor yang cocok</h3><p>Coba ubah kata kunci atau filter pencarianmu.</p><a class="button button-outline" href="{{ route('vendors.index') }}">Hapus Filter</a></div>
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
        </div>
    </section>

    <section class="container safe-banner">
        <div class="safe-icon">✓</div><div><strong>Informasi vendor yang ditinjau</strong><p>Profil yang tampil di katalog telah melalui proses verifikasi KolaboraEO.</p></div>
        <a href="{{ route('register') }}">Bergabung dengan KolaboraEO <span>→</span></a>
    </section>
@endsection
