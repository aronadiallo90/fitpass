<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — FitPass Dakar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">

    <div style="width: 100%; max-width: 400px;">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <h1 style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">FitPass</h1>
            <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-top: 0.25rem;">1 abonnement · Toutes les salles</p>
        </div>

        <div class="card-static">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1.5rem 0;">Connexion</h2>

            @if($errors->any())
                <div class="alert-error" style="margin-bottom: 1.5rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label for="phone" class="label">Téléphone</label>
                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="+221 77 123 45 67"
                        class="input"
                        autocomplete="tel"
                        required
                    >
                </div>

                <div style="margin-bottom: 1.75rem;">
                    <label for="password" class="label">Mot de passe</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        class="input"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Se connecter</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <span style="font-size: 0.875rem; color: var(--color-text-muted);">Pas encore de compte ?</span>
                <a href="{{ route('register') }}" style="color: var(--color-primary); text-decoration: none; font-size: 0.875rem; margin-left: 0.25rem;">S'inscrire</a>
            </div>
        </div>
    </div>

</body>
</html>
