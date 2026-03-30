# Guide VPS — FitPass Dakar
Setup complet DigitalOcean Ubuntu 24 + déploiement initial

---

## Prérequis

- Droplet DigitalOcean Ubuntu 24 LTS (recommandé : 2 vCPU / 4 GB RAM / 80 GB SSD)
- Domaine `fitpass.sn` pointant vers l'IP du Droplet (enregistrement A)
- Accès SSH root initial

---

## 1. Sécurisation initiale du VPS

```bash
# Connexion initiale
ssh root@<IP_DROPLET>

# Mise à jour système
apt update && apt upgrade -y

# Créer utilisateur dédié
adduser fitpass
usermod -aG sudo fitpass

# Copier la clé SSH de root vers fitpass
rsync --archive --chown=fitpass:fitpass ~/.ssh /home/fitpass

# Désactiver connexion SSH root
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
systemctl restart sshd

# Firewall UFW
ufw allow OpenSSH
ufw allow 80
ufw allow 443
ufw enable
```

---

## 2. Stack serveur

```bash
# PHP 8.3 + extensions
add-apt-repository ppa:ondrej/php -y
apt install -y php8.3 php8.3-fpm php8.3-mysql php8.3-redis \
    php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip

# Nginx
apt install -y nginx

# MySQL 8
apt install -y mysql-server
mysql_secure_installation

# Redis
apt install -y redis-server
systemctl enable redis-server

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

---

## 3. MySQL — Création base de données

```sql
-- Connexion : mysql -u root -p
CREATE DATABASE fitpass_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fitpass_user'@'localhost' IDENTIFIED BY '<MOT_DE_PASSE_FORT>';
GRANT ALL PRIVILEGES ON fitpass_prod.* TO 'fitpass_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 4. Déploiement initial du code

```bash
# Connexion avec l'utilisateur fitpass
ssh fitpass@<IP_DROPLET>

# Cloner le repo
mkdir -p /var/www
cd /var/www
git clone git@github.com:<TON_USERNAME>/fitpass.git
cd fitpass

# Copier et configurer .env
cp .env.production.example .env
nano .env   # Remplir toutes les valeurs

# Installation
composer install --no-dev --optimize-autoloader
npm ci --omit=dev
npm run build
php8.3 artisan key:generate
php8.3 artisan migrate --force
php8.3 artisan config:cache
php8.3 artisan route:cache
php8.3 artisan view:cache

# Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/fitpass
```

---

## 5. Nginx — Config vhost

```bash
# /etc/nginx/sites-available/fitpass
server {
    listen 80;
    server_name fitpass.sn www.fitpass.sn;
    root /var/www/fitpass/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 10M;
}
```

```bash
# Activer le vhost
ln -s /etc/nginx/sites-available/fitpass /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

---

## 6. SSL Let's Encrypt (HTTPS)

```bash
apt install -y certbot python3-certbot-nginx
certbot --nginx -d fitpass.sn -d www.fitpass.sn
# Renouvellement automatique — déjà configuré par certbot
```

---

## 7. Queue Worker (Supervisor)

```bash
apt install -y supervisor

# /etc/supervisor/conf.d/fitpass-worker.conf
[program:fitpass-worker]
process_name=%(program_name)s_%(process_num)02d
command=php8.3 /var/www/fitpass/artisan queue:work redis --queue=notifications,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/fitpass/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
supervisorctl reread
supervisorctl update
supervisorctl start fitpass-worker:*
```

---

## 8. Cron Laravel (Scheduler)

```bash
# Ajouter au crontab de www-data
crontab -u www-data -e

# Ajouter cette ligne :
* * * * * cd /var/www/fitpass && php8.3 artisan schedule:run >> /dev/null 2>&1
```

---

## 9. Secrets GitHub — Activer le déploiement automatique

Dans le repo GitHub → **Settings → Secrets and variables → Actions** :

| Secret | Valeur |
|--------|--------|
| `VPS_HOST` | IP du Droplet |
| `VPS_USER` | `fitpass` |
| `VPS_SSH_KEY` | Contenu de `~/.ssh/id_rsa` (clé privée) |
| `VPS_PORT` | `22` |
| `VPS_APP_PATH` | `/var/www/fitpass` |

Dans **Settings → Variables → Actions** :

| Variable | Valeur |
|----------|--------|
| `DEPLOY_ENABLED` | `true` |

---

## 10. Vérifications post-déploiement

```bash
# APP_DEBUG=false vérifié
grep APP_DEBUG /var/www/fitpass/.env

# Test erreur 500 — pas de stack trace visible
curl -s https://fitpass.sn/force-error | grep -i "stack\|trace\|exception"

# Queue worker actif
supervisorctl status

# Cron actif
crontab -u www-data -l

# SSL valide
curl -I https://fitpass.sn

# Redis connecté
php8.3 /var/www/fitpass/artisan tinker --execute="Cache::put('test', 1); echo Cache::get('test');"
```

---

## Checklist finale avant lancement public

- [ ] `APP_DEBUG=false` dans `.env`
- [ ] SSL actif et redirige HTTP → HTTPS
- [ ] Queue worker supervisor actif (2 processus)
- [ ] Cron scheduler actif
- [ ] `php8.3 artisan test` → 100% passant en CI
- [ ] Google Analytics installé sur la landing
- [ ] Backup MySQL configuré (DigitalOcean Managed Backups ou cron mysqldump)
- [ ] `composer audit` → 0 vulnérabilité
- [ ] Clés PayTech + Twilio configurées dans `.env`
- [ ] `PAYTECH_SANDBOX=false` en production
