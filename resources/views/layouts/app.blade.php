<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FitPass') — FitPass Dakar</title>
    <meta name="description" content="@yield('description', '1 abonnement, toutes les salles de Dakar')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FF3B3B">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FitPass">
    <meta name="mobile-web-app-capable" content="yes">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh;"
      x-data="{ menuOpen: false }"
      @keydown.escape="menuOpen = false">

    {{-- Overlay drawer --}}
    <div x-show="menuOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="drawer-overlay"
         @click="menuOpen = false">
    </div>

    {{-- Mobile drawer --}}
    <div class="mobile-drawer" :class="{ 'open': menuOpen }" x-cloak>
        <div class="drawer-header">
            <div>
                <span style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase;">FitPass</span>
                <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-left: 0.5rem;">Dakar</span>
            </div>
            <button @click="menuOpen = false"
                    style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0.25rem; font-size: 1.25rem; line-height: 1;">✕</button>
        </div>
        <nav class="drawer-nav">
            <a href="{{ route('member.dashboard') }}"
               class="{{ request()->routeIs('member.dashboard') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Dashboard</a>
            <a href="{{ route('member.gyms') }}"
               class="{{ request()->routeIs('member.gyms*') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Salles partenaires</a>
            <a href="{{ route('member.qrcode') }}"
               class="{{ request()->routeIs('member.qrcode') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Mon QR Code</a>
            <a href="{{ route('member.subscriptions') }}"
               class="{{ request()->routeIs('member.subscriptions') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Abonnement</a>
            <a href="{{ route('member.checkins') }}"
               class="{{ request()->routeIs('member.checkins') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Historique</a>
        </nav>
        <div class="drawer-footer">
            <div class="drawer-user">{{ auth()->user()->name }}</div>
            <a href="{{ route('logout') }}" class="nav-link" style="display: block;">Déconnexion</a>
        </div>
    </div>

    {{-- Navbar --}}
    <nav style="background-color: var(--color-bg-soft); border-bottom: 1px solid var(--color-border); position: sticky; top: 0; z-index: 20;">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; height: 60px; gap: 1rem;">

                <a href="{{ route('member.dashboard') }}" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; flex-shrink: 0;">
                    <span style="font-family: var(--font-heading); font-size: 1.375rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em;">FitPass</span>
                    <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted);">Dakar</span>
                </a>

                {{-- Desktop nav --}}
                <div class="desktop-nav-links" style="display: flex; align-items: center; gap: 0.25rem; flex: 1; justify-content: center;">
                    <a href="{{ route('member.dashboard') }}" class="{{ request()->routeIs('member.dashboard') ? 'nav-link-active' : 'nav-link' }}">Dashboard</a>
                    <a href="{{ route('member.gyms') }}" class="{{ request()->routeIs('member.gyms*') ? 'nav-link-active' : 'nav-link' }}">Salles</a>
                    <a href="{{ route('member.qrcode') }}" class="{{ request()->routeIs('member.qrcode') ? 'nav-link-active' : 'nav-link' }}">QR Code</a>
                    <a href="{{ route('member.subscriptions') }}" class="{{ request()->routeIs('member.subscriptions') ? 'nav-link-active' : 'nav-link' }}">Abonnement</a>
                </div>

                {{-- Desktop : user + logout --}}
                <div class="desktop-nav-links" style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                    <span style="font-size: 0.8rem; color: var(--color-text-muted);">{{ auth()->user()->name }}</span>
                    <a href="{{ route('logout') }}" class="btn-ghost" style="padding: 0.375rem 0.75rem;">Déconnexion</a>
                </div>

                {{-- Mobile : hamburger --}}
                <button class="hamburger-btn"
                        @click="menuOpen = !menuOpen"
                        :aria-expanded="menuOpen.toString()"
                        aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div style="max-width: 1280px; margin: 1rem auto; padding: 0 1rem;">
            <div class="alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="max-width: 1280px; margin: 1rem auto; padding: 0 1rem;">
            <div class="alert-error">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Contenu --}}
    <main class="main-with-bottom-nav" style="max-width: 1280px; margin: 0 auto; padding: 1.5rem 1rem;">
        @yield('content')
    </main>

    {{-- Footer minimal (desktop only) --}}
    <footer class="desktop-nav-links" style="border-top: 1px solid var(--color-border); padding: 1.5rem 1rem; margin-top: 2rem;">
        <div style="max-width: 1280px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-family: var(--font-heading); font-size: 1rem; color: var(--color-primary); text-transform: uppercase;">FitPass Dakar</span>
            <span style="font-size: 0.75rem; color: var(--color-text-muted);">1 abonnement, toutes les salles de Dakar</span>
        </div>
    </footer>

    {{-- Bottom navigation (mobile) --}}
    <nav class="bottom-nav">
        <div class="bottom-nav-inner">
            <a href="{{ route('member.dashboard') }}"
               class="bottom-nav-item {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>
            <a href="{{ route('member.gyms') }}"
               class="bottom-nav-item {{ request()->routeIs('member.gyms*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Salles
            </a>
            <a href="{{ route('member.qrcode') }}"
               class="bottom-nav-item {{ request()->routeIs('member.qrcode') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                QR Code
            </a>
            <a href="{{ route('member.subscriptions') }}"
               class="bottom-nav-item {{ request()->routeIs('member.subscriptions') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                Abonnement
            </a>
            <button class="bottom-nav-item" @click="menuOpen = true" style="background: none; border: none; cursor: pointer;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Menu
            </button>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
