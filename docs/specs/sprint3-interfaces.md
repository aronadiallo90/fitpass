# Spec Sprint 3 — Interfaces
Projet : FitPass Dakar
Version : 1.0
Date : 2026-03-30
Agents : DESIGNER → DEV → QA
Sprint : 3

---

## Résumé

Sprint 3 livre toutes les interfaces utilisateur : dashboard membre avec QR code,
scanner gym_owner, carte Leaflet des salles, et dashboards admin.
Priorité absolue : mobile-first 375px, parcours utilisateur < 3 clics.

---

## S3-T1 — Dashboard membre

### User Story
En tant que membre,
Je veux voir d'un coup d'œil mon abonnement actif et mon QR code,
Afin d'accéder rapidement à la salle sans chercher.

**Critères d'acceptation :**
- [ ] Statut abonnement visible dès l'arrivée (badge coloré : vert/ambre/rouge)
- [ ] QR code affiché en taille correcte pour scan (min 200×200px)
- [ ] Date d'expiration lisible (format "Expire le 15 avril 2026")
- [ ] Historique des 5 dernières entrées (salle + date + heure)
- [ ] CTA "Renouveler" si abonnement expire dans 7 jours ou moins
- [ ] CTA "S'abonner" si aucun abonnement actif
- [ ] Plan Découverte : afficher "X séances restantes sur 4"

**Estimation :** L
**Priorité :** Critique

### Données API nécessaires
- `GET /api/v1/subscriptions/active` → abonnement actif du membre
- `GET /api/v1/member/checkins?limit=5` → 5 derniers checkins

### Design
- Fond : `#0A0A0F`, cards : `#13131A`
- Badge actif : vert `#22C55E`, expiré : rouge `#EF4444`, en attente : ambre `#F59E0B`
- QR code : fond blanc, bord arrondi, centré
- Mobile 375px en priorité

---

## S3-T2 — Page QR code grand format

### User Story
En tant que membre,
Je veux afficher mon QR code en plein écran,
Afin que le scanner de la salle puisse le lire même en mauvaise lumière.

**Critères d'acceptation :**
- [ ] QR code occupe 80%+ de l'écran en mode portrait
- [ ] Fond blanc forcé derrière le QR (lisibilité)
- [ ] Nom du membre affiché sous le QR
- [ ] Badge statut visible (valide = vert, expiré = rouge)
- [ ] Si expiré : overlay rouge avec message "Abonnement expiré — Renouvelez"
- [ ] Bouton "Renouveler" si expiré
- [ ] Pas de navigation superflue — écran épuré

**Estimation :** M
**Priorité :** Critique

### Route Web
`GET /member/qr-code` → view `member.qr-code`

### Note technique
QR code généré server-side via `simplesoftwareio/simple-qrcode`.
La valeur encodée est le `qr_token` (UUID) de l'utilisateur.

---

## S3-T3 — Interface scan gym_owner

### User Story
En tant que propriétaire de salle,
Je veux scanner le QR code d'un membre avec mon téléphone,
Afin de valider son entrée instantanément (vert = autorisé, rouge = refusé).

**Critères d'acceptation :**
- [ ] Accès caméra demandé à l'ouverture de la page
- [ ] Zone de scan visible avec cadre animé (pulse)
- [ ] Résultat vert affiché < 1 seconde après scan réussi : nom membre + "Entrée validée"
- [ ] Résultat rouge affiché < 1 seconde si refusé : raison claire ("Abonnement expiré", "Déjà entré aujourd'hui")
- [ ] Son de confirmation (optionnel, si activé)
- [ ] Bouton "Scanner à nouveau" après chaque résultat
- [ ] Historique du jour visible en bas (liste des entrées validées)
- [ ] Fonctionne sur mobile Chrome Android (75%+ des téléphones à Dakar)

**Estimation :** L
**Priorité :** Critique

