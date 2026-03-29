<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Salle — @yield('title', 'Dashboard') — FitPass</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh;">

    <!-- Navbar gym owner -->
    <nav style="background-color: var(--color-bg-soft); border-bottom: 1px solid var(--color-border);">
        <div style="max-width: 768px; margin: 0 auto; padding: 0 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; height: 60px;">

                <a href="{{ route('gym.dashboard') }}" style="text-decoration: none;">
                    <span style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase;">FitPass</span>
                    <span style="font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-left: 0.5rem;">Salle</span>
                </a>

                <div style="display: flex; gap: 0.25rem;">
                    <a href="{{ route('gym.scan') }}" class="{{ request()->routeIs('gym.scan') ? 'nav-link-active' : 'nav-link' }}">Scanner</a>
                    <a href="{{ route('gym.checkins') }}" class="{{ request()->routeIs('gym.checkins') ? 'nav-link-active' : 'nav-link' }}">Historique</a>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-ghost" style="padding: 0.375rem 0.75rem;">Sortir</button>
                </form>
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
