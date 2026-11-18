@extends('layouts.app')
@section('title', $vendor->nama_usaha.' — Ringkasan layanan')
@section('content')
    <div class="container detail-page">
        @include('customer.partials.detail-header')
        <div class="detail-content">
            <section class="detail-panel detail-section">
                <span class="section-kicker">Tentang {{ \Illuminate\Support\Str::before($vendor->nama_usaha, ' ') }}</span>
                <h2>Tentang {{ \Illuminate\Support\Str::before($vendor->nama_usaha, ' ') }}</h2>
                <p class="long-copy">{!! nl2br(e($vendor->deskripsi_usaha)) !!}</p>
                <h3 class="subsection-title">Wilayah layanan</h3>
                <p class="long-copy">{{ $vendor->lokasi_layanan }}</p>
            </section>
        </div>
    </div>
@endsection
