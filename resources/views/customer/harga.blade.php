@extends('layouts.app')
@section('title', $vendor->nama_usaha.' — Harga & detail layanan')
@section('content')
    <div class="container detail-page">
        @include('customer.partials.detail-header')
        <div class="detail-content">
            <section class="detail-panel detail-section">
                <div class="panel-heading"><div><span class="section-kicker">Panduan harga</span><h2>Harga & detail layanan</h2></div></div>
                <div class="price-guide">
                    <span class="section-kicker">Perkiraan biaya</span>
                    <h3>Mulai {{ $vendor->harga_format }}</h3>
                    <p>Harga akhir dan cakupan layanan dapat dikonfirmasi langsung kepada vendor.</p>
                    <div class="price-tier"><div><strong>Kisaran harga</strong><br><span>{{ $vendor->nama_usaha }}</span></div><strong>Mulai {{ $vendor->harga_format }}</strong></div>
                </div>
            </section>
        </div>
    </div>
@endsection
