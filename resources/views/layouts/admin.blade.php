<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'Dashboard') — FitPass</title>
    <meta name="description" content="FitPass Dakar — interface d'administration">
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
<body style="background-color: var(--color-bg); color: var(--color-text);"
      x-data="{ sidebarOpen: false }"
      @keydown.escape="sidebarOpen = false">

    {{-- Overlay (mobile) --}}
    <div x-show="sidebarOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="drawer-overlay"
         @click="sidebarOpen = false">
    </div>

    <div class="admin-wrapper">

        {{-- Sidebar --}}
        <aside class="admin-sidebar" :class="{ 'open': sidebarOpen }">

            <div style="margin-bottom: 2rem; padding: 0 0.5rem;">
                <span style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em;">FitPass</span>
                <div style="font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-top: 0.125rem;">Admin</div>
            </div>

            <nav style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1;">
                <a href="{{ route('admin.dashboard') }}"
                   class="{{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'nav-link' }}"
                   @click="sidebarOpen = false">
                    Dashboard
                </a>
                <a href="{{ route('admin.members') }}"
                   class="{{ request()->routeIs('admin.members') ? 'nav-link-active' : 'nav-link' }}"
                   @click="sidebarOpen = false">
                    Membres
                </a>
                <a href="{{ route('admin.gyms') }}"
                   class="{{ request()->routeIs('admin.gyms*') ? 'nav-link-active' : 'nav-link' }}"
                   @click="sidebarOpen = false">
                    Salles
                </a>
                <a href="{{ route('admin.payments') }}"
                   class="{{ request()->routeIs('admin.payments') ? 'nav-link-active' : 'nav-link' }}"
                   @click="sidebarOpen = false">
                    Paiements
                </a>
            </nav>

            <div style="margin-top: auto; padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 0.5rem; padding: 0 0.5rem;">{{ auth()->user()->name }}</div>
                <a href="{{ route('logout') }}" class="nav-link" style="display: block;">Déconnexion</a>
            </div>
        </aside>

        {{-- Main --}}
        <div class="admin-main">

            {{-- Topbar --}}
            <header class="admin-topbar">
                <h1 class="admin-topbar-title" style="min-width: 0;">@yield('title', 'Dashboard')</h1>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ now()->format('d/m/Y') }}</div>
                    <button class="hamburger-btn"
                            @click="sidebarOpen = !sidebarOpen"
                            :aria-expanded="sidebarOpen.toString()"
                            aria-label="Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </header>

            {{-- Flash --}}
            @if(session('success'))
                <div style="padding: 1rem 1.5rem 0;">
                    <div class="alert-success">{{ session('success') }}</div>
                </div>
            @endif
            @if(session('error'))
                <div style="padding: 1rem 1.5rem 0;">
                    <div class="alert-error">{{ session('error') }}</div>
                </div>
            @endif

            <main style="padding: 1.5rem;">
                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
</body>
</html>
