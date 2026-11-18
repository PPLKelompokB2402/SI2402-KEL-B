<div class="breadcrumbs"><a href="{{ route('vendors.index') }}">Vendor</a><span>/</span><span>{{ $vendor->nama_usaha }}</span></div>
@php($gambar = $vendor->portofolio->map(fn ($item) => route('media.portofolio', $item))->values())
<div class="detail-gallery">
    <div class="gallery-main" @if($gambar->get(0)) style="--cover: url('{{ $gambar->get(0) }}')" @endif></div>
    <div class="gallery-side" @if($gambar->get(1, $gambar->get(0))) style="--cover: url('{{ $gambar->get(1, $gambar->get(0)) }}')" @endif></div>
    <div class="gallery-side last" @if($gambar->get(2, $gambar->get(0))) style="--cover: url('{{ $gambar->get(2, $gambar->get(0)) }}')" @endif></div>
</div>
<div class="detail-overview-layout">
    <div class="detail-overview-main">
        <div class="detail-labels"><span>{{ $vendor->kategori->nama_kategori }}</span><span>✓ Profil terverifikasi</span></div>
        <div class="detail-header">
            <div>
                <div class="detail-title-line"><h1>{{ $vendor->nama_usaha }}</h1></div>
                <p>⌖ {{ $vendor->lokasi_layanan }}</p>
            </div>
            <div class="detail-quick-actions">
            </div>
        </div>
        <nav class="detail-tabs" aria-label="Detail vendor">
            <a class="{{ $activeTab === 'show' ? 'active' : '' }}" href="{{ route('vendors.show', $vendor) }}">Ringkasan Layanan</a>
            <a class="{{ $activeTab === 'portofolio' ? 'active' : '' }}" href="{{ route('vendors.portofolio', $vendor) }}">Galeri Portofolio</a>
            <a class="{{ $activeTab === 'harga' ? 'active' : '' }}" href="{{ route('vendors.harga', $vendor) }}">Harga & Detail</a>
        </nav>
    </div>
    <aside class="detail-price-card">
        <span class="section-kicker">Kisaran layanan</span>
        <strong>Mulai {{ $vendor->harga_format }}</strong>
        @if($vendor->kontak_url)
            <a class="button button-dark full-button" href="{{ $vendor->kontak_url }}" target="_blank" rel="noopener noreferrer">▤ &nbsp; Hubungi vendor</a>
        @else
            <p class="muted">Kontak vendor belum tersedia.</p>
        @endif
    </aside>
</div>
