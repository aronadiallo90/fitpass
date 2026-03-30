#!/bin/bash
# deploy.sh — Script de déploiement FitPass sur VPS DigitalOcean Ubuntu 24
# Usage : bash scripts/deploy.sh
# À exécuter sur le VPS dans /var/www/fitpass

set -euo pipefail

APP_PATH="/var/www/fitpass"
PHP="php8.3"
COMPOSER="composer"

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║   FitPass Dakar — Deploy $(date '+%Y-%m-%d %H:%M')  ║"
echo "╚══════════════════════════════════════════╝"
echo ""

cd "$APP_PATH"

# ── 1. Maintenance ──────────────────────────────────────────────────────────
echo "→ [1/9] Mode maintenance..."
$PHP artisan down --retry=10

# ── 2. Git pull ─────────────────────────────────────────────────────────────
echo "→ [2/9] Mise à jour du code..."
git fetch origin main
git reset --hard origin/main

# ── 3. Dépendances PHP ──────────────────────────────────────────────────────
echo "→ [3/9] Installation Composer (prod)..."
$COMPOSER install --no-interaction --no-dev --prefer-dist --optimize-autoloader

# ── 4. Assets frontend ──────────────────────────────────────────────────────
echo "→ [4/9] Build assets Vite..."
npm ci --omit=dev
npm run build

# ── 5. Migrations ───────────────────────────────────────────────────────────
echo "→ [5/9] Migrations..."
$PHP artisan migrate --force

# ── 6. Cache Laravel ────────────────────────────────────────────────────────
echo "→ [6/9] Cache config/routes/vues..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

# ── 7. Permissions ──────────────────────────────────────────────────────────
echo "→ [7/9] Permissions storage..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# ── 8. Queue workers ────────────────────────────────────────────────────────
echo "→ [8/9] Redémarrage queue workers..."
$PHP artisan queue:restart

# ── 9. Désactiver maintenance ───────────────────────────────────────────────
echo "→ [9/9] Remise en ligne..."
$PHP artisan up

echo ""
echo "✓ Deploy terminé avec succès — $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
