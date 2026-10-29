{{-- Kerangka portal: $items, $label, $homeRoute, $logoutRoute, $nama, $inisial, $topLink (opsional) --}}
<aside class="portal-sidebar">
    <a class="brand portal-brand portal-brand-image" href="{{ route($homeRoute) }}" aria-label="KolaboraEO"><img src="{{ asset('images/kolaboraeo-logo-white.png') }}" alt="KolaboraEO — Events, Connections, Experiences"></a>
    <div class="portal-label">{{ $label }}</div>
    <nav class="portal-nav">
        @foreach($items as $item)
            <a href="{{ route($item['name']) }}" class="{{ request()->routeIs($item['match'] ?? $item['name']) ? 'active' : '' }}"><span class="nav-icon"><x-icon :name="$item['icon']" /></span>{{ $item['label'] }}</a>
        @endforeach
    </nav>
    <form class="portal-logout" method="POST" action="{{ route($logoutRoute) }}">@csrf<button type="submit"><span class="nav-icon"><x-icon name="logout" /></span>Keluar</button></form>
</aside>
<div class="portal-main">
    <header class="portal-topbar">
        <div><small>Selamat datang kembali,</small><strong>{{ $nama }}</strong></div>
        <div class="topbar-actions">
            @if(!empty($topLink))
                <a class="calendar-square" href="{{ $topLink['href'] }}" title="{{ $topLink['title'] }}" aria-label="{{ $topLink['title'] }}"><x-icon :name="$topLink['icon']" />@if(!empty($topLink['badge']))<span class="nav-badge">{{ $topLink['badge'] }}</span>@endif</a>
            @else
                <span class="calendar-square"><x-icon name="calendar" /></span>
            @endif
            <span class="avatar">{{ $inisial }}</span>
        </div>
    </header>
    @include('partials.flash')
    <main class="portal-content">@yield('portal-content')</main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
