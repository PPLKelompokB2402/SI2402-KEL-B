<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Admin Console') — KolaboraEO</title>
</head>
<body class="portal-body admin-portal-body">
    @php($portalUser = auth('admin')->user())
    @include('partials.portal-shell', [
        'label' => 'ADMINISTRATOR',
        'homeRoute' => 'admin.dashboard',
        'logoutRoute' => 'admin.logout',
        'nama' => $portalUser->nama,
        'inisial' => $portalUser->inisial,
        'items' => [
            ['name' => 'admin.dashboard', 'label' => 'Ringkasan', 'icon' => 'grid'],
            ['name' => 'admin.verifikasi', 'match' => 'admin.verifikasi*', 'label' => 'Verifikasi vendor', 'icon' => 'shield'],
            ['name' => 'admin.kategori', 'label' => 'Kategori', 'icon' => 'briefcase'],
        ],
    ])
</body>
</html>
