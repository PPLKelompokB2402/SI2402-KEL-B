<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Super Admin') — KolaboraEO</title>
</head>
<body class="portal-body super-admin-portal-body">
    @php($portalUser = auth('web')->user())
    @include('partials.portal-shell', [
        'label' => 'SUPER ADMIN',
        'homeRoute' => 'super-admin.dashboard',
        'logoutRoute' => 'logout',
        'nama' => $portalUser->nama.' · Super Admin',
        'inisial' => $portalUser->inisial,
        'items' => [
            ['name' => 'super-admin.dashboard', 'label' => 'Ringkasan', 'icon' => 'grid'],
        ],
    ])
</body>
</html>
