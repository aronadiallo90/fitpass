@extends('layouts.landing')

@section('title', 'FitPass Dakar — 1 abonnement · Toutes les salles de sport')
@section('meta_description', 'Accédez à toutes les salles de sport partenaires à Dakar avec un seul abonnement FitPass. Paiement Wave & Orange Money. Sans engagement.')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    html { scroll-behavior: smooth; }

    .hero-bg {
        background: linear-gradient(135deg, rgba(10,10,15,0.95) 0%, rgba(20,10,10,0.9) 100%),
                    url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23FF3B3B' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .plan-card-popular {
        border-color: var(--color-primary) !important;
        transform: scale(1.02);
    }

    .step-number {
        width: 3rem; height: 3rem;
        background-color: var(--color-primary);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-heading);
        font-size: 1.25rem; font-weight: 700;
        color: white;
        flex-shrink: 0;
        margin: 0 auto 1rem;
    }

    .testimonial-avatar {
        width: 3rem; height: 3rem;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-family: var(--font-heading);
        font-size: 1.25rem; font-weight: 700;
        color: white;
        flex-shrink: 0;
    }

    section { scroll-margin-top: 64px; }

    @media (min-width: 768px) {
        .plan-card-popular { transform: scale(1.04); }
    }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════
     HERO
════════════════════════════════════════════════════════ --}}
<section id="hero" class="hero-bg" style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 6rem 1.5rem 4rem;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">

        {{-- Badge --}}
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; background-color: rgba(255,59,59,0.1); border: 1px solid rgba(255,59,59,0.3); border-radius: 999px; padding: 0.375rem 1rem; margin-bottom: 2rem;">
            <span style="width: 6px; height: 6px; background-color: var(--color-primary); border-radius: 50%; display: inline-block;"></span>
            <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-primary);">Maintenant disponible à Dakar</span>
        </div>

        {{-- Titre --}}
        <h1 style="font-family: var(--font-heading); font-size: clamp(2.75rem, 8vw, 5.5rem); font-weight: 800; text-transform: uppercase; letter-spacing: 0.03em; line-height: 0.95; margin-bottom: 1.5rem; color: var(--color-text);">
            1 abonnement<br>
            <span style="color: var(--color-primary);">Toutes les salles</span><br>
            de Dakar
        </h1>

        {{-- Sous-titre --}}
        <p style="font-size: clamp(1rem, 3vw, 1.25rem); color: var(--color-text-muted); max-width: 540px; margin: 0 auto 2.5rem; line-height: 1.6;">
            Accédez à tous nos gyms et salles de sport partenaires à Dakar avec un seul abonnement.<br>
            Paiement Wave & Orange Money. Sans engagement.
        </p>

        {{-- CTAs --}}
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('register') }}" class="btn-primary" style="padding: 1rem 2.5rem; font-size: 1rem;">
                Commencer maintenant
            </a>
            <a href="#salles" class="btn-outline" style="padding: 1rem 2.5rem; font-size: 1rem;">
                Voir les salles
            </a>
        </div>

        {{-- Stats --}}
        <div style="display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap; margin-top: 4rem; padding-top: 3rem; border-top: 1px solid rgba(255,255,255,0.08);">
            @foreach([['10+', 'Salles partenaires'], ['3', 'Formules'], ['Wave & OM', 'Paiement mobile']] as [$val, $label])
            <div style="text-align: center;">
                <div style="font-family: var(--font-heading); font-size: 2rem; font-weight: 700; color: var(--color-primary);">{{ $val }}</div>
                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-text-muted); margin-top: 0.25rem;">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     COMMENT ÇA MARCHE
