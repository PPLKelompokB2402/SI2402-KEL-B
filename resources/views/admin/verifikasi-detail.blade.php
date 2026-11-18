@extends('layouts.admin')

@section('title', 'Tinjau '.$vendor->nama_usaha)

@section('portal-content')
    <section class="portal-page">
        <div class="admin-page-heading">
            <div><span class="section-kicker"><a href="{{ route('admin.verifikasi') }}">← Verifikasi vendor</a></span><h1>{{ $vendor->nama_usaha }}</h1><p>{{ $vendor->kategori->nama_kategori }} · {{ $vendor->lokasi_layanan }} · {{ $vendor->user->nama }} ({{ $vendor->user->email }})</p></div>
            <span class="status-tag {{ $vendor->status_verifikasi }}">{{ $vendor->status_label }}</span>
        </div>
        <section class="portal-panel">
            <h2>Profil usaha</h2>
            <dl class="detail-list">
                <dt>Deskripsi</dt><dd>{!! nl2br(e($vendor->deskripsi_usaha)) !!}</dd>
                <dt>Kisaran harga</dt><dd>Mulai {{ $vendor->harga_format }}</dd>
                <dt>WhatsApp</dt><dd>{{ $vendor->user->no_hp ?? '—' }}</dd>
                <dt>Portofolio</dt><dd>{{ $vendor->portofolio->count() }} item</dd>
            </dl>
            @if($vendor->portofolio->isNotEmpty())
                <div class="thumb-row">@foreach($vendor->portofolio->take(4) as $item)<a href="{{ route('media.portofolio', $item) }}" target="_blank" rel="noopener" style="--cover: url('{{ route('media.portofolio', $item) }}')" title="{{ $item->judul_proyek }}"></a>@endforeach</div>
            @endif
        </section>
        <section class="portal-panel admin-list-panel doc-review">
            <div class="panel-heading"><div><h2>Dokumen legalitas</h2><p>Periksa setiap dokumen dan berikan catatan bila ditolak.</p></div></div>
            <div class="doc-review-grid">
                @foreach($jenisDokumen as $jenis)
                    @php($dok = $vendor->dokumenLegalitas->firstWhere('jenis_dokumen', $jenis))
                    <article class="doc-card">
                        <div class="doc-card-head"><strong>{{ $jenis }}</strong>@if($dok)<span class="status-tag {{ $dok->status_verifikasi }}">{{ $dok->status_label }}</span>@endif</div>
                        @if(!$dok)
                            <p class="muted">Belum diunggah vendor.</p>
                        @else
                            <p>No. dokumen: <strong>{{ $dok->no_dokumen }}</strong></p>
                            <a class="button button-outline full-button" href="{{ route('media.dokumen', $dok) }}" target="_blank" rel="noopener">Buka berkas</a>
                            <form method="POST" action="{{ route('admin.dokumen.periksa', $dok) }}" class="stack-form">@csrf @method('PATCH')
                                <label>Catatan admin<textarea name="catatan_admin" rows="2" maxlength="500" placeholder="Wajib diisi bila ditolak">{{ $dok->catatan_admin }}</textarea></label>
                                <div class="btn-pair">
                                    <button class="reject-button" type="submit" name="status_verifikasi" value="ditolak">Tolak</button>
                                    <button class="button button-dark" type="submit" name="status_verifikasi" value="valid">Valid</button>
                                </div>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    </section>
@endsection
