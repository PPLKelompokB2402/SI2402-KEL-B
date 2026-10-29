<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Portal Vendor') — KolaboraEO</title>
</head>
<body class="portal-body vendor-portal-body">
    @php
        $portalUser = auth('web')->user();
        $namaPortal = $portalUser->nama;
        $inisial = collect(explode(' ', $namaPortal))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
    @endphp
    @include('partials.portal-shell', [
        'label' => 'PORTAL VENDOR',
        'homeRoute' => 'vendor.dashboard',
        'logoutRoute' => 'logout',
        'nama' => $namaPortal,
        'inisial' => $inisial,
        'items' => [
            ['name' => 'vendor.dashboard', 'label' => 'Ringkasan', 'icon' => 'grid'],
        ],
    ])
</body>
</html>
