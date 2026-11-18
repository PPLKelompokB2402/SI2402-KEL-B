@extends('layouts.super-admin')

@section('title', 'Super Admin')

@section('portal-content')
    <section class="portal-page admin-dashboard-page">
        <div class="admin-page-heading">
            <div><span class="section-kicker">Super administrator</span><h1>Halo, {{ auth()->user()->nama }}</h1><p>Pilih menu untuk mulai mengelola KolaboraEO.</p></div>
        </div>
        <div class="admin-shortcuts">
            <a href="{{ route('super-admin.kategori') }}"><span><x-icon name="briefcase" /></span><strong>Kategori layanan</strong><small>Kelola kategori katalog</small></a>
        </div>
    </section>
@endsection
