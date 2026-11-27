<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', 'Super Admin') — KolaboraEO</title>
</head>
<body class="portal-body super-admin-portal-body">
    @php($portalUser = auth('super_admin')->user())
    @include('partials.portal-shell', [
        'label' => 'SUPER ADMIN',
        'homeRoute' => 'super-admin.dashboard',
        'logoutRoute' => 'super-admin.logout',
        'nama' => $portalUser->nama.' · Super Admin',
        'inisial' => $portalUser->inisial,
        'items' => [
            ['name' => 'super-admin.dashboard', 'label' => 'Ringkasan', 'icon' => 'grid'],
            ['name' => 'super-admin.kategori', 'label' => 'Kategori', 'icon' => 'briefcase'],
            ['name' => 'super-admin.administrator', 'match' => 'super-admin.administrator*', 'label' => 'Administrator', 'icon' => 'users'],
        ],
    ])
</body>
</html>
