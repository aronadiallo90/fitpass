<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FitPass') — FitPass Dakar</title>
    <meta name="description" content="@yield('description', '1 abonnement, toutes les salles de Dakar')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh;">

    <!-- Navbar membre -->
    <nav style="background-color: var(--color-bg-soft); border-bottom: 1px solid var(--color-border);">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 1rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; height: 64px;">

                <!-- Logo -->
                <a href="{{ route('member.dashboard') }}" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                    <span style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em;">FitPass</span>
                    <span style="font-size: 0.625rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted);">Dakar</span>
                </a>

                <!-- Nav links desktop -->
                <div style="display: none;" class="md-flex">
                    <a href="{{ route('member.dashboard') }}" class="{{ request()->routeIs('member.dashboard') ? 'nav-link-active' : 'nav-link' }}">Dashboard</a>
                    <a href="{{ route('member.gyms') }}" class="{{ request()->routeIs('member.gyms*') ? 'nav-link-active' : 'nav-link' }}" style="margin-left: 0.25rem;">Salles</a>
                    <a href="{{ route('member.qrcode') }}" class="{{ request()->routeIs('member.qrcode') ? 'nav-link-active' : 'nav-link' }}" style="margin-left: 0.25rem;">Mon QR Code</a>
                    <a href="{{ route('member.subscriptions') }}" class="{{ request()->routeIs('member.subscriptions') ? 'nav-link-active' : 'nav-link' }}" style="margin-left: 0.25rem;">Abonnement</a>
                </div>

                <!-- User menu -->
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 0.875rem; color: var(--color-text-muted);">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-ghost" style="padding: 0.375rem 0.75rem;">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash messages -->
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

    <!-- Contenu principal -->
    <main style="max-width: 1280px; margin: 0 auto; padding: 2rem 1rem;">
        @yield('content')
    </main>

    <!-- Footer minimal -->
    <footer style="border-top: 1px solid var(--color-border); padding: 2rem 1rem; margin-top: 4rem;">
        <div style="max-width: 1280px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-family: var(--font-heading); font-size: 1.125rem; color: var(--color-primary); text-transform: uppercase;">FitPass Dakar</span>
            <span style="font-size: 0.75rem; color: var(--color-text-muted);">1 abonnement, toutes les salles de Dakar</span>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
