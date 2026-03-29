<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — FitPass Dakar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: var(--color-bg); color: var(--color-text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">

    <div style="width: 100%; max-width: 420px;">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 700; color: var(--color-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">FitPass</h1>
            <p style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-top: 0.25rem;">Rejoindre FitPass Dakar</p>
        </div>

        <div class="card-static">
            <h2 style="font-family: var(--font-heading); font-size: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 1.5rem 0;">Créer un compte</h2>

            @if($errors->any())
                <div class="alert-error" style="margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label for="name" class="label">Nom complet</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Awa Diop" class="input" required>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="phone" class="label">Téléphone Wave / Orange Money</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+221 77 123 45 67" class="input" required>
                    <p style="font-size: 0.75rem; color: var(--color-text-muted); margin-top: 0.375rem;">Votre numéro Wave ou Orange Money</p>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="email" class="label">Email <span style="color: var(--color-text-muted);">(optionnel)</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="awa@example.com" class="input">
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="password" class="label">Mot de passe</label>
                    <input id="password" type="password" name="password" placeholder="Minimum 8 caractères" class="input" required>
                </div>

                <div style="margin-bottom: 1.75rem;">
                    <label for="password_confirmation" class="label">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" class="input" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">Créer mon compte</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <span style="font-size: 0.875rem; color: var(--color-text-muted);">Déjà membre ?</span>
                <a href="{{ route('login') }}" style="color: var(--color-primary); text-decoration: none; font-size: 0.875rem; margin-left: 0.25rem;">Se connecter</a>
            </div>
        </div>
    </div>

</body>
</html>
