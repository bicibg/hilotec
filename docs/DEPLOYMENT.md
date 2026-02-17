# Deployment Guide

## Production Requirements

- PHP 8.2+ with extensions: `pdo_mysql` or `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `bcmath`
- Composer 2.x
- Node.js 18+ & npm (for building assets — not required on production server if pre-built)
- MySQL 8+ or PostgreSQL 14+ (production database)
- Nginx or Apache web server
- SSL certificate (Let's Encrypt or similar)

## Environment Configuration

Copy `.env.example` to `.env` and update for production:

```env
APP_NAME="HILOTEC"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.hilotec.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hilotec
DB_USERNAME=hilotec_user
DB_PASSWORD=<secure-password>

CACHE_STORE=file
SESSION_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-user>
MAIL_PASSWORD=<smtp-password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@hilotec.com
MAIL_FROM_NAME="HILOTEC"
```

### Key Settings
- `APP_DEBUG=false` — **critical** for production (exposes sensitive data if `true`)
- `APP_URL` — must match your actual domain, used for asset URLs and link generation
- Database — switch from SQLite to MySQL/PostgreSQL
- Mail — configure SMTP for contact form notifications (optional, submissions are stored in DB regardless)

## Deployment Steps

### First Deployment

```bash
# 1. Clone the repository
git clone https://github.com/bicibg/hilotec.git /var/www/hilotec
cd /var/www/hilotec

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm install

# 3. Environment setup
cp .env.example .env
# Edit .env with production values (see above)
php artisan key:generate

# 4. Build frontend assets
npm run build

# 5. Database setup
php artisan migrate --seed

# 6. Storage link (for file uploads)
php artisan storage:link

# 7. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Subsequent Deployments

```bash
cd /var/www/hilotec

# Pull latest code
git pull origin master

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations (if any)
php artisan migrate --force

# Clear and rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

## Web Server Configuration

### Nginx

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name www.hilotec.com hilotec.com;
    return 301 https://www.hilotec.com$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name www.hilotec.com;

    root /var/www/hilotec/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/www.hilotec.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/www.hilotec.com/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript image/svg+xml;

    # Static file caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff2|woff)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache (.htaccess)
Laravel includes a `public/.htaccess` file that handles URL rewriting. Ensure `mod_rewrite` is enabled.

## File Permissions

```
/var/www/hilotec/
├── storage/           # 775, owned by www-data
│   ├── app/public/    # Uploaded files (team photos, post images)
│   ├── framework/     # Cache, sessions, compiled views
│   └── logs/          # Application logs
├── bootstrap/cache/   # 775, owned by www-data
└── .env               # 640, readable by www-data only
```

## GDPR Considerations

### Google Fonts
Currently loaded from Google CDN. For strict GDPR compliance, self-host the fonts:

1. Download Sora and DM Sans from [Google Fonts](https://fonts.google.com)
2. Place `.woff2` files in `public/fonts/`
3. Replace the `<link>` tags in `resources/views/components/layout.blade.php` with `@font-face` declarations in `resources/css/app.css`
4. Rebuild assets with `npm run build`

### Contact Form
- Submissions are stored in the database (`contact_submissions` table)
- No third-party services are involved
- No tracking cookies or analytics are installed
- Privacy policy is available at `/datenschutz`

## Backup

### Database
```bash
# SQLite (local dev)
cp database/database.sqlite database/backup_$(date +%Y%m%d).sqlite

# MySQL (production)
mysqldump -u hilotec_user -p hilotec > backup_$(date +%Y%m%d).sql
```

### Uploaded Files
```bash
tar -czf uploads_$(date +%Y%m%d).tar.gz storage/app/public/
```

## Monitoring

### Logs
Laravel logs are in `storage/logs/laravel.log`. Check for errors:
```bash
tail -f storage/logs/laravel.log
```

### Common Issues

| Problem | Solution |
|---------|----------|
| 500 error after deployment | Check `storage/logs/laravel.log`, verify `.env` exists and `APP_KEY` is set |
| CSS/JS not loading | Run `npm run build`, check `public/build/` directory exists |
| Settings not updating | Run `php artisan cache:clear` to flush settings cache |
| Images not displaying | Run `php artisan storage:link` to create the symlink |
| Permission denied errors | Fix: `chown -R www-data:www-data storage bootstrap/cache` |
