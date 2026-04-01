<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hors ligne — FitPass Dakar</title>
    <meta name="theme-color" content="#FF3B3B">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700&family=Inter:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: #0A0A0F;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            text-align: center;
        }

        .container {
            max-width: 360px;
            width: 100%;
        }

        .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background-color: rgba(255, 59, 59, 0.12);
            border: 1px solid rgba(255, 59, 59, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 36px;
            height: 36px;
            color: #FF3B3B;
        }

        .logo {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #FF3B3B;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        h1 {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        p {
            color: #8888A0;
            font-size: 0.9375rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background-color: #FF3B3B;
            color: #FFFFFF;
            font-family: 'Inter', sans-serif;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            transition: opacity 0.2s ease;
            width: 100%;
        }

        .btn:hover { opacity: 0.88; }

        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 1.75rem 0;
        }

        .tip {
            font-size: 0.8125rem;
            color: #8888A0;
        }

        .tip strong {
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">FitPass</div>

        <div class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="1" y1="1" x2="23" y2="23"></line>
                <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>
                <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path>
                <path d="M10.71 5.05A16 16 0 0 1 22.56 9"></path>
                <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                <line x1="12" y1="20" x2="12.01" y2="20"></line>
            </svg>
        </div>

        <h1>Hors ligne</h1>
        <p>Vous n'êtes pas connecté à internet.<br>Vérifiez votre connexion et réessayez.</p>

        <button class="btn" onclick="window.location.reload()">
            Réessayer
        </button>

        <hr class="divider">

        <p class="tip">
            <strong>Astuce :</strong> Installez FitPass sur votre écran d'accueil pour un accès rapide même avec une connexion limitée.
        </p>
    </div>
</body>
</html>
