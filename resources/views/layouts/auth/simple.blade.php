<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-page min-h-screen antialiased">
        <div class="auth-orbit auth-orbit--one" aria-hidden="true"></div>
        <div class="auth-orbit auth-orbit--two" aria-hidden="true"></div>
        <main class="auth-shell">
            <a href="{{ route('home') }}" class="auth-brand" wire:navigate>
                <img
                    class="auth-brand-logo"
                    src="{{ asset('images/branding/ChatGPT Image Sep 24, 2026, 05_31_24 PM.png') }}"
                    alt="Dermavera"
                    width="2172"
                    height="724"
                >
                <span class="auth-brand-caption">SKINCARE DECISION SYSTEM</span>
            </a>

            <section class="auth-card">
                <div class="auth-card-sheen" aria-hidden="true"></div>
                <div class="auth-card-content">
                    {{ $slot }}
                </div>
            </section>

            <p class="auth-footnote"><span></span> Data kulitmu tetap berada di ruang yang aman.</p>
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