════════════════════════════════════════════════════════ --}}
<section id="comment" style="padding: 5rem 1.5rem; background-color: var(--color-bg-soft);">
    <div style="max-width: 1024px; margin: 0 auto;">

        <div style="text-align: center; margin-bottom: 3.5rem;">
            <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text);">
                Comment ça marche
            </h2>
            <p style="color: var(--color-text-muted); margin-top: 0.75rem;">3 étapes, c'est tout.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 2rem;">

            @foreach([
                ['01', '💳', 'Choisissez un plan', 'Découverte, Mensuel, Trimestriel ou Annuel. Payez en Wave ou Orange Money en 30 secondes.'],
                ['02', '📲', 'Recevez votre QR code', 'Votre QR code unique est généré instantanément dans l\'app. Toujours disponible, même sans connexion.'],
                ['03', '🏋️', 'Entrez dans la salle', 'Présentez votre QR code à l\'accueil. Le gérant scanne, vous entrez. C\'est tout.'],
            ] as [$num, $icon, $title, $desc])
            <div style="text-align: center; padding: 2rem 1.5rem;">
                <div class="step-number">{{ $num }}</div>
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">{{ $icon }}</div>
                <h3 style="font-family: var(--font-heading); font-size: 1.25rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text); margin-bottom: 0.75rem;">{{ $title }}</h3>
                <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">{{ $desc }}</p>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     PLANS TARIFAIRES