### Flow technique
1. DESIGNER crée la page scan avec Alpine.js + jsQR ou `html5-qrcode`
2. Alpine.js capture le flux caméra et décode le QR
3. DEV branche sur `POST /api/v1/checkins/validate` (token gym + qr_token)
4. Réponse `200` → afficher vert | `422` → afficher rouge + raison

### API
```
POST /api/v1/checkins/validate
Headers: Authorization: Bearer {gym_api_token}
Body: { "qr_token": "uuid" }

200 OK  → { "data": { "status": "valid", "member_name": "Moussa Diop", ... } }
422     → { "message": "Abonnement expiré" }
```

---

## S3-T4 + S3-T5 — Carte Leaflet des salles

### User Story
En tant que visiteur ou membre,
Je veux voir toutes les salles partenaires sur une carte de Dakar,
Afin de choisir la salle la plus proche de chez moi.

**Critères d'acceptation :**
- [ ] Carte Leaflet + OpenStreetMap centrée sur Dakar (lat: 14.72, lng: -17.45)
- [ ] Marqueurs personnalisés rouge FitPass (`#FF3B3B`) pour chaque salle active
- [ ] Clic sur marqueur → popup : nom, adresse, activités, bouton "Voir détail"
- [ ] Filtres par activité (musculation, yoga, cardio, natation, arts martiaux)
- [ ] Barre de recherche par nom de salle
- [ ] Responsive : 375px (carte pleine largeur) + 1280px (carte + liste)
- [ ] Données mises en cache Redis 1h (API GeoJSON)
- [ ] Carte visible sans abonnement (page publique)

**Estimation :** L (carte) + S (API GeoJSON)
**Priorité :** Haute

### API GeoJSON (S3-T5)
```
GET /api/v1/gyms/geojson
Cache-Control: max-age=3600

Réponse :
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "geometry": { "type": "Point", "coordinates": [-17.44, 14.69] },
      "properties": {
        "id": "uuid",
        "name": "City Sport Almadies",
        "address": "Route des Almadies, Dakar",
        "activities": ["musculation", "cardio"],
        "phone": "+221 77 000 0000"
      }
    }
  ]
}
```

### Cache Redis
```php
// GymController::geojson()
return Cache::remember('gyms.geojson', 3600, function () {
    return GymResource::collection(Gym::active()->with('activities')->get());
});
```

---

## S3-T6 — Dashboard admin

### User Story
En tant qu'administrateur,
Je veux voir les métriques clés du business en temps réel,
Afin de piloter FitPass et détecter les anomalies.

**Critères d'acceptation :**
- [ ] KPIs en haut : membres actifs, revenus du mois, abonnements actifs, checkins du jour
- [ ] Tableau membres : nom, plan, statut, date d'expiration, dernière entrée
- [ ] Tableau revenus : liste paiements avec statut (completed/failed/refunded)
- [ ] Tableau salles : nom, nbr checkins 30j, statut actif/inactif
- [ ] Filtres : par date, par statut, par plan
- [ ] Export CSV membres et paiements (bouton)
- [ ] Actions admin : désactiver membre, annuler abonnement, rembourser paiement
- [ ] 2FA requis pour toutes les actions destructives

**Estimation :** L
**Priorité :** Haute

### Routes
- `GET /admin/dashboard` → vue principale
- `GET /admin/members` → liste membres
- `GET /admin/gyms` → liste + CRUD salles
- `GET /admin/payments` → historique paiements

---

## S3-T7 — CRUD admin salles

### User Story
En tant qu'administrateur,
Je veux ajouter et modifier les salles partenaires,
Afin de maintenir la carte à jour.

