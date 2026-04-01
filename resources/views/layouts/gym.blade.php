<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Salle — @yield('title', 'Dashboard') — FitPass</title>
    <meta name="description" content="@yield('description', 'FitPass — espace de gestion de votre salle partenaire')">
    <meta name="robots" content="noindex, nofollow">

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

    {{-- Overlay --}}
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
                <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-left: 0.5rem;">Salle</span>
            </div>
            <button @click="menuOpen = false"
                    style="background: none; border: none; color: var(--color-text-muted); cursor: pointer; padding: 0.25rem; font-size: 1.25rem; line-height: 1;">✕</button>
        </div>
        <nav class="drawer-nav">
            <a href="{{ route('gym.dashboard') }}"
               class="{{ request()->routeIs('gym.dashboard') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Dashboard</a>
            <a href="{{ route('gym.scan') }}"
               class="{{ request()->routeIs('gym.scan') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Scanner QR</a>
            <a href="{{ route('gym.checkins') }}"
               class="{{ request()->routeIs('gym.checkins') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Historique entrées</a>
            <a href="{{ route('gym.profil') }}"
               class="{{ request()->routeIs('gym.profil*') ? 'nav-link-active' : 'nav-link' }}"
               @click="menuOpen = false">Ma salle</a>
        </nav>
        <div class="drawer-footer">
            <div class="drawer-user">{{ auth()->user()->name }}</div>
            <a href="{{ route('logout') }}" class="nav-link" style="display: block;">Déconnexion</a>
        </div>
    </div>

    {{-- Navbar --}}
    <nav style="background-color: var(--color-bg-soft); border-bottom: 1px solid var(--color-border); position: sticky; top: 0; z-index: 20;">
        <div style="max-width: 768px; margin: 0 auto; padding: 0 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; height: 60px; gap: 0.75rem;">

                <a href="{{ route('gym.dashboard') }}" style="text-decoration: none; flex-shrink: 0;">
                    <span style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase;">FitPass</span>
                    <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-left: 0.4rem;">Salle</span>
                </a>

                {{-- Desktop links --}}
                <div class="desktop-nav-links" style="display: flex; gap: 0.25rem; align-items: center;">
                    <a href="{{ route('gym.scan') }}" class="{{ request()->routeIs('gym.scan') ? 'nav-link-active' : 'nav-link' }}">Scanner</a>
                    <a href="{{ route('gym.checkins') }}" class="{{ request()->routeIs('gym.checkins') ? 'nav-link-active' : 'nav-link' }}">Historique</a>
                    <a href="{{ route('gym.profil') }}" class="{{ request()->routeIs('gym.profil*') ? 'nav-link-active' : 'nav-link' }}">Ma salle</a>
                    <a href="{{ route('logout') }}" class="btn-ghost" style="padding: 0.375rem 0.75rem;">Sortir</a>
                </div>

                {{-- Mobile hamburger --}}
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

    @if(session('success'))
        <div style="max-width: 768px; margin: 1rem auto; padding: 0 1rem;">
            <div class="alert-success">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div style="max-width: 768px; margin: 1rem auto; padding: 0 1rem;">
            <div class="alert-error">{{ session('error') }}</div>
        </div>
    @endif

    <main style="max-width: 768px; margin: 0 auto; padding: 1.5rem 1rem;">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