════════════════════════════════════════════════════════ --}}
<section id="plans" style="padding: 5rem 1.5rem; background-color: var(--color-bg);">
    <div style="max-width: 1200px; margin: 0 auto;">

        <div style="text-align: center; margin-bottom: 3.5rem;">
            <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text);">
                Nos formules
            </h2>
            <p style="color: var(--color-text-muted); margin-top: 0.75rem;">Toutes les salles partenaires incluses. Sans engagement.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; align-items: center;">

            {{-- Découverte --}}
            <div class="card" style="text-align: center;">
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-bottom: 1rem;">Découverte</div>
                <div style="font-family: var(--font-heading); font-size: 3rem; font-weight: 700; color: var(--color-text); line-height: 1;">15 000</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 1.5rem;">FCFA · 4 séances</div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem; text-align: left;">
                    @foreach(['4 entrées en salle', 'Valable 30 jours', 'Toutes les salles', 'QR code instantané'] as $feature)
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--color-text-muted); padding: 0.375rem 0;">
                        <span style="color: var(--color-success); font-size: 1rem;">✓</span> {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="btn-outline" style="width: 100%; display: block; text-align: center; padding: 0.75rem;">Choisir</a>
            </div>

            {{-- Mensuel — POPULAIRE --}}
            <div class="card plan-card-popular" style="text-align: center; position: relative; padding-top: 2.5rem;">
                <div style="position: absolute; top: -1px; left: 50%; transform: translateX(-50%); background: var(--color-primary); color: white; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em; padding: 0.25rem 1rem; border-radius: 0 0 0.5rem 0.5rem; white-space: nowrap;">
                    ★ Populaire
                </div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-primary); margin-bottom: 1rem;">Mensuel</div>
                <div style="font-family: var(--font-heading); font-size: 3rem; font-weight: 700; color: var(--color-text); line-height: 1;">25 000</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 1.5rem;">FCFA · 30 jours</div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem; text-align: left;">
                    @foreach(['Entrées illimitées', 'Toutes les salles', 'QR code instantané', 'SMS de rappel', 'Sans engagement'] as $feature)
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--color-text-muted); padding: 0.375rem 0;">
                        <span style="color: var(--color-success); font-size: 1rem;">✓</span> {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="btn-primary" style="width: 100%; display: block; text-align: center; padding: 0.75rem;">Commencer</a>
            </div>

            {{-- Trimestriel --}}
            <div class="card" style="text-align: center;">
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-bottom: 1rem;">Trimestriel</div>
                <div style="font-family: var(--font-heading); font-size: 3rem; font-weight: 700; color: var(--color-text); line-height: 1;">65 000</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 0.25rem;">FCFA · 90 jours</div>
                <div style="font-size: 0.7rem; color: var(--color-success); margin-bottom: 1.25rem;">↓ Économisez 10 000 FCFA</div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem; text-align: left;">
                    @foreach(['Entrées illimitées', 'Toutes les salles', 'QR code instantané', 'SMS de rappel', 'Sans engagement'] as $feature)
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--color-text-muted); padding: 0.375rem 0;">
                        <span style="color: var(--color-success); font-size: 1rem;">✓</span> {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="btn-outline" style="width: 100%; display: block; text-align: center; padding: 0.75rem;">Choisir</a>
            </div>

            {{-- Annuel --}}
            <div class="card" style="text-align: center;">
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-text-muted); margin-bottom: 1rem;">Annuel</div>
                <div style="font-family: var(--font-heading); font-size: 3rem; font-weight: 700; color: var(--color-text); line-height: 1;">220 000</div>
                <div style="font-size: 0.75rem; color: var(--color-text-muted); margin-bottom: 0.25rem;">FCFA · 365 jours</div>
                <div style="font-size: 0.7rem; color: var(--color-success); margin-bottom: 1.25rem;">↓ Économisez 80 000 FCFA</div>
                <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem; text-align: left;">
                    @foreach(['Entrées illimitées', 'Toutes les salles', 'QR code instantané', 'SMS de rappel', 'Priorité nouvelles salles'] as $feature)
                    <li style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--color-text-muted); padding: 0.375rem 0;">
                        <span style="color: var(--color-success); font-size: 1rem;">✓</span> {{ $feature }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="btn-outline" style="width: 100%; display: block; text-align: center; padding: 0.75rem;">Choisir</a>
            </div>

        </div>

        {{-- Paiement mobile --}}
        <div style="text-align: center; margin-top: 2.5rem; padding: 1.5rem; background: rgba(255,255,255,0.03); border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.06);">
            <p style="font-size: 0.875rem; color: var(--color-text-muted);">
                💳 Paiement sécurisé via
                <strong style="color: var(--color-text);">Wave</strong> &
                <strong style="color: var(--color-text);">Orange Money</strong>
                — Activation immédiate après paiement
            </p>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     SALLES PARTENAIRES
════════════════════════════════════════════════════════ --}}
<section id="salles" style="padding: 5rem 1.5rem; background-color: var(--color-bg-soft);">
    <div style="max-width: 1200px; margin: 0 auto;">

        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text);">
                {{ $gymsCount }}+ gyms et salles à Dakar
            </h2>
            <p style="color: var(--color-text-muted); margin-top: 0.75rem;">Musculation · Yoga · Fitness · Arts martiaux · CrossFit — tous accessibles avec votre abonnement.</p>
        </div>

        <div class="map-container" id="landing-map" style="height: 420px; margin-bottom: 2rem;"></div>

        {{-- Liste gyms --}}
        @if($gyms->count())
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
            @foreach($gyms as $gym)
            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; background: var(--color-bg-card); border: 1px solid var(--color-border); border-radius: 0.5rem;">
                <span style="color: var(--color-primary); font-size: 1rem;">📍</span>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 500; color: var(--color-text);">{{ $gym->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ $gym->address }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     TÉMOIGNAGES
