@extends('layouts.app')
@section('title', $vendor->nama_usaha.' — Portofolio')
@section('content')
    <div class="container detail-page">
        @include('customer.partials.detail-header')
        <div class="detail-content">
            <section class="detail-panel detail-section">
                <div class="panel-heading">
                    <div><span class="section-kicker">Karya pilihan</span><h2>Galeri portofolio</h2></div>
                    <span>{{ $vendor->portofolio->count() }} karya</span>
                </div>
                @if($vendor->portofolio->isEmpty())
                    <p class="muted">Portofolio vendor belum ditambahkan.</p>
                @else
                    <div class="portfolio-grid">
                        @foreach($vendor->portofolio as $item)
                            <article class="portfolio-item">
                                <a href="{{ route('media.portofolio', $item) }}" target="_blank" rel="noopener"><div class="portfolio-image" style="--cover: url('{{ route('media.portofolio', $item) }}')"></div></a>
                                <strong>{{ $item->judul_proyek }}</strong>
                                @if($item->deskripsi_proyek)<p>{{ $item->deskripsi_proyek }}</p>@endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
