<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'FitPass Dakar — 1 abonnement · Toutes les salles')</title>
    <meta name="description" content="@yield('meta_description', 'FitPass : accédez à toutes les salles de sport partenaires à Dakar avec un seul abonnement. Paiement Wave & Orange Money.')">

    {{-- OG Tags --}}
    <meta property="og:title"       content="@yield('og_title', 'FitPass Dakar — 1 abonnement · Toutes les salles')">
    <meta property="og:description" content="@yield('og_description', 'Accédez à toutes les salles de sport partenaires à Dakar. Paiement Wave & Orange Money.')">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="{{ url('/') }}">
    <meta property="og:image"       content="{{ url('/og-image.jpg') }}">
    <meta property="og:locale"      content="fr_SN">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="FitPass Dakar">
    <meta name="twitter:description" content="1 abonnement · Toutes les salles de sport à Dakar">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); overflow-x: hidden;">

    {{-- Navbar landing --}}
    <nav style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; background-color: rgba(10,10,15,0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.05);">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; display: flex; align-items: center; justify-content: space-between; height: 64px;">
            <a href="{{ url('/') }}" style="text-decoration: none;">
                <span style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em;">FitPass</span>
                <span style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-left: 0.375rem;">Dakar</span>
            </a>

            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="{{ route('login') }}"
                   style="font-size: 0.875rem; color: var(--color-text-muted); text-decoration: none; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem 1rem; transition: color 0.2s;"
                   onmouseover="this.style.color='var(--color-text)'"
                   onmouseout="this.style.color='var(--color-text-muted)'">
                    Connexion
                </a>
                <a href="{{ route('register') }}" class="btn-primary" style="padding: 0.5rem 1.5rem; font-size: 0.8rem;">
                    S'inscrire
                </a>
            </div>
        </div>
    </nav>

    @yield('content')

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
