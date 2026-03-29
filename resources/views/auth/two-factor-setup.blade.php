<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration 2FA — FitPass Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">

    <div style="width: 100%; max-width: 480px;">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <h1 style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">FitPass</h1>
            <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-top: 0.25rem;">Administration</p>
        </div>

        <div class="card-static">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 0.5rem 0;">
                Authentification à deux facteurs
            </h2>
            <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0 0 2rem 0;">
                La 2FA est obligatoire pour les comptes administrateurs. Scannez le QR code avec Google Authenticator ou Authy.
            </p>

            @if($errors->any())
                <div class="alert-error" style="margin-bottom: 1.5rem;">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert-success" style="margin-bottom: 1.5rem;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- QR Code (généré localement) -->
            @if(isset($qrCodeSvg))
                <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 1.5rem; background: white; padding: 1rem; border-radius: 0.5rem; gap: 0.5rem;">
                    {!! $qrCodeSvg !!}
                    <p style="font-size: 0.75rem; color: #666; margin: 0;">Scannez avec Google Authenticator ou Authy</p>
                </div>
            @endif

            <!-- Clé manuelle -->
            @if(isset($secretKey))
                <div style="margin-bottom: 1.5rem; background-color: var(--color-bg); border: 1px solid var(--color-border); border-radius: 0.5rem; padding: 1rem;">
                    <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-text-muted); margin: 0 0 0.5rem 0;">Clé manuelle</p>
                    <code style="font-size: 0.875rem; color: var(--color-primary); word-break: break-all;">{{ $secretKey }}</code>
                </div>
            @endif

            <!-- Vérification du code -->
            <form method="POST" action="{{ route('two-factor.confirm') }}">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label for="code" class="label">Code de vérification (6 chiffres)</label>
                    <input
                        id="code"
                        type="text"
                        name="code"
                        placeholder="000000"
                        class="input"
                        inputmode="numeric"
                        maxlength="6"
                        autocomplete="one-time-code"
                        required
                        style="letter-spacing: 0.3em; text-align: center; font-size: 1.25rem;"
                    >
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Activer la 2FA</button>
            </form>

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
