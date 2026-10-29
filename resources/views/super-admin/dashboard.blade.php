@extends('layouts.super-admin')

@section('title', 'Super Admin')

@section('portal-content')
    <section class="portal-page admin-dashboard-page">
        <div class="admin-page-heading">
            <div><span class="section-kicker">Super administrator</span><h1>Halo, {{ auth()->user()->nama }}</h1><p>Anda masuk sebagai super admin.</p></div>
        </div>
    </section>
@endsection
