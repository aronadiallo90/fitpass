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
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh; display: flex;">

    <!-- Sidebar admin -->
    <aside style="width: 240px; min-height: 100vh; background-color: var(--color-bg-soft); border-right: 1px solid var(--color-border); flex-shrink: 0; padding: 1.5rem 1rem;">

        <!-- Logo -->
        <div style="margin-bottom: 2rem; padding: 0 0.5rem;">
            <span style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em;">FitPass</span>
            <div style="font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-top: 0.125rem;">Admin</div>
        </div>

        <!-- Navigation -->
        <nav style="display: flex; flex-direction: column; gap: 0.25rem;">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'nav-link' }}">Dashboard</a>
            <a href="{{ route('admin.members') }}" class="{{ request()->routeIs('admin.members') ? 'nav-link-active' : 'nav-link' }}">Membres</a>
            <a href="{{ route('admin.gyms') }}" class="{{ request()->routeIs('admin.gyms') ? 'nav-link-active' : 'nav-link' }}">Salles</a>
        </nav>

        <!-- User info -->
        <div style="position: absolute; bottom: 1.5rem; left: 1rem; right: 1rem;">
            <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 0.5rem;">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-ghost" style="width: 100%; padding: 0.375rem 0.75rem; text-align: left;">Déconnexion</button>
            </form>
        </div>
    </aside>

    <!-- Contenu principal -->
    <div style="flex: 1; overflow: auto;">

        <!-- Header page -->
        <header style="background-color: var(--color-bg-soft); border-bottom: 1px solid var(--color-border); padding: 1rem 2rem; display: flex; align-items: center; justify-content: space-between;">
            <h1 style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">@yield('title', 'Dashboard')</h1>
            <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ now()->format('d/m/Y') }}</div>
        </header>

        <!-- Flash messages -->
        @if(session('success'))
            <div style="padding: 1rem 2rem 0;">
                <div class="alert-success">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div style="padding: 1rem 2rem 0;">
                <div class="alert-error">{{ session('error') }}</div>
            </div>
        @endif

        <main style="padding: 2rem;">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
