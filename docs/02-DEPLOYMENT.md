# Production Deployment Guide

This guide walks you through deploying the HILOTEC website from a bare Ubuntu server to a fully running, hardened production environment. It is written for sysadmins and developers who are comfortable with the Linux command line but may not have deployed a Laravel application before.

> **Note:** The hardened deployment files (`deploy.sh`, `nginx-security.conf`, `php-hardening.ini`, `.env.production.example`) are only available on the `master` branch. If deploying `design-v2`, either merge security hardening from master first (recommended), or manually apply the security configurations described in [09-SECURITY.md](09-SECURITY.md).

The project ships with several hardened deployment files that this guide references:

| File | Purpose | Branch |
|------|---------|--------|
| `deploy.sh` | Automated deployment script (git pull, clean, build, optimize, permissions) | `master` only |
| `nginx-security.conf` | Security rules to include in your Nginx server block | `master` only |
| `php-hardening.ini` | PHP security settings for FPM | `master` only |
| `.env.production.example` | Production `.env` template with security defaults | `master` only |
| `public/.htaccess` | Hardened Apache rewrite rules (HTTPS, block PHP in assets, block scanners) | `master` only (standard Laravel on `design-v2`) |

---

## Table of Contents

1. [Server Requirements](#1-server-requirements)
2. [Pre-deployment: Build Assets Locally](#2-pre-deployment-build-assets-locally)
3. [Server Setup (Ubuntu 22.04 / 24.04)](#3-server-setup-ubuntu-2204--2404)
4. [Application Deployment](#4-application-deployment)
5. [Nginx Configuration](#5-nginx-configuration)
6. [Apache Configuration (Alternative)](#6-apache-configuration-alternative)
7. [SSL with Certbot](#7-ssl-with-certbot)
8. [Using the deploy.sh Script](#8-using-the-deploysh-script)
9. [Post-deployment Verification Checklist](#9-post-deployment-verification-checklist)
10. [Updating the Website](#10-updating-the-website)
11. [GDPR: Self-hosting Google Fonts](#11-gdpr-self-hosting-google-fonts)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Server Requirements

### Minimum Hardware

- **CPU:** 1 vCPU (2 recommended)
- **RAM:** 1 GB (2 GB recommended)
- **Disk:** 10 GB free (the application itself is under 100 MB; budget extra for database, logs, and backups)
- **Network:** Public IPv4 with ports 80 and 443 open

### Software

| Software | Minimum Version | Notes |
|----------|----------------|-------|
| PHP | 8.2+ | With required extensions (see below) |
| Composer | 2.x | PHP dependency manager |
| Node.js | 18+ | Only needed if building assets on the server |
| MySQL | 8.0+ | Or PostgreSQL 14+ |
| Nginx | 1.18+ | Recommended. Apache 2.4+ is also supported. |
| Git | 2.x | For deployment |
| Certbot | Latest | For free SSL via Let's Encrypt |

### Required PHP Extensions

```
php-cli php-fpm php-mbstring php-xml php-curl php-zip
php-bcmath php-intl php-gd php-mysql php-tokenizer
php-fileinfo php-dom php-sqlite3
```

> **Note:** `php-sqlite3` is only needed if you use SQLite during initial setup or testing. For production with MySQL, `php-mysql` is the critical one. If using PostgreSQL, install `php-pgsql` instead.

---

## 2. Pre-deployment: Build Assets Locally

The recommended approach is to build frontend assets on your development machine and commit the `public/build/` directory to git. This means **Node.js is not needed on the production server**.

### Why build locally?

- Eliminates Node.js as a production dependency
- Faster deployments (no `npm install` + `npm run build` on every deploy)
- Smaller attack surface on the server
- Reproducible builds from your development environment

### How to do it

**Step 1:** On your development machine, build the production assets:

```bash
npm install
npm run build
```

This creates optimized, hashed files in `public/build/` (CSS, JS, and a `manifest.json`).

**Step 2:** Remove `/public/build` from `.gitignore`:

The project's `.gitignore` currently includes `/public/build`. To commit built assets, remove or comment out that line:

```gitignore
# /public/build    <-- comment this out
```

**Step 3:** Commit the built assets:

```bash
git add public/build/
git commit -m "Add production build assets"
git push
```

> **Warning:** Once you commit `public/build/`, remember to rebuild and re-commit whenever you change CSS, JS, or Blade templates. Forgetting this step means deploying stale frontend assets.

### What the deploy.sh script does

The `deploy.sh` script automatically detects whether `public/build` is in `.gitignore`. If it is, the script runs `npm ci && npm run build` on the server. If it is not (i.e., you committed the build), the script skips the Node.js build entirely:

```bash
# From deploy.sh:
if grep -q '/public/build' .gitignore 2>/dev/null; then
    npm ci
    npm run build
else
    echo "Skipping frontend build (build/ is tracked in git)"
fi
```

---

## 3. Server Setup (Ubuntu 22.04 / 24.04)

All commands assume you are logged in as root or using `sudo`.

### 3.1 System Update

```bash
apt update && apt upgrade -y
```

### 3.2 Install PHP 8.3 and Extensions

On Ubuntu 24.04, PHP 8.3 is available from the default repositories. On Ubuntu 22.04, add the Ondrej PPA first:

```bash
# Ubuntu 22.04 only — add the PPA:
apt install -y software-properties-common
add-apt-repository -y ppa:ondrej/php
apt update

# Install PHP 8.3 and all required extensions:
apt install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-gd \
    php8.3-mysql \
    php8.3-tokenizer \
    php8.3-fileinfo \
    php8.3-dom \
    php8.3-sqlite3
```

> **Note:** Replace `8.3` with `8.2` or `8.4` throughout this guide if you are targeting a different PHP version. The application requires PHP 8.2 at minimum.

### 3.3 Install Composer

```bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

Verify:

```bash
composer --version
```

### 3.4 Install MySQL

```bash
apt install -y mysql-server
mysql_secure_installation
```

During `mysql_secure_installation`:
- Set a strong root password
- Remove anonymous users: **Yes**
- Disallow root login remotely: **Yes**
- Remove test database: **Yes**
- Reload privilege tables: **Yes**

Create the application database and user:

```sql
mysql -u root -p
```

```sql
CREATE DATABASE hilotec CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'hilotec'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON hilotec.* TO 'hilotec'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> **Warning:** Use a strong, random password. Never use `password`, `123456`, or the examples shown in documentation. Generate one with: `openssl rand -base64 32`

### 3.5 Install Nginx

```bash
apt install -y nginx
systemctl enable nginx
```

### 3.6 Install Node.js (only if building on server)

If you chose NOT to commit `public/build/` to git, install Node.js:

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs
```

Verify:

```bash
node --version   # Should be 18+
npm --version
```

### 3.7 Install Certbot

```bash
apt install -y certbot python3-certbot-nginx
```

### 3.8 Install Git

```bash
apt install -y git
```

### 3.9 Create the Web User

If you are not using a platform like Laravel Forge, create a dedicated deploy user:

```bash
adduser deploy
usermod -aG www-data deploy
```

---

## 4. Application Deployment

### 4.1 Clone the Repository

```bash
# As the deploy user (or as root into /var/www):
su - deploy
mkdir -p /var/www
cd /var/www
git clone https://github.com/your-org/hilotec.git hilotec.com
cd hilotec.com
```

### 4.2 Install PHP Dependencies

```bash
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
```

The `--no-dev` flag excludes development packages (PHPUnit, Faker, Pint, Sail, etc.), reducing the vendor directory size and attack surface.

### 4.3 Configure the Environment

Copy the production environment template:

> **Note:** `.env.production.example` only exists on the `master` branch. On `design-v2`, use `.env.example` and manually configure production values.

```bash
cp .env.production.example .env
```

Now edit `.env` with your actual values:

```bash
nano .env
```

Here is every important variable explained:

```ini
# ---------------------------------------------------------------------------
# APPLICATION
# ---------------------------------------------------------------------------

# MUST be "production" — controls error display, HTTPS forcing, debug mode
APP_ENV=production

# MUST be false — setting to true exposes stack traces, queries, and secrets
APP_DEBUG=false

# Your full production URL with https://
# Used for generating absolute URLs, asset paths, and CSRF validation
APP_URL=https://www.hilotec.com

# Generate with: php artisan key:generate
# This encrypts sessions, cookies, and all encrypted data.
# NEVER share this key. If compromised, rotate immediately.
APP_KEY=

# ---------------------------------------------------------------------------
# DATABASE
# ---------------------------------------------------------------------------

# Use mysql for production (not sqlite)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hilotec
DB_USERNAME=hilotec
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

# ---------------------------------------------------------------------------
# SESSION SECURITY
# ---------------------------------------------------------------------------

# Store sessions in the database (survives PHP-FPM restarts, auditable)
SESSION_DRIVER=database

# Session timeout in minutes (120 = 2 hours)
SESSION_LIFETIME=120

# Encrypt session data at rest — prevents reading session files even if leaked
SESSION_ENCRYPT=true

# Only send session cookie over HTTPS — prevents session hijacking on HTTP
SESSION_SECURE_COOKIE=true

# Strict same-site policy — prevents CSRF via cross-origin requests
SESSION_SAME_SITE=strict

# ---------------------------------------------------------------------------
# LOGGING
# ---------------------------------------------------------------------------

# Stack = write to daily log files
LOG_CHANNEL=stack

# Only log errors and above in production (not debug/info noise)
LOG_LEVEL=error

# ---------------------------------------------------------------------------
# ADMIN ACCESS CONTROL (master branch only)
# ---------------------------------------------------------------------------

# Comma-separated email addresses allowed to access /admin
# The User model's canAccessPanel() checks this list
# Note: This variable is not used on the design-v2 branch (any authenticated user can access admin)
ADMIN_EMAILS=admin@hilotec.com

# ---------------------------------------------------------------------------
# MAIL (for security alerts and contact form)
# ---------------------------------------------------------------------------

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-email-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@hilotec.com
MAIL_FROM_NAME="HILOTEC"

# Recipient for security audit alerts
MAIL_TO=admin@hilotec.com

# ---------------------------------------------------------------------------
# RECAPTCHA (optional, for contact form protection)
# ---------------------------------------------------------------------------

# Get keys from https://www.google.com/recaptcha/admin/create
# Choose reCAPTCHA v3 and add your domain
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=

# ---------------------------------------------------------------------------
# CACHE & QUEUE
# ---------------------------------------------------------------------------

CACHE_STORE=database
QUEUE_CONNECTION=database

# ---------------------------------------------------------------------------
# VITE
# ---------------------------------------------------------------------------

VITE_APP_NAME="HILOTEC"
```

### 4.4 Generate Application Key

```bash
php artisan key:generate
```

This writes a random `APP_KEY=base64:...` value into your `.env`. This key encrypts sessions, cookies, and any data you encrypt with Laravel's `encrypt()` helper.

> **Warning:** Back up this key securely. If you lose it, all encrypted data (sessions, encrypted database fields) becomes unreadable. If you rotate it, all users will be logged out and any encrypted data must be re-encrypted.

### 4.5 Run Migrations and Seed

```bash
php artisan migrate --force
php artisan db:seed --force
```

The `--force` flag is required when `APP_ENV=production` because Laravel protects against accidental database changes.

The seeder populates all website content (services, references, team members, settings, pages, etc.).

### 4.6 Create the Storage Symlink

```bash
php artisan storage:link
```

This creates `public/storage -> storage/app/public`, which allows uploaded files (if any) to be web-accessible.

### 4.7 Set File Permissions

```bash
# All project files owned by deploy user, readable by www-data
chown -R deploy:www-data /var/www/hilotec.com

# Directories: rwxr-xr-x (owner writes, group/others read+execute)
find /var/www/hilotec.com -type d -exec chmod 755 {} \;

# Files: rw-r--r-- (owner writes, group/others read)
find /var/www/hilotec.com -type f -exec chmod 644 {} \;

# Storage and cache MUST be writable by PHP-FPM (www-data)
chmod -R 775 /var/www/hilotec.com/storage
chmod -R 775 /var/www/hilotec.com/bootstrap/cache

# .env: readable only by owner and group (not world-readable)
chmod 640 /var/www/hilotec.com/.env
```

### 4.8 Apply PHP Security Hardening

> **Note:** `php-hardening.ini` only exists on the `master` branch. On `design-v2`, skip this step or merge the file from master first.

Copy the project's PHP hardening configuration:

```bash
cp /var/www/hilotec.com/php-hardening.ini /etc/php/8.3/fpm/conf.d/99-security.ini
```

> **Important:** If you need Composer on the server (e.g., for `deploy.sh`), note that the hardening config disables `proc_open` via `disable_functions`. Composer requires `proc_open`. Two approaches:
>
> 1. **Recommended:** Run Composer via CLI (which uses `/etc/php/8.3/cli/php.ini`), and only apply the hardening to FPM (`/etc/php/8.3/fpm/conf.d/`). This way, web requests are hardened but CLI tools work normally.
> 2. **Alternative:** Remove `proc_open` from the `disable_functions` list if you must run Composer through FPM (not recommended).

If your project needs `open_basedir` restriction, uncomment and customize this line in `99-security.ini`:

```ini
open_basedir = /var/www/hilotec.com:/tmp:/var/lib/php/sessions
```

Restart PHP-FPM to apply:

```bash
systemctl restart php8.3-fpm
```

### 4.9 Laravel Optimizations

Cache configuration, routes, and views for faster request handling:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> **Note:** After running `config:cache`, the `.env` file is no longer read at runtime -- all config values are loaded from the cached file. If you change `.env`, you **must** re-run `php artisan config:cache`.

### 4.10 Set Up the Cron Scheduler

> **Note:** The security audit schedule only exists on the `master` branch. On `design-v2`, the cron scheduler is still useful for other Laravel scheduled tasks, but `security:audit` is not available.

The application schedules a security audit every 6 hours. Add the Laravel scheduler to cron:

```bash
crontab -e -u deploy
```

Add this line:

```
* * * * * cd /var/www/hilotec.com && php artisan schedule:run >> /dev/null 2>&1
```

This runs the Laravel scheduler every minute, which in turn runs `security:audit --fix --notify` every 6 hours (as defined in `routes/console.php`).

---

## 5. Nginx Configuration

### 5.1 Complete Server Block

Create the site configuration:

```bash
nano /etc/nginx/sites-available/hilotec.com
```

Paste the following configuration:

```nginx
# =============================================================================
# Rate limiting zones — add to /etc/nginx/nginx.conf inside http { }
# =============================================================================
# limit_req_zone $binary_remote_addr zone=admin_login:10m rate=5r/m;
# limit_req_zone $binary_remote_addr zone=general:10m rate=30r/s;
# limit_conn_zone $binary_remote_addr zone=conn_limit:10m;

# =============================================================================
# HTTP -> HTTPS redirect
# =============================================================================
server {
    listen 80;
    listen [::]:80;
    server_name hilotec.com www.hilotec.com;

    # Allow certbot ACME challenge
    location /.well-known/acme-challenge/ {
        root /var/www/hilotec.com/public;
    }

    # Redirect everything else to HTTPS
    location / {
        return 301 https://www.hilotec.com$request_uri;
    }
}

# =============================================================================
# Redirect bare domain to www (or vice versa — pick one canonical URL)
# =============================================================================
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name hilotec.com;

    ssl_certificate     /etc/letsencrypt/live/hilotec.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hilotec.com/privkey.pem;

    return 301 https://www.hilotec.com$request_uri;
}

# =============================================================================
# Main HTTPS server block
# =============================================================================
server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name www.hilotec.com;

    root /var/www/hilotec.com/public;
    index index.php;

    # --- SSL Configuration ---
    ssl_certificate     /etc/letsencrypt/live/hilotec.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/hilotec.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;
    ssl_session_tickets off;

    # OCSP Stapling
    ssl_stapling on;
    ssl_stapling_verify on;
    resolver 8.8.8.8 8.8.4.4 valid=300s;
    resolver_timeout 5s;

    # --- Include security rules (master branch only; file does not exist on design-v2) ---
    include /var/www/hilotec.com/nginx-security.conf;

    # --- Gzip Compression ---
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 1000;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/json
        application/javascript
        application/xml
        application/rss+xml
        application/atom+xml
        image/svg+xml
        font/woff2;

    # --- Static file caching ---
    # Vite-built assets have content hashes in filenames, so they can
    # be cached aggressively. The hash changes when content changes.
    location /build/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Images, fonts, and other static assets
    location ~* \.(jpg|jpeg|png|gif|ico|svg|webp|avif|woff|woff2|ttf|eot|otf|css|js|map)$ {
        expires 30d;
        add_header Cache-Control "public";
        access_log off;
    }

    # --- Laravel front controller ---
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # --- PHP-FPM ---
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;

        # Security: prevent path traversal via crafted URLs
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_index index.php;

        # Performance
        fastcgi_buffering on;
        fastcgi_buffer_size 16k;
        fastcgi_buffers 16 16k;
    }

    # --- Deny access to Laravel internals ---
    location ~ /\.(?!well-known) {
        deny all;
    }

    # --- Logging ---
    access_log /var/log/nginx/hilotec.com-access.log;
    error_log  /var/log/nginx/hilotec.com-error.log;
}
```

### 5.2 Add Rate Limiting Zones

Edit the main Nginx config to add rate limiting zones inside the `http {}` block:

```bash
nano /etc/nginx/nginx.conf
```

Add these lines inside `http { }`:

```nginx
# Rate limiting for HILOTEC
limit_req_zone $binary_remote_addr zone=admin_login:10m rate=5r/m;
limit_req_zone $binary_remote_addr zone=general:10m rate=30r/s;
limit_conn_zone $binary_remote_addr zone=conn_limit:10m;
```

### 5.3 Enable the Site

```bash
ln -s /etc/nginx/sites-available/hilotec.com /etc/nginx/sites-enabled/

# Remove the default site
rm -f /etc/nginx/sites-enabled/default

# Test the configuration
nginx -t

# Reload
systemctl reload nginx
```

> **Warning:** Always run `nginx -t` before reloading. A syntax error will take down all sites on the server if you restart without testing.

---

## 6. Apache Configuration (Alternative)

If you use Apache instead of Nginx, the project's `public/.htaccess` already contains hardened rewrite rules. You just need the VirtualHost configuration.

### 6.1 Enable Required Modules

```bash
a2enmod rewrite ssl headers expires
systemctl restart apache2
```

### 6.2 VirtualHost Configuration

```bash
nano /etc/apache2/sites-available/hilotec.com.conf
```

```apache
# HTTP -> HTTPS redirect
<VirtualHost *:80>
    ServerName hilotec.com
    ServerAlias www.hilotec.com

    # Allow certbot ACME challenge
    DocumentRoot /var/www/hilotec.com/public

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/.well-known/acme-challenge/
    RewriteRule ^ https://www.hilotec.com%{REQUEST_URI} [L,R=301]
</VirtualHost>

# HTTPS
<VirtualHost *:443>
    ServerName www.hilotec.com
    ServerAlias hilotec.com
    DocumentRoot /var/www/hilotec.com/public

    # SSL (paths set by certbot)
    SSLEngine On
    SSLCertificateFile    /etc/letsencrypt/live/hilotec.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/hilotec.com/privkey.pem

    # Modern SSL configuration
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLHonorCipherOrder off

    <Directory /var/www/hilotec.com/public>
        AllowOverride All
        Require all granted

        # The .htaccess file handles:
        # - HTTPS redirect
        # - Block hidden files (.env, .git)
        # - Block PHP in asset directories
        # - Block exploit patterns
        # - Block scanner paths
        # - Laravel front controller routing
    </Directory>

    # Deny access to non-public directories
    <DirectoryMatch "^/var/www/hilotec\.com/(app|bootstrap|config|database|resources|routes|storage|tests|vendor)">
        Require all denied
    </DirectoryMatch>

    # Static file caching
    <IfModule mod_expires.c>
        ExpiresActive On

        # Vite build assets (content-hashed filenames)
        <Directory /var/www/hilotec.com/public/build>
            ExpiresDefault "access plus 1 year"
            Header set Cache-Control "public, immutable"
        </Directory>

        # Images and fonts
        ExpiresByType image/jpeg "access plus 30 days"
        ExpiresByType image/png "access plus 30 days"
        ExpiresByType image/gif "access plus 30 days"
        ExpiresByType image/svg+xml "access plus 30 days"
        ExpiresByType image/webp "access plus 30 days"
        ExpiresByType font/woff2 "access plus 1 year"
        ExpiresByType font/woff "access plus 1 year"
        ExpiresByType text/css "access plus 30 days"
        ExpiresByType application/javascript "access plus 30 days"
    </IfModule>

    # Gzip compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/css
        AddOutputFilterByType DEFLATE application/javascript application/json
        AddOutputFilterByType DEFLATE text/xml application/xml
        AddOutputFilterByType DEFLATE image/svg+xml
        AddOutputFilterByType DEFLATE font/woff2
    </IfModule>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/hilotec.com-error.log
    CustomLog ${APACHE_LOG_DIR}/hilotec.com-access.log combined
</VirtualHost>
```

### 6.3 Enable the Site

```bash
a2ensite hilotec.com.conf
a2dissite 000-default.conf
apache2ctl configtest
systemctl reload apache2
```

---

## 7. SSL with Certbot

### 7.1 Obtain the Certificate

For Nginx:

```bash
certbot --nginx -d hilotec.com -d www.hilotec.com
```

For Apache:

```bash
certbot --apache -d hilotec.com -d www.hilotec.com
```

Certbot will:
1. Verify domain ownership via the ACME challenge
2. Download and install the certificate
3. Automatically configure your server block / VirtualHost
4. Set up auto-renewal

### 7.2 Verify Auto-renewal

```bash
certbot renew --dry-run
```

Certbot installs a systemd timer (or cron job) that renews certificates automatically before they expire. Verify the timer is active:

```bash
systemctl list-timers | grep certbot
```

### 7.3 Post-SSL Hardening

After certbot runs, verify your SSL configuration with an online scanner:

- https://www.ssllabs.com/ssltest/ -- aim for an A or A+ rating.

To get A+ on SSL Labs, the HSTS header must be present. On the `master` branch, the application's `SecurityHeaders` middleware already sends (this middleware does not exist on `design-v2`):

```
Strict-Transport-Security: max-age=31536000; includeSubDomains; preload
```

---

## 8. Using the deploy.sh Script

> **Note:** `deploy.sh` only exists on the `master` branch. On `design-v2`, use the manual deployment steps from [Section 10.1](#101-standard-update-procedure) instead, or merge `deploy.sh` from master.

The project includes `deploy.sh`, a hardened deployment script designed for CI/CD pipelines, Laravel Forge, or manual execution.

### 8.1 Initial Setup

**Step 1:** Edit the `SITE_DIR` variable at the top of the script:

```bash
nano /var/www/hilotec.com/deploy.sh
```

Change:

```bash
SITE_DIR="${FORGE_SITE_PATH:-/home/forge/yoursite.com}"
```

To:

```bash
SITE_DIR="${FORGE_SITE_PATH:-/var/www/hilotec.com}"
```

**Step 2:** Make it executable:

```bash
chmod +x /var/www/hilotec.com/deploy.sh
```

### 8.2 What the Script Does, Step by Step

| Step | Action | Why |
|------|--------|-----|
| 1 | `git fetch --depth=1 && git reset --hard` | Pulls only the latest commit (fast, minimal bandwidth) |
| 2 | `git clean -fd public/` | Removes any file in `public/` not tracked by git (except `build/` and `storage/`). This is the primary defense against injected web shells. |
| 3 | `find public/ ... -name '*.php' -delete` | Extra safety: explicitly removes PHP files from asset directories |
| 4 | `find public/ -mindepth 2 -name '.htaccess' -delete` | Removes `.htaccess` files injected into subdirectories |
| 5 | `composer install --no-dev --optimize-autoloader` | Installs production PHP dependencies with optimized class map |
| 6 | `npm ci && npm run build` (conditional) | Only runs if `public/build` is in `.gitignore`. Skipped if you committed build assets. |
| 7 | `php artisan config:cache` | Caches all configuration into a single file |
| 8 | `php artisan route:cache` | Compiles routes into a cached file for faster routing |
| 9 | `php artisan view:cache` | Pre-compiles all Blade templates |
| 10 | `php artisan migrate --force` | Runs any pending database migrations |
| 11 | `php artisan security:audit` | Scans for unauthorized files, injected code, permission issues |
| 12 | File permission lockdown | Sets `644` for files, `755` for directories, `775` for storage/cache, `640` for `.env` |

### 8.3 Running Manually

```bash
cd /var/www/hilotec.com
./deploy.sh
```

### 8.4 Using with Laravel Forge

If you deploy via Laravel Forge, the script automatically picks up the `FORGE_SITE_PATH`, `FORGE_SITE_BRANCH`, `FORGE_PHP`, and `FORGE_COMPOSER` environment variables. Just paste the following into Forge's deploy script field:

```bash
cd /home/forge/hilotec.com
./deploy.sh
```

### 8.5 Using with GitHub Actions

Example workflow (`.github/workflows/deploy.yml`):

```yaml
name: Deploy
on:
  push:
    branches: [master]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.SERVER_HOST }}
          username: deploy
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/hilotec.com
            ./deploy.sh
```

---

## 9. Post-deployment Verification Checklist

Run through this checklist after every fresh deployment:

### Application

- [ ] **Homepage loads:** `curl -I https://www.hilotec.com` returns `200 OK`
- [ ] **HTTPS redirect works:** `curl -I http://www.hilotec.com` returns `301` to `https://`
- [ ] **All routes work:** Visit `/angebot`, `/referenzen`, `/ueber-uns`, `/aktuelles`, `/kontakt`
- [ ] **Admin panel loads:** `https://www.hilotec.com/admin` shows the login page
- [ ] **Admin login works:** Sign in with your configured admin email
- [ ] **Contact form works:** Submit a test message via `/kontakt`
- [ ] **Images load:** Check hero images, team photos, partner logos
- [ ] **CSS/JS load correctly:** Page is styled, Alpine.js interactions work (mobile menu, etc.)

### Security

- [ ] **APP_DEBUG is false:** `curl -s https://www.hilotec.com/nonexistent-page` shows a clean 404, not a stack trace
- [ ] **Security headers present** (`master` branch only; `SecurityHeaders` middleware does not exist on `design-v2`)**:**
  ```bash
  curl -sI https://www.hilotec.com | grep -iE '(strict-transport|x-frame|x-content-type|content-security|referrer-policy|permissions-policy)'
  ```
- [ ] **.env not accessible:** `curl -s https://www.hilotec.com/.env` returns 403 or 404 (NOT the file contents)
- [ ] **.git not accessible:** `curl -s https://www.hilotec.com/.git/HEAD` returns 403 or 404
- [ ] **PHP blocked in assets:** `curl -s https://www.hilotec.com/images/test.php` returns 403
- [ ] **Admin throttled** (`master` only)**:** 6+ rapid login attempts return 429
- [ ] **Scanner paths blocked** (`master` only)**:** `curl -sI https://www.hilotec.com/wp-admin` returns 403, 404, or connection reset
- [ ] **Security audit clean** (`master` only)**:** `php artisan security:audit` reports no issues

### Performance

- [ ] **Config cached:** `php artisan config:show app.env` returns `production`
- [ ] **Routes cached:** File `bootstrap/cache/routes-v7.php` exists
- [ ] **Views cached:** Directory `storage/framework/views/` contains compiled `.php` files
- [ ] **Gzip working:** `curl -sI -H "Accept-Encoding: gzip" https://www.hilotec.com | grep content-encoding` shows `gzip`
- [ ] **Static file caching:** `curl -sI https://www.hilotec.com/build/assets/app-XXXXX.css | grep cache-control` shows long expiry

### Infrastructure

- [ ] **PHP-FPM running:** `systemctl status php8.3-fpm` is active
- [ ] **Nginx/Apache running:** `systemctl status nginx` (or `apache2`) is active
- [ ] **MySQL running:** `systemctl status mysql` is active
- [ ] **Cron scheduler active:** `crontab -l -u deploy` shows the Laravel scheduler entry
- [ ] **SSL certificate valid:** `echo | openssl s_client -connect www.hilotec.com:443 2>/dev/null | openssl x509 -noout -dates`
- [ ] **Disk space adequate:** `df -h /` shows sufficient free space
- [ ] **Log files writing:** `ls -la storage/logs/` shows recent log files

---

## 10. Updating the Website

### 10.1 Standard Update Procedure

If using the `deploy.sh` script:

```bash
cd /var/www/hilotec.com
./deploy.sh
```

That single command handles everything: pull, clean, install, build, optimize, migrate, audit, permissions.

If doing it manually:

```bash
cd /var/www/hilotec.com

# 1. Enable maintenance mode (shows a 503 page to visitors)
php artisan down --secret="your-bypass-token"

# 2. Pull latest code
git pull origin master

# 3. Install dependencies
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Build assets (skip if committed to git)
# npm ci && npm run build

# 5. Run migrations
php artisan migrate --force

# 6. Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Disable maintenance mode
php artisan up
```

> **Tip:** The `--secret` flag lets you bypass maintenance mode by visiting `https://www.hilotec.com/your-bypass-token` in your browser. This sets a cookie that lets you browse the site normally while visitors see the 503 page.

### 10.2 Zero-downtime Approach

For zero-downtime deployments, use a blue-green or symlink-based strategy:

```bash
RELEASE_DIR="/var/www/releases/$(date +%Y%m%d_%H%M%S)"
CURRENT_LINK="/var/www/hilotec.com"
SHARED_DIR="/var/www/shared"

# 1. Clone fresh copy
git clone --depth=1 --branch=master https://github.com/your-org/hilotec.git "$RELEASE_DIR"

# 2. Link shared resources (so .env and storage persist across deploys)
ln -sf "$SHARED_DIR/.env" "$RELEASE_DIR/.env"
ln -sf "$SHARED_DIR/storage" "$RELEASE_DIR/storage"

# 3. Install and optimize
cd "$RELEASE_DIR"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link

# 4. Atomic switch — visitors never see downtime
ln -sfn "$RELEASE_DIR" "$CURRENT_LINK"

# 5. Reload PHP-FPM to pick up the new code path
systemctl reload php8.3-fpm

# 6. Clean up old releases (keep last 5)
ls -dt /var/www/releases/*/ | tail -n +6 | xargs rm -rf
```

> **Important:** Ensure your Nginx/Apache config uses the symlink path (`/var/www/hilotec.com/public`) and uses `$realpath_root` (not `$document_root`) in the `fastcgi_param SCRIPT_FILENAME` directive so PHP-FPM resolves through the symlink correctly.

### 10.3 Rollback

If a deployment causes issues, roll back to the previous version:

**With deploy.sh (git-based):**

```bash
cd /var/www/hilotec.com

# Find the previous commit
git log --oneline -5

# Reset to the previous commit
git checkout <previous-commit-hash>

# Re-run optimizations
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**With symlink-based deploys:**

```bash
# List available releases
ls -lt /var/www/releases/

# Switch to the previous release
ln -sfn /var/www/releases/PREVIOUS_RELEASE /var/www/hilotec.com
systemctl reload php8.3-fpm
```

> **Warning:** If the broken deployment included a database migration that is not backward-compatible (e.g., dropped a column), rolling back the code alone will not fix the issue. Always write reversible migrations and test them before deploying. You can create a rollback migration with `php artisan migrate:rollback --step=1`, but be cautious -- this runs the `down()` method which may cause data loss.

---

## 11. GDPR: Self-hosting Google Fonts

The HILOTEC website currently loads **Sora** (headings) and **DM Sans** (body text) from Google's CDN (`fonts.googleapis.com` / `fonts.gstatic.com`). When a visitor loads the page, their browser connects to Google's servers, transmitting their IP address to Google. Under strict GDPR interpretations (particularly relevant for Swiss and EU companies), this constitutes a data transfer to a third party without user consent.

Self-hosting the fonts eliminates this concern entirely. Fonts are served from your own domain with no external connections.

### 11.1 Download the Font Files

Use the [google-webfonts-helper](https://gwfh.mranftl.com/fonts) tool or download directly from Google Fonts:

**Option A: Using google-webfonts-helper (recommended)**

1. Visit https://gwfh.mranftl.com/fonts/sora?subsets=latin
2. Select character sets: **latin** (add **latin-ext** if you need extended characters)
3. Select styles: **400, 500, 600, 700, 800**
4. Set the "Customize folder prefix" to `/fonts/sora/`
5. Download the ZIP file

Repeat for DM Sans:

1. Visit https://gwfh.mranftl.com/fonts/dm-sans?subsets=latin
2. Select styles: **300, 400, 500, 600, 700** (regular and italic for 400 and 700)
3. Set the "Customize folder prefix" to `/fonts/dm-sans/`
4. Download the ZIP file

**Option B: Direct from Google Fonts**

```bash
# Create the font directories
mkdir -p public/fonts/sora
mkdir -p public/fonts/dm-sans

# Download Sora (woff2 format — supported by all modern browsers)
# Visit https://fonts.google.com/specimen/Sora and use browser DevTools
# to find the actual woff2 URLs from the CSS, then download them.
# The URLs change over time, so use the helper tool above for reliability.
```

### 11.2 Organize the Font Files

Place the downloaded files in your project:

```
public/
  fonts/
    sora/
      sora-v15-latin-regular.woff2
      sora-v15-latin-500.woff2
      sora-v15-latin-600.woff2
      sora-v15-latin-700.woff2
      sora-v15-latin-800.woff2
    dm-sans/
      dm-sans-v15-latin-300.woff2
      dm-sans-v15-latin-regular.woff2
      dm-sans-v15-latin-italic.woff2
      dm-sans-v15-latin-500.woff2
      dm-sans-v15-latin-600.woff2
      dm-sans-v15-latin-700.woff2
      dm-sans-v15-latin-700italic.woff2
```

> **Note:** Only `woff2` is needed for modern browsers (97%+ support). If you need to support very old browsers, also include `.woff` files.

### 11.3 Create @font-face Declarations

Create a new CSS file for font declarations:

```bash
touch resources/css/fonts.css
```

Add the `@font-face` rules (adjust filenames to match your actual downloaded files):

```css
/* ==========================================================================
   Self-hosted Google Fonts — GDPR compliant
   No external requests to Google servers
   ========================================================================== */

/* --- Sora (Headings) --- */

@font-face {
    font-family: 'Sora';
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url('/fonts/sora/sora-v15-latin-regular.woff2') format('woff2');
}

@font-face {
    font-family: 'Sora';
    font-style: normal;
    font-weight: 500;
    font-display: swap;
    src: url('/fonts/sora/sora-v15-latin-500.woff2') format('woff2');
}

@font-face {
    font-family: 'Sora';
    font-style: normal;
    font-weight: 600;
    font-display: swap;
    src: url('/fonts/sora/sora-v15-latin-600.woff2') format('woff2');
}

@font-face {
    font-family: 'Sora';
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url('/fonts/sora/sora-v15-latin-700.woff2') format('woff2');
}

@font-face {
    font-family: 'Sora';
    font-style: normal;
    font-weight: 800;
    font-display: swap;
    src: url('/fonts/sora/sora-v15-latin-800.woff2') format('woff2');
}

/* --- DM Sans (Body) --- */

@font-face {
    font-family: 'DM Sans';
    font-style: normal;
    font-weight: 300;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-300.woff2') format('woff2');
}

@font-face {
    font-family: 'DM Sans';
    font-style: normal;
    font-weight: 400;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-regular.woff2') format('woff2');
}

@font-face {
    font-family: 'DM Sans';
    font-style: italic;
    font-weight: 400;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-italic.woff2') format('woff2');
}

@font-face {
    font-family: 'DM Sans';
    font-style: normal;
    font-weight: 500;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-500.woff2') format('woff2');
}

@font-face {
    font-family: 'DM Sans';
    font-style: normal;
    font-weight: 600;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-600.woff2') format('woff2');
}

@font-face {
    font-family: 'DM Sans';
    font-style: normal;
    font-weight: 700;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-700.woff2') format('woff2');
}

@font-face {
    font-family: 'DM Sans';
    font-style: italic;
    font-weight: 700;
    font-display: swap;
    src: url('/fonts/dm-sans/dm-sans-v15-latin-700italic.woff2') format('woff2');
}
```

### 11.4 Import the Font CSS

Edit `resources/css/app.css` to import the font declarations at the top of the file:

```css
@import './fonts.css';
@import 'tailwindcss';

/* ... rest of app.css unchanged ... */
```

The Tailwind `@theme` block in `app.css` already references `'Sora'` and `'DM Sans'` as font families, so no changes are needed there:

```css
@theme {
    --font-heading: 'Sora', ui-sans-serif, system-ui, sans-serif;
    --font-body: 'DM Sans', ui-sans-serif, system-ui, sans-serif;
}
```

### 11.5 Remove the Google Fonts Link from the Layout

Edit `resources/views/components/layout.blade.php`. Remove these three lines:

```html
{{-- REMOVE THESE THREE LINES --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

Optionally, add a preload hint for the most critical font weights (the ones used above the fold):

```html
{{-- Preload critical fonts for faster rendering --}}
<link rel="preload" href="/fonts/sora/sora-v15-latin-700.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="/fonts/dm-sans/dm-sans-v15-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
```

> **Note:** The `crossorigin` attribute is required on font preloads even when self-hosting, because fonts are always fetched using CORS.

### 11.6 Update the Content Security Policy

Edit `app/Http/Middleware/SecurityHeaders.php`. In the `publicCsp()` method, update the `style-src` and `font-src` directives to remove Google domains:

**Before:**
```php
"style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
"font-src 'self' https://fonts.gstatic.com",
```

**After:**
```php
"style-src 'self' 'unsafe-inline'",
"font-src 'self'",
```

### 11.7 Rebuild and Deploy

```bash
npm run build
# Commit changes and deploy
```

### 11.8 Verify

After deploying, confirm no requests go to Google:

1. Open Chrome DevTools > Network tab
2. Load the homepage
3. Filter by "google" -- there should be zero requests to `fonts.googleapis.com` or `fonts.gstatic.com`
4. Filter by "font" -- fonts should load from your own domain
5. Verify the fonts render correctly (compare headings and body text to the original)

---

## 12. Troubleshooting

### 502 Bad Gateway

PHP-FPM is not running or the socket path is wrong.

```bash
# Check PHP-FPM status
systemctl status php8.3-fpm

# Verify the socket exists
ls -la /run/php/php8.3-fpm.sock

# Check Nginx error log
tail -20 /var/log/nginx/hilotec.com-error.log
```

### 500 Internal Server Error

Usually a Laravel error. Check the log:

```bash
tail -50 /var/www/hilotec.com/storage/logs/laravel.log
```

Common causes:
- Missing `APP_KEY` -- run `php artisan key:generate`
- Database connection failed -- verify `DB_*` settings in `.env`
- Missing PHP extension -- check `php -m` for required extensions
- Permission denied on `storage/` or `bootstrap/cache/` -- fix with `chmod -R 775`

### Blank Page (No Errors)

```bash
# Check if config is cached with wrong values
php artisan config:clear
php artisan config:cache

# Check if APP_DEBUG=false hides an error
# Temporarily set APP_DEBUG=true, reproduce, check the log, then set it back
```

### CSS/JS Not Loading (Unstyled Page)

```bash
# Check if build files exist
ls -la /var/www/hilotec.com/public/build/

# Check the Vite manifest
cat /var/www/hilotec.com/public/build/manifest.json

# Verify APP_URL matches your actual domain
php artisan config:show app.url
```

### "419 Page Expired" on Forms

This is a CSRF token mismatch. Common causes:
- Session driver misconfigured
- `SESSION_DOMAIN` set incorrectly (leave it as `null` unless you have subdomains)
- `SESSION_SECURE_COOKIE=true` but the site is accessed via HTTP

### Permission Denied Errors

```bash
# Nuclear option: reset all permissions
chown -R deploy:www-data /var/www/hilotec.com
find /var/www/hilotec.com -type d -exec chmod 755 {} \;
find /var/www/hilotec.com -type f -exec chmod 644 {} \;
chmod -R 775 /var/www/hilotec.com/storage
chmod -R 775 /var/www/hilotec.com/bootstrap/cache
chmod 640 /var/www/hilotec.com/.env
```

### Composer Fails with "proc_open" Error

The `php-hardening.ini` disables `proc_open` for security. Composer needs it. Run Composer via CLI (not FPM):

```bash
# The CLI php.ini does not have the hardening config
php -r "echo php_ini_loaded_file();"
# Should show /etc/php/8.3/cli/php.ini (not fpm)

# Composer uses CLI PHP by default, so this should just work:
composer install --no-dev
```

If it still fails, the hardening config may have been placed in the CLI conf.d too. Remove it from CLI:

```bash
rm /etc/php/8.3/cli/conf.d/99-security.ini
# Keep it in FPM:
ls /etc/php/8.3/fpm/conf.d/99-security.ini
```
