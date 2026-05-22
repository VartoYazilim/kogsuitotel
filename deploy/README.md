# Deploy Klasörü

Production VPS'e kurulum + deploy için template ve script'ler.

## Dosyalar

- **`deploy.sh`** — Git pull + composer + migrate + cache + npm build + reload script'i. VPS'te `/home/deploy/deploy.sh` olarak yerleştirilir.
- **`nginx.conf.example`** — Nginx vhost yapılandırması. `/etc/nginx/sites-available/kogsuitotel.com` olarak yerleştirilir.

## Kurulum Sırası (VPS'te)

> Detaylı plan için repo kökünde **`CLAUDE.md`** Section 8 ve Section 11 (Faz 3) bölümlerini oku.

```bash
# 1. VPS hardening (SSH key, UFW, fail2ban, unattended-upgrades)
# 2. Base stack
sudo apt update && sudo apt install -y nginx php8.3-fpm php8.3-{mbstring,xml,mysql,curl,zip,gd,intl,bcmath,sqlite3} mariadb-server redis-server composer nodejs npm

# 3. Repo clone (deploy kullanıcısı olarak)
sudo mkdir -p /var/www && sudo chown deploy:deploy /var/www
cd /var/www && git clone <repo-url> kogsuitotel
cd kogsuitotel

# 4. .env hazırla (production credentials)
cp .env.example .env.production
nano .env.production
# APP_ENV=production, APP_DEBUG=false, DB_CONNECTION=mariadb, vs.
cp .env.production .env

# 5. composer + npm + migrate
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --force --seed

# 6. Permissions
sudo chown -R deploy:www-data /var/www/kogsuitotel
sudo chmod -R 775 storage bootstrap/cache

# 7. Storage symlink
php artisan storage:link

# 8. Nginx vhost
sudo cp deploy/nginx.conf.example /etc/nginx/sites-available/kogsuitotel.com
sudo ln -s /etc/nginx/sites-available/kogsuitotel.com /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx

# 9. Cloudflare Origin Certificate
# Cloudflare Dashboard → SSL/TLS → Origin Server → Create Certificate
# Çıkan PEM + Key dosyalarını /etc/ssl/certs/ ve /etc/ssl/private/'a koy

# 10. Deploy script'i kur
cp deploy/deploy.sh /home/deploy/deploy.sh
chmod +x /home/deploy/deploy.sh

# 11. Cron — günlük backup (Spatie Backup paketi sonra)
# 0 3 * * * /home/deploy/backup.sh
```

## Sonraki Deploy'lar

```bash
ssh deploy@<vps-ip>
/home/deploy/deploy.sh
```

## Backup

Faz 3'ün son adımı: **Spatie Laravel Backup** kurulumu.

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
# config/backup.php düzenle, Backblaze B2 credentials .env'e
# Cron: 0 3 * * * cd /var/www/kogsuitotel && php artisan backup:run
```

Backup hedef: **Backblaze B2** veya **Wasabi** (~€5-10/yıl).

## Health Check

```bash
# Uptime test (cron, dakikada 1)
curl -fsS https://kogsuitotel.com/ > /dev/null || echo "DOWN" | mail -s "kogsuitotel.com down" admin@vartoyazilim.com
```

Veya **UptimeRobot** (ücretsiz 5 dk interval).