**Critères d'acceptation :**
- [ ] Formulaire : nom, adresse, téléphone, email, activités (checkbox), coordonnées GPS
- [ ] Sélecteur de coordonnées via clic sur une mini-carte Leaflet intégrée dans le formulaire
- [ ] Upload photo de la salle (optionnel)
- [ ] Toggle actif/inactif (n'apparaît pas sur la carte si inactif)
- [ ] Génération automatique du `api_token` pour la borne de scan
- [ ] Affichage du `api_token` (avec bouton copier) pour configuration borne

**Estimation :** M
**Priorité :** Haute

---

## S3-T8 — Dashboard gym_owner

### User Story
En tant que propriétaire de salle,
Je veux voir les statistiques de fréquentation de ma salle,
Afin de connaître l'activité FitPass dans mon établissement.

**Critères d'acceptation :**
- [ ] Vue du jour : liste des membres entrés aujourd'hui (heure + nom)
- [ ] Compteur total entrées du mois
- [ ] Graphique simple fréquentation 30 derniers jours (barres)
- [ ] Filtre par date
- [ ] Ne voit QUE ses propres salles (règle métier critique)
- [ ] Accessible sur mobile (gym owner utilise son téléphone)

**Estimation :** M
**Priorité :** Haute

### Règle sécurité
```php
// GymOwnerController — toujours filtrer par gym du owner connecté
$checkins = GymCheckin::whereHas('gym', fn($q) =>
    $q->where('owner_id', auth()->id())
)->with(['user', 'gym'])->latest()->paginate(20);
```

---

## S3-P1 + S3-P2 — Tests E2E recette mobile

### Critères QA
- [ ] Parcours complet membre : login → voir plans → s'abonner → QR code (375px)
- [ ] Scan QR valide : résultat vert < 1 seconde (375px)
- [ ] Scan QR invalide (expiré) : résultat rouge + message (375px)
- [ ] Navigation entre pages sans erreur JS console
- [ ] Formulaires utilisables au doigt (target size min 44px)
- [ ] Carte Leaflet zoomable et scrollable au doigt

---

## S3-P3 — Performance

### Critères
- [ ] PageSpeed Insights mobile > 90 sur toutes les pages principales
- [ ] Réponse API `GET /api/v1/subscriptions/active` < 300ms
- [ ] Réponse API `POST /api/v1/checkins/validate` < 500ms (critique pour scan)
- [ ] `GET /api/v1/gyms/geojson` servi depuis cache Redis (< 50ms)
- [ ] 0 requête N+1 (vérification avec Laravel Debugbar)

---

## Dépendances

### Dépend de (Sprint 2)
- ✅ `CheckinService::validate()` — API scan fonctionnelle
- ✅ `SubscriptionService` — abonnements actifs requêtables
- ✅ `POST /api/v1/checkins/validate` — endpoint borne de scan
- ✅ GymResource + CheckinResource + SubscriptionResource

### Bloque (Sprint 4)
- Landing page publique (S4-T1) dépend de la carte Leaflet (S3-T4)
- Démo client dépend des dashboards

---

## Hors scope Sprint 3

- Paiement PayTech réel (toujours FakePaymentService jusqu'à clé sandbox)
- SMS Twilio réel (toujours FakeSmsService jusqu'à credentials)
- App mobile native
- Réservation créneaux horaires
- Avis et notation salles
- Export PDF

---

## Questions ouvertes

- [ ] Bibliothèque scan QR côté client : `html5-qrcode` ou `jsQR` + Alpine.js ?
- [ ] Graphiques dashboard : `Chart.js` ou SVG natif Blade ?
- [ ] Upload photos salles : stockage local ou S3 DigitalOcean Spaces ?
- [ ] Dashboard admin : pagination 15 ou 25 membres par page par défaut ?

---

## Definition of Done Sprint 3

- [ ] `php artisan test` → 0 erreur (tests Sprint 2 + nouveaux tests Sprint 3)
- [ ] Parcours membre complet sur mobile 375px (login → QR code)
- [ ] Scanner QR : retour visuel < 1 seconde
- [ ] Carte Leaflet : salles visibles, filtres opérationnels
- [ ] Dashboards admin + gym_owner : données réelles affichées
- [ ] PageSpeed mobile > 90
- [ ] 0 bug visuel sur 375px / 768px / 1280px
- [ ] /accessibility-review QA passé sur les pages principales
