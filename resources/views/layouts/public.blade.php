<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dermavera membantu pria membandingkan facial wash melalui safety gate, bukti formula, dan pemeringkatan SAW yang transparan.">
    <meta name="theme-color" content="#14213D">
    <title>{{ $title ?? 'Dermavera' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @livewireStyles
</head>
<body>
<a href="#main" class="skip-link">Lewati ke konten utama</a>
<header class="site-header" data-motion-root="navbar">
    <nav class="shell nav" aria-label="Navigasi utama" x-data="{ open: false }" @keydown.escape.window="open = false">
        <a href="{{ route('home') }}" wire:navigate class="brand" aria-label="Dermavera — beranda">
            <img class="brand-logo" src="{{ asset('images/branding/ChatGPT Image Sep 24, 2026, 05_31_24 PM.png') }}" alt="Dermavera" width="2172" height="724">
        </a>
        <button class="mobile-menu" type="button" @click="open = !open" :aria-expanded="open" aria-controls="primary-menu"><span class="sr-only">Buka menu</span><span></span><span></span></button>
        <div id="primary-menu" class="nav-links" :class="open ? 'is-open' : ''" @click="open = false">
            <a href="{{ route('home') }}" wire:navigate @class(['active' => request()->routeIs('home')])>Beranda</a>
            <a href="{{ route('catalog') }}" wire:navigate @class(['active' => request()->routeIs('catalog', 'products.*')])>Katalog</a>
            <a href="{{ route('method') }}" wire:navigate @class(['active' => request()->routeIs('method')])>Metode</a>
            <a href="{{ route('about') }}" wire:navigate @class(['active' => request()->routeIs('about')])>Penelitian</a>
            @auth
                <a href="{{ route('history') }}" wire:navigate @class(['active' => request()->routeIs('history')])>Riwayat</a>
                <form method="POST" action="{{ route('logout') }}" class="nav-logout">@csrf<button type="submit">Keluar</button></form>
            @else
                <a href="{{ route('login') }}" wire:navigate class="nav-login">Masuk</a>
                <a href="{{ route('register') }}" wire:navigate class="button button-secondary nav-register">Daftar</a>
            @endauth
            <a href="{{ route('consultation.consent') }}" wire:navigate class="button button-primary nav-cta">Mulai konsultasi <span aria-hidden="true">↗</span></a>
        </div>
    </nav>
</header>
<main id="main">{{ $slot }}</main>
<footer class="footer">
    <div class="shell footer-main">
        <div class="footer-intro"><a href="{{ route('home') }}" class="brand brand-light" aria-label="Dermavera — beranda"><img class="brand-logo brand-logo-footer" src="{{ asset('images/branding/ChatGPT Image Sep 24, 2026, 05_31_24 PM.png') }}" alt="Dermavera" width="2172" height="724"></a><p>Keputusan facial wash yang lebih tenang, transparan, dan dapat ditelusuri.</p></div>
        <div><strong>Jelajahi</strong><a href="{{ route('catalog') }}">Katalog produk</a><a href="{{ route('method') }}">Metodologi</a><a href="{{ route('about') }}">Tentang penelitian</a></div>
        <div><strong>Akses</strong>@auth<a href="{{ route('history') }}">Riwayat saya</a>@else<a href="{{ route('login') }}">Masuk</a><a href="{{ route('register') }}">Daftar akun</a>@endauth<a href="{{ route('consultation.consent') }}">Mulai konsultasi</a><a href="/admin">Panel admin</a></div>
    </div>
    <div class="shell footer-bottom"><span>© {{ date('Y') }} Dermavera</span><p>Bukan alat diagnosis dan bukan pengganti dokter.</p></div>
</footer>
@livewireScripts
@fluxScripts
</body>
</html>
