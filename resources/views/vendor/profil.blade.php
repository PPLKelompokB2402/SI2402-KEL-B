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
        <div class="inline-note">Status verifikasi: <span class="status-tag {{ $vendor->status_verifikasi }}">{{ $vendor->status_label }}</span></div>
        @if($vendor->status_verifikasi === 'terverifikasi')
            <div class="inline-note">Menyimpan perubahan akan mengajukan profil untuk verifikasi ulang.</div>
        @endif
        <section class="portal-panel profile-form-panel">
            <form method="POST" action="{{ route('vendor.usaha.update', $vendor) }}" class="profile-form">@csrf @method('PUT')
                @include('vendor.partials.form-profil', ['item' => $vendor, 'span' => 'profile-span'])
                <div class="profile-span profile-submit"><button class="button button-dark" type="submit">Simpan perubahan</button></div>
            </form>
        </section>
    </section>
@endsection
