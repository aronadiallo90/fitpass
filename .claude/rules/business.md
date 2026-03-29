# Règles Métier — [NOM_PROJET]
# Copier dans .claude/rules/business.md de chaque projet
# Adapter selon les spécificités du projet

## Données financières
- Devise : FCFA
- Stockage prix : integer en base de données — JAMAIS float
- Affichage : formater côté frontend (ex: "25 000 FCFA")

## Format identifiants
FORMAT_ID=FIT-{YYYY}-{00001}   # ex: FIT-2026-00042

## Statuts et transitions
# Abonnement
pending -> active (après paiement confirmé PayTech)
active -> expired (date_fin dépassée — cron quotidien)
active -> cancelled (annulation admin uniquement)
pending -> cancelled (non-paiement après 24h)

# Paiement
pending -> completed (webhook PayTech success)
pending -> failed (webhook PayTech échec ou timeout 30min)
completed -> refunded (admin uniquement)

## Règles de paiement
- Paiement PayTech (Wave ou Orange Money) requis AVANT activation
- Webhook PayTech valide la transaction → déclenche activation
- Pas de paiement cash, pas de crédit

## Règles QR Code
- 1 QR code par membre (UUID unique en base)
- QR code valide uniquement si subscription.status = active ET date_fin >= aujourd'hui
- 1 checkin maximum par salle par jour par membre (anti-abus)
- Scan invalide → SMS d'alerte au membre

## Règles de notifications SMS (Twilio)
- Activation abonnement : SMS immédiat
- J-7 avant expiration : SMS rappel renouvellement
- J-1 avant expiration : SMS rappel urgent
- Expiration : SMS + lien renouvellement
- Checkin réussi : SMS confirmation (optionnel selon plan)

## Règles d'annulation
- Membre : ne peut pas annuler (contacter admin)
- Admin : peut annuler à tout statut
- Pas de remboursement automatique (gestion manuelle)

## Taxes et fiscalité
# Sénégal commerce informel : pas de TVA

## Analytics — événements à tracker
- subscription_created
- payment_completed
- payment_failed
- gym_checkin (avec gym_id)
- subscription_expired
- subscription_renewed

## Zones géographiques
# Phase 1 : Dakar uniquement
# Salles géolocalisées via coordonnées GPS (Leaflet.js)

## Règles spécifiques FitPass
- Un membre ne peut avoir qu'1 abonnement actif à la fois
- Gym owner voit uniquement les checkins de ses salles
- Renouvellement = nouvelle subscription (pas d'extension de l'ancienne)
- Plans disponibles : mensuel (30j), trimestriel (90j), annuel (365j)