════════════════════════════════════════════════════════ --}}
<section id="temoignages" style="padding: 5rem 1.5rem; background-color: var(--color-bg);">
    <div style="max-width: 1024px; margin: 0 auto;">

        <div style="text-align: center; margin-bottom: 3.5rem;">
            <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text);">
                Ils nous font confiance
            </h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">

            @foreach([
                ['AM', '#FF3B3B', 'Awa M.', 'Membre depuis 3 mois', 'Avant je payais 3 salles séparément. Avec FitPass je dépense moitié moins et je varie mes entraînements chaque semaine.'],
                ['MD', '#FF8C00', 'Moussa D.', 'Membre depuis 6 mois', 'Le QR code c\'est genius. Je montre mon téléphone, le gérant scanne, j\'entre. 5 secondes chrono.'],
                ['KN', '#22C55E', 'Khady N.', 'Membre depuis 1 mois', 'Payer en Wave c\'est trop pratique. Je me suis abonnée depuis mon portable en 2 minutes.'],
            ] as [$initials, $color, $name, $since, $quote])
            <div class="card" style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div class="testimonial-avatar" style="background-color: {{ $color }};">{{ $initials }}</div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem; color: var(--color-text);">{{ $name }}</div>
                        <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ $since }}</div>
                    </div>
                </div>
                <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.65; font-style: italic;">"{{ $quote }}"</p>
                <div style="display: flex; gap: 0.25rem;">
                    @for($i = 0; $i < 5; $i++)
                        <span style="color: var(--color-warning); font-size: 0.9rem;">★</span>
                    @endfor
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FAQ
════════════════════════════════════════════════════════ --}}
<section id="faq" style="padding: 5rem 1.5rem; background-color: var(--color-bg-soft);">
    <div style="max-width: 720px; margin: 0 auto;">

        <div style="text-align: center; margin-bottom: 3.5rem;">
            <h2 style="font-family: var(--font-heading); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text);">
                Questions fréquentes
            </h2>
        </div>

        <div x-data="{ open: null }">
            @foreach([
                ['Quelles salles sont accessibles avec FitPass ?', 'Toutes nos salles partenaires à Dakar sont accessibles selon votre formule. La liste est disponible sur la carte interactive ci-dessus. De nouvelles salles rejoignent le réseau chaque mois.'],
                ['Comment fonctionne le paiement Wave / Orange Money ?', 'Après votre inscription, vous choisissez votre formule et payez via Wave ou Orange Money. Votre abonnement est activé immédiatement après confirmation du paiement. Aucune carte bancaire requise.'],
                ['Puis-je aller dans plusieurs salles dans la même journée ?', 'Vous pouvez accéder à autant de salles que vous voulez, avec une limite de 1 entrée par salle par jour. Idéal pour varier les entraînements.'],
                ['Que se passe-t-il si j\'oublie mon téléphone ?', 'Contactez le support FitPass. En attendant, certaines salles peuvent vérifier votre identité manuellement. Nous recommandons de toujours avoir votre QR code accessible hors-ligne.'],
                ['Comment renouveler mon abonnement ?', 'Vous recevez un SMS de rappel 7 jours et 1 jour avant l\'expiration. Reconnectez-vous à votre espace membre et choisissez votre nouvelle formule. Le renouvellement prend moins de 2 minutes.'],
            ] as $i => [$question, $answer])
            <div style="border-bottom: 1px solid var(--color-border);">
                <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                        style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 0; background: none; border: none; cursor: pointer; text-align: left; gap: 1rem;">
                    <span style="font-size: 0.95rem; font-weight: 500; color: var(--color-text);">{{ $question }}</span>
                    <span style="color: var(--color-primary); font-size: 1.25rem; flex-shrink: 0; transition: transform 0.3s;"
                          :style="open === {{ $i }} ? 'transform: rotate(45deg)' : ''">+</span>
                </button>
                <div x-show="open === {{ $i }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     style="padding-bottom: 1.25rem; display: none;">
                    <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.7;">{{ $answer }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CTA FINAL
