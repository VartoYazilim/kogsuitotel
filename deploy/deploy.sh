#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────
# Koğ Suit Otel — Production Deploy Script
# ─────────────────────────────────────────────────────────────────────
# VPS üzerinde /home/deploy/deploy.sh olarak çalıştır.
# Cron veya GitHub Actions ile otomatik çağrılabilir, ya da manuel.
#
# Önkoşullar:
#   - Repo /var/www/kogsuitotel altında clone edilmiş
#   - /home/deploy/.env (prod credentials) hazır
#   - Composer + Node 20 + PHP 8.3-FPM kurulu
#   - php-fpm sudo reload yetkisi 'deploy' kullanıcısına verilmiş
# ─────────────────────────────────────────────────────────────────────

set -euo pipefail

PROJECT_DIR="/var/www/kogsuitotel"
NGINX_RELOAD="sudo systemctl reload nginx"
PHP_FPM_RELOAD="sudo systemctl reload php8.3-fpm"

cd "$PROJECT_DIR"

echo "── Git pull ──"
git fetch --quiet
git reset --hard origin/main

echo "── Composer dependencies (prod, optimize) ──"
composer install --no-dev --optimize-autoloader --no-interaction --quiet

echo "── DB migration (force) ──"
php artisan migrate --force --no-interaction

echo "── Cache: config, route, view, event ──"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "── Filament cache ──"
php artisan filament:optimize

echo "── Storage symlink (idempotent) ──"
php artisan storage:link || true

echo "── Frontend build ──"
npm ci --prefer-offline --no-audit --silent
npm run build --silent

echo "── PHP-FPM reload ──"
$PHP_FPM_RELOAD

echo "── Nginx reload (opsiyonel) ──"
$NGINX_RELOAD

echo "── Deploy başarılı ✓ ──"
echo "Mevcut commit: $(git rev-parse --short HEAD)"
