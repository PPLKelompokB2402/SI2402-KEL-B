@extends('layouts.portal')

@section('title', 'Profil bisnis')

@section('portal-content')
    <section class="portal-page">
        <div class="portal-heading">
            <div><span class="section-kicker">Informasi bisnis</span><h1>Profil bisnis</h1><p>Informasi ini akan tampil di halaman publik vendor Anda.</p></div>
            @if($vendors->count() > 1)
                <form method="GET" action="{{ route('vendor.profil') }}" class="admin-search">
                    <select name="vendor" onchange="this.form.submit()" aria-label="Pilih usaha" style="height:42px;padding:0 12px;border:1px solid #e1e9f0;border-radius:9px;background:#fff">
                        @foreach($vendors as $item)<option value="{{ $item->id_vendor }}" @selected($item->id_vendor === $vendor->id_vendor)>{{ $item->nama_usaha }}</option>@endforeach
                    </select>
                </form>
            @endif
        </div>
        @if($vendor->status_verifikasi === 'terverifikasi')
            <div class="inline-note">Menyimpan perubahan akan mengajukan profil untuk verifikasi ulang.</div>
        @endif
        <section class="portal-panel profile-form-panel">
            <form method="POST" action="{{ route('vendor.usaha.update', $vendor) }}" class="profile-form">@csrf @method('PUT')
                @include('vendor.partials.form-profil', ['item' => $vendor, 'span' => 'profile-span'])
                <div class="profile-span profile-submit"><button class="button button-dark" type="submit">Simpan perubahan</button></div>
            </form>
        </section>
        <section class="portal-panel legal-panel">
            <div class="panel-heading"><div><h2>Legalitas</h2><p>Dokumen digunakan untuk verifikasi dan tidak tampil ke publik.</p></div><span class="status-tag {{ $vendor->status_verifikasi }}">{{ $vendor->status_label }}</span></div>
            <div class="legal-card-grid two">
                @foreach([['KTP Penanggung Jawab', ['KTP']], ['NIB / SIUP', ['NIB', 'SIUP']]] as [$judul, $jenis])
                    @php($dok = $vendor->dokumenLegalitas->whereIn('jenis_dokumen', $jenis)->first())
                    <article>
                        <span>▤</span>
                        <strong>{{ $judul }}</strong>
                        @if($dok)
                            <span class="status-tag {{ $dok->status_verifikasi }}">{{ $dok->status_label }}</span>
                            <small>{{ $dok->jenis_dokumen }} · No. {{ $dok->no_dokumen }}</small>
                            @if($dok->catatan_admin)<small class="admin-note">Catatan admin: {{ $dok->catatan_admin }}</small>@endif
                            <div class="row-actions"><a class="text-link" href="{{ route('media.dokumen', $dok) }}" target="_blank" rel="noopener">Lihat berkas</a></div>
                        @else
                            <small>Dokumen legalitas belum diunggah</small>
                        @endif
                        <details @if($errors->any() && in_array(old('jenis_dokumen'), $jenis, true)) open @endif>
                            <summary class="text-link">{{ $dok ? 'Perbarui dokumen' : 'Unggah dokumen' }}</summary>
                            <form method="POST" action="{{ route('vendor.legalitas.unggah', $vendor) }}" enctype="multipart/form-data" class="stack-form">@csrf
                                @if(count($jenis) > 1)
                                    <label>Jenis dokumen<select name="jenis_dokumen" required>@foreach($jenis as $j)<option value="{{ $j }}" @selected($dok?->jenis_dokumen === $j)>{{ $j }}</option>@endforeach</select></label>
                                @else
                                    <input type="hidden" name="jenis_dokumen" value="{{ $jenis[0] }}">
                                @endif
                                <label>Nomor dokumen<input name="no_dokumen" value="{{ $dok?->no_dokumen }}" required maxlength="50"></label>
                                <label>Berkas (PDF/JPG/PNG)<input type="file" name="berkas" required accept=".pdf,.jpg,.jpeg,.png"></label>
                                <button class="button button-dark" type="submit">Unggah</button>
                            </form>
                        </details>
                    </article>
                @endforeach
            </div>
        </section>
    </section>
@endsection