════════════════════════════════════════════════════════ --}}
<section style="padding: 5rem 1.5rem; background: linear-gradient(135deg, var(--color-primary) 0%, #CC2222 100%);">
    <div style="max-width: 640px; margin: 0 auto; text-align: center;">
        <h2 style="font-family: var(--font-heading); font-size: clamp(2.25rem, 6vw, 3.5rem); text-transform: uppercase; letter-spacing: 0.04em; color: white; margin-bottom: 1rem; line-height: 1.1;">
            Prêt à rejoindre<br>FitPass ?
        </h2>
        <p style="color: rgba(255,255,255,0.85); font-size: 1rem; margin-bottom: 2.5rem; line-height: 1.6;">
            Inscription gratuite · Paiement Wave ou Orange Money<br>Accès immédiat après paiement
        </p>
        <a href="{{ route('register') }}"
           style="display: inline-flex; align-items: center; background: white; color: var(--color-primary); padding: 1rem 3rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; font-size: 1rem; border-radius: 0.5rem; text-decoration: none; transition: all 0.3s; font-family: var(--font-heading);"
           onmouseover="this.style.transform='scale(1.03)'"
           onmouseout="this.style.transform='scale(1)'">
            Rejoindre FitPass →
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════════════ --}}
<footer style="background-color: var(--color-bg-soft); border-top: 1px solid var(--color-border); padding: 3rem 1.5rem;">
    <div style="max-width: 1200px; margin: 0 auto;">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 2rem; margin-bottom: 2.5rem;">

            {{-- Brand --}}
            <div>
                <div style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: var(--color-primary); text-transform: uppercase; margin-bottom: 0.75rem;">FitPass</div>
                <p style="font-size: 0.8rem; color: var(--color-text-muted); line-height: 1.6;">1 abonnement · Toutes les salles de Dakar.</p>
            </div>

            {{-- Navigation --}}
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-text-muted); margin-bottom: 1rem; font-weight: 600;">Navigation</div>
                @foreach([['#plans', 'Nos formules'], ['#salles', 'Les salles'], ['#faq', 'FAQ'], [route('login'), 'Connexion'], [route('register'), 'S\'inscrire']] as [$href, $label])
                <a href="{{ $href }}" style="display: block; font-size: 0.85rem; color: var(--color-text-muted); text-decoration: none; margin-bottom: 0.5rem; transition: color 0.2s;"
                   onmouseover="this.style.color='var(--color-text)'"
                   onmouseout="this.style.color='var(--color-text-muted)'">{{ $label }}</a>
                @endforeach
            </div>

            {{-- Contact --}}
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-text-muted); margin-bottom: 1rem; font-weight: 600;">Contact</div>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); line-height: 1.6;">
                    📧 contact@fitpass.sn<br>
                    📱 WhatsApp : +221 78 000 00 00<br>
                    📍 Dakar, Sénégal
                </p>
            </div>

            {{-- Réseaux --}}
            <div>
                <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-text-muted); margin-bottom: 1rem; font-weight: 600;">Suivez-nous</div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    @foreach([['Instagram', '📷'], ['TikTok', '🎵'], ['WhatsApp', '💬']] as [$name, $icon])
                    <span style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; color: var(--color-text-muted); padding: 0.375rem 0.75rem; border: 1px solid var(--color-border); border-radius: 999px;">
                        {{ $icon }} {{ $name }}
                    </span>
                    @endforeach
                </div>
            </div>

        </div>

        <div style="border-top: 1px solid var(--color-border); padding-top: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <p style="font-size: 0.75rem; color: var(--color-text-muted);">© {{ date('Y') }} FitPass Dakar. Tous droits réservés.</p>
            <div style="display: flex; gap: 1.5rem;">
                @foreach([['Mentions légales', '#'], ['Confidentialité', '#'], ['CGU', '#']] as [$label, $href])
                <a href="{{ $href }}" style="font-size: 0.75rem; color: var(--color-text-muted); text-decoration: none;"
                   onmouseover="this.style.color='var(--color-text)'"
                   onmouseout="this.style.color='var(--color-text-muted)'">{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

@endsection

@php $mappableGyms = $gyms->whereNotNull('latitude')->whereNotNull('longitude')->values(); @endphp
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gyms = @json($mappableGyms);

    if (!gyms.length) return;

    const map = L.map('landing-map').setView([14.7167, -17.4677], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    const icon = L.divIcon({
        html: '<div style="width:14px;height:14px;background:#FF3B3B;border-radius:50%;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.5);"></div>',
        iconSize: [14, 14], iconAnchor: [7, 7], className: ''
    });

    gyms.forEach(gym => {
        L.marker([gym.latitude, gym.longitude], { icon })
            .addTo(map)
            .bindPopup(`<strong>${gym.name}</strong><br><small>${gym.address}</small>`);
    });
});
</script>
@endpush
