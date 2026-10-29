@extends('layouts.admin')

@section('title', 'Admin console')

@section('portal-content')
    <section class="portal-page admin-dashboard-page">
        <div class="admin-page-heading">
            <div><span class="section-kicker">Admin console</span><h1>Halo, {{ auth()->user()->nama }}</h1><p>Anda masuk sebagai administrator.</p></div>
        </div>
    </section>
@endsection
