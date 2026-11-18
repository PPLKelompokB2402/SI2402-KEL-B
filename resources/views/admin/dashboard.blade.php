@extends('layouts.admin')

@section('title', 'Admin console')

@section('portal-content')
    <section class="portal-page admin-dashboard-page">
        <div class="admin-page-heading">
            <div><span class="section-kicker">Admin console</span><h1>Halo, {{ auth()->user()->nama }}</h1><p>Pilih menu untuk mulai mengelola KolaboraEO.</p></div>
        </div>
        <div class="admin-shortcuts">
            <a href="{{ route('admin.verifikasi') }}"><span><x-icon name="shield" /></span><strong>Verifikasi vendor</strong><small>Tinjau dokumen legalitas</small></a>
            <a href="{{ route('admin.kategori') }}"><span><x-icon name="briefcase" /></span><strong>Kategori layanan</strong><small>Kelola kategori katalog</small></a>
        </div>
    </section>
@endsection
