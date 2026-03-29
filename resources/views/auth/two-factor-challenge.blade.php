<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA — FitPass Admin</title>
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
            <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-top: 0.25rem;">Administration</p>
        </div>

        <div class="card-static">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">
                Vérification 2FA
            </h2>
            <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0 0 2rem 0;">
                Saisissez le code à 6 chiffres affiché dans votre application d'authentification.
            </p>

            @if($errors->any())
                <div class="alert-error" style="margin-bottom: 1.5rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Code TOTP -->
            <form method="POST" action="{{ route('two-factor.verify') }}">
                @csrf

                <div style="margin-bottom: 1.75rem;">
                    <label for="code" class="label">Code de vérification</label>
                    <input
                        id="code"
                        type="text"
                        name="code"
                        placeholder="000000"
                        class="input"
                        inputmode="numeric"
                        maxlength="6"
                        autocomplete="one-time-code"
                        autofocus
                        required
                        style="letter-spacing: 0.3em; text-align: center; font-size: 1.5rem;"
                    >
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Vérifier</button>
            </form>

            <!-- Code de récupération -->
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                <p style="font-size: 0.875rem; color: var(--color-text-muted); text-align: center; margin: 0 0 0.75rem 0;">
                    Accès à votre téléphone impossible ?
                </p>

                <form method="POST" action="{{ route('two-factor.recovery') }}">
                    @csrf

                    <div style="margin-bottom: 1rem;">
                        <input
                            type="text"
                            name="recovery_code"
                            placeholder="Code de récupération"
                            class="input"
                            autocomplete="off"
                            style="font-size: 0.8125rem;"
                        >
                    </div>

                    <button type="submit" class="btn-outline" style="width: 100%; font-size: 0.875rem;">
                        Utiliser un code de récupération
                    </button>
                </form>
            </div>

            <div style="text-align: center; margin-top: 1.5rem;">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-ghost" style="font-size: 0.875rem;">Se déconnecter</button>
                </form>
            </div>
        </div>

    </div>

</body>
</html>
