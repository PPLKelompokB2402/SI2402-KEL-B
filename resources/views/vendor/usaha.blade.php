@extends('layouts.portal')

@section('title', 'Usaha saya')

@section('portal-content')
    <section class="portal-page">
        <div class="portal-heading"><div><span class="section-kicker">Manajemen multi-vendor</span><h1>Usaha saya</h1><p>Satu akun dapat mengelola beberapa profil usaha secara terpisah.</p></div><a class="button button-dark" href="#tambah-usaha">＋ &nbsp; Tambah usaha</a></div>
        <div class="business-grid">
            @foreach($vendors as $vendor)
                <article class="business-card">
                    <div class="business-cover" @if($vendor->sampul_url) style="--cover: url('{{ $vendor->sampul_url }}')" @endif></div>
                    <div class="business-card-body">
                        <div class="business-badges"><span class="status-tag {{ $vendor->status_verifikasi }}">{{ $vendor->status_label }}</span></div>
                        <h2>{{ $vendor->nama_usaha }}</h2><p>{{ $vendor->kategori->nama_kategori }} · {{ $vendor->lokasi_layanan }}</p>
                        <a class="button button-outline full-button" href="{{ route('vendor.profil', ['vendor' => $vendor->id_vendor]) }}">{{ $vendor->status_verifikasi === 'terverifikasi' ? 'Kelola usaha' : 'Lengkapi & ajukan' }}</a>
                    </div>
                </article>
            @endforeach
            <article class="business-card add-business-card" id="tambah-usaha">
                <div class="business-card-body">
                    <span class="section-kicker">Profil usaha baru</span><h2>Tambahkan usaha</h2><p>Daftarkan profil terpisah untuk layanan atau brand lainnya.</p>
                    <form method="POST" action="{{ route('vendor.usaha.simpan') }}" class="stack-form">@csrf
                        @include('vendor.partials.form-profil', ['item' => null, 'rows' => 3])
                        <button class="button button-dark" type="submit">Ajukan verifikasi</button>
                    </form>
                </div>
            </article>
        </div>
    </section>
@endsection
