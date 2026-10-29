<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Admin Console') — KolaboraEO</title>
</head>
<body class="portal-body admin-portal-body">
    @php($portalUser = auth('web')->user())
    @include('partials.portal-shell', [
        'label' => 'ADMINISTRATOR',
        'homeRoute' => 'admin.dashboard',
        'logoutRoute' => 'logout',
        'nama' => $portalUser->nama,
        'inisial' => $portalUser->inisial,
        'items' => [
            ['name' => 'admin.dashboard', 'label' => 'Ringkasan', 'icon' => 'grid'],
        ],
    ])
</body>
</html>
