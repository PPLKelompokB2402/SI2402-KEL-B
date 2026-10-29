@extends('layouts.app')

@section('title', 'KolaboraEO — Vendor acara terpercaya')

@section('content')
    <section class="catalog-hero">
        <div class="container hero-content">
            <span class="eyebrow light-eyebrow">Satu tempat untuk semua momen</span>
            <h1>Acara hebat dimulai<br>bersama <em>partner yang tepat.</em></h1>
            @guest('web')
                <p><a class="button button-dark" href="{{ route('register') }}">Daftar sekarang <span>→</span></a></p>
            @endguest
        </div>
        <div class="hero-orb orb-one"></div><div class="hero-orb orb-two"></div>
    </section>
@endsection
