<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.head')
    <meta name="description" content="KolaboraEO membantu menemukan dan membandingkan vendor acara terpercaya.">
    <title>@yield('title', 'KolaboraEO — Vendor acara terpercaya')</title>
</head>
<body>
    @php
        $webUser = auth('web')->user();
    @endphp
    <header class="site-header">
        <div class="container nav-row">
            <a class="brand brand-image" href="{{ route('vendors.index') }}" aria-label="KolaboraEO — Beranda"><img src="{{ asset('images/kolaboraeo-logo.png') }}" alt="KolaboraEO — Events, Connections, Experiences"></a>
            <button class="menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false">☰</button>
            <nav class="main-nav">
                <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.index', 'vendors.show', 'vendors.portofolio', 'vendors.harga', 'vendors.ulasan') ? 'active' : '' }}">Temukan Vendor</a>
                @if($webUser?->role === 'vendor')
                    <a href="{{ route('vendor.dashboard') }}">Dashboard Vendor</a>
                @endif
            </nav>
            <div class="nav-actions">
                @if($webUser)
                    <span class="user-chip">{{ $webUser->nama }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="text-button" type="submit">Keluar</button></form>
                @else
                    <a class="login-link" href="{{ route('login') }}">Masuk</a>
                    <a class="button button-sm button-dark" href="{{ route('register') }}">Daftar</a>
                @endif
            </div>
        </div>
    </header>

    @include('partials.flash', ['wrap' => 'container'])

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <a class="brand brand-footer brand-image" href="{{ route('vendors.index') }}" aria-label="KolaboraEO — Beranda"><img src="{{ asset('images/kolaboraeo-logo-white.png') }}" alt="KolaboraEO — Events, Connections, Experiences"></a>
                    <p>Temukan partner terbaik untuk momen yang berarti. KolaboraEO menghubungkanmu dengan vendor acara terpercaya.</p>
                </div>
                <nav class="footer-links" aria-label="Jelajahi">
                    <h2>Jelajahi</h2>
                    <a href="{{ route('vendors.index') }}">Cari vendor</a>
                    <a href="{{ route('register') }}">Daftar sebagai vendor</a>
                    <a href="{{ route('login') }}">Portal vendor</a>
                </nav>
                <section class="footer-trust">
                    <h2>Vendor Terverifikasi</h2>
                    <p>Periksa profil vendor yang telah ditinjau, bandingkan layanan, dan baca ulasan untuk membuat keputusan dengan lebih yakin. Laporkan profil, portofolio, atau ulasan yang mencurigakan agar segera ditindaklanjuti.</p>
                </section>
            </div>
            <div class="footer-bottom"><span>© {{ date('Y') }} KolaboraEO. Semua hak dilindungi.</span></div>
        </div>
    </footer>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
