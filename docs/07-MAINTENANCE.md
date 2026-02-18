# Ongoing Maintenance Guide

This guide covers everything you need to keep the HILOTEC website running smoothly in production. It is written for IT infrastructure professionals -- you do not need Laravel or web development experience to follow these procedures.

**Technology Stack Reference:**

| Component       | Current Version | Purpose                        |
| --------------- | --------------- | ------------------------------ |
| PHP             | 8.2+            | Server-side language           |
| Laravel         | 12.x            | Web application framework      |
| Filament        | 3.x             | Admin panel (CMS at `/admin`)  |
| Composer        | 2.x             | PHP package manager            |
| Node.js         | 18+             | Build tool runtime             |
| npm             | 9+              | JavaScript package manager     |
| Tailwind CSS    | 4.x             | CSS styling framework          |
| Alpine.js       | 3.x             | Lightweight JavaScript         |
| Vite            | 7.x             | Frontend asset build tool      |
| SQLite / MySQL  | -               | Database                       |

---

## 1. Routine Maintenance Schedule

### Daily Tasks

| Task | Command / Action | Why |
| ---- | ---------------- | --- |
| Check the site loads | Open the website in a browser, verify homepage renders | Catches outages immediately |
| Check admin panel | Log in at `/admin`, verify dashboard loads | Ensures CMS is functional |
| Review security audit results | `php artisan security:audit` | The scheduled audit runs automatically every 6 hours, but a manual check lets you see output directly. **Note:** This command only exists on the `master` branch. |
| Check disk space | `df -h` | Laravel writes logs, cached data, and uploaded files that consume disk |
| Glance at error log | Check `storage/logs/laravel.log` for new ERROR or CRITICAL entries | Catch problems before users report them |

### Weekly Tasks

| Task | Command / Action | Why |
| ---- | ---------------- | --- |
| Check for PHP security advisories | `composer audit` | Detects known vulnerabilities in PHP packages |
| Check for JavaScript security advisories | `npm audit` | Detects known vulnerabilities in JavaScript packages |
| Review log file size | `ls -lh storage/logs/laravel.log` | Prevent log files from filling the disk |
| Verify backups completed | Check your backup system's reports | Database and file backups are your safety net |
| Test SSL certificate validity | `curl -vI https://hilotec.com 2>&1 \| grep "expire"` | Catch certificate problems before they cause browser warnings |
| Check Laravel scheduler is running | `grep -c "schedule:run" /var/log/syslog` (or your cron log) | The security audit and other scheduled tasks depend on this |

### Monthly Tasks

| Task | Command / Action | Why |
| ---- | ---------------- | --- |
| Update PHP dependencies | See [Section 2](#2-updating-php-dependencies-composer) | Security patches and bug fixes |
| Update JavaScript dependencies | See [Section 3](#3-updating-frontend-dependencies-npm) | Security patches and bug fixes |
| Clean old log files | See [Section 8](#8-log-management) | Prevent disk exhaustion |
| Clean expired cache and sessions | See [Section 9](#9-storage-management) | Free disk space and remove stale data |
| Review admin user accounts | Check `/admin` user list, disable unused accounts | Principle of least privilege |
| Database backup + integrity check | See [Section 7](#7-database-maintenance) | Protect against data loss and corruption |

### Quarterly Tasks

| Task | Command / Action | Why |
| ---- | ---------------- | --- |
| Check for Laravel minor updates | See [Section 4](#4-laravel-framework-updates) | Stay current with framework patches |
| Check for Filament updates | See [Section 5](#5-filament-updates) | CMS improvements and fixes |
| Review PHP version support status | See [Section 6](#6-php-version-upgrades) | Ensure you are on a supported PHP version |
| Full security review | Run `composer audit`, `npm audit`, check PHP version EOL, review server config | Comprehensive security posture check |
| Test the deployment process | Run `deploy.sh` on a staging environment (`master` only; not present on `design-v2`) | Verify deployments still work correctly |
| Review and test backup restoration | Restore a backup to a test environment | The only backup you can trust is one you have tested |

---

## 2. Updating PHP Dependencies (Composer)

Composer manages all PHP packages, including Laravel itself and Filament. The file `composer.lock` records the exact versions currently installed.

### Before You Start

1. **Always back up your database** before updating dependencies.
2. **Always test on a staging/dev environment first** -- never update directly on production.
3. Make sure the website is working correctly before you start (so you have a known-good baseline).

### Step-by-Step Procedure

```bash
# Navigate to the project directory
cd /path/to/hilotec

# 1. Check for known security vulnerabilities FIRST
composer audit

# 2. See what would be updated (dry run -- changes nothing)
composer update --dry-run

# 3. Review the output. Look for major version changes (e.g., 3.x -> 4.x).
#    Major version bumps can break things. Minor/patch updates are usually safe.

# 4. If the dry run looks reasonable, perform the actual update
composer update

# 5. The update modifies composer.lock. This file MUST be committed to git.
#    Never edit composer.lock by hand.

# 6. Test the website
#    - Load the homepage
#    - Log in to /admin
#    - Create/edit a test content item
#    - Check that contact forms still work

# 7. If everything works, commit the updated lockfile
git add composer.lock
git commit -m "Update PHP dependencies"

# 8. Deploy to production (deploy.sh is master only; use manual deployment on design-v2)
./deploy.sh
```

### If Something Breaks After Updating

```bash
# Revert to the previous lockfile
git checkout HEAD~1 -- composer.lock

# Reinstall the previous versions
composer install

# Verify the site works again
```

### Understanding Version Constraints in composer.json

The project uses constraints like `"^12.0"` and `"^3.0"`. The caret (`^`) means "compatible updates only":
- `^12.0` allows `12.0.1`, `12.1.0`, `12.9.x`, but NOT `13.0.0`
- `^3.0` allows `3.0.1`, `3.1.0`, `3.9.x`, but NOT `4.0.0`

This means `composer update` will never accidentally jump to a new major version. Major version upgrades require manually editing `composer.json`.

---

## 3. Updating Frontend Dependencies (npm)

npm manages JavaScript packages: Tailwind CSS (styling), Alpine.js (interactive elements), and Vite (build tool).

### Step-by-Step Procedure

```bash
cd /path/to/hilotec

# 1. Check for known security vulnerabilities
npm audit

# 2. See what is outdated
npm outdated

# 3. Update packages within the version ranges specified in package.json
npm update

# 4. IMPORTANT: Rebuild the frontend assets after any npm update
npm run build

# 5. Test the website visually
#    - Check that the homepage looks correct (layout, fonts, colors)
#    - Check that navigation menus work (these use Alpine.js)
#    - Check that mobile/responsive views still work
#    - Check the admin panel styling at /admin

# 6. If everything looks good, commit
git add package-lock.json
git commit -m "Update frontend dependencies"
```

### Major Version Bumps (e.g., Tailwind CSS 4 to 5)

Major version upgrades for frontend tools often require configuration changes. Do NOT attempt these as routine maintenance. These should be treated as a development project:

- **Tailwind CSS**: Styling framework. A major version bump may change how CSS classes work, breaking the site's appearance. Configuration lives in `resources/css/app.css` and `vite.config.js`.
- **Vite**: Build tool. A major version bump may require changes to `vite.config.js` and `package.json` scripts.
- **Alpine.js**: Used for dropdowns, mobile menus, and other interactive elements. Major versions may change syntax.

For major version upgrades, consult the migration guide published by each tool and test thoroughly on a staging environment.

### If the Site Looks Broken After Updating

```bash
# Revert the lockfile
git checkout HEAD~1 -- package-lock.json

# Clean install the previous versions
rm -rf node_modules
npm ci

# Rebuild
npm run build

# Verify the site looks correct again
```

---

## 4. Laravel Framework Updates

Laravel is the web framework that powers the entire application. It follows semantic versioning: `MAJOR.MINOR.PATCH`.

### Minor and Patch Updates (e.g., 12.0 to 12.1, or 12.1.0 to 12.1.1)

These are handled automatically by `composer update` (see Section 2) because the constraint `"^12.0"` allows them. They include bug fixes and small improvements. These are generally safe.

### Major Updates (e.g., 12.x to 13.x)

Major Laravel versions are released roughly once per year. Major upgrades can introduce breaking changes and require code modifications. **Do not attempt a major Laravel upgrade as routine maintenance.** This is a development project.

When a major upgrade is needed:

1. Read the official Laravel Upgrade Guide at `https://laravel.com/docs/{version}/upgrade`
2. Plan for several hours of development and testing work
3. Test on a full staging environment that mirrors production
4. Key areas to verify after upgrading:
   - All page routes load correctly
   - Admin panel at `/admin` works (Filament compatibility)
   - Contact form submissions work
   - File uploads work
   - Scheduled tasks run (security audit)
   - Email sending works

### How to Perform a Major Upgrade

```bash
# 1. Create a new branch for the upgrade
git checkout -b laravel-upgrade

# 2. Edit composer.json: change "laravel/framework": "^12.0" to "^13.0"
#    (or whatever the next version is)

# 3. Run composer update
composer update

# 4. Follow the official upgrade guide for any required code changes

# 5. Rebuild frontend assets
npm run build

# 6. Run all tests
php artisan test

# 7. Test everything manually on staging

# 8. Once verified, merge to master and deploy
```

---

## 5. Filament Updates

Filament is the admin panel framework (the CMS at `/admin`). It is updated through Composer like any other PHP package.

### Updating Filament

```bash
cd /path/to/hilotec

# Update Filament (this is included in a general composer update,
# but you can also target it specifically)
composer update filament/filament

# IMPORTANT: After updating Filament, refresh its published assets.
# This is already configured to run automatically via the post-autoload-dump
# script in composer.json, but you can also run it manually:
php artisan filament:upgrade

# Clear cached views to ensure the admin panel uses new templates
php artisan view:clear

# Test the admin panel
#   - Log in at /admin
#   - Navigate through all menu items
#   - Try creating, editing, and deleting a test record
#   - Check that file uploads still work
```

### Filament Major Version Upgrades (e.g., 3.x to 4.x)

Like Laravel, major Filament upgrades are development projects, not routine maintenance. Filament resources live in `app/Filament/Resources/` and `app/Filament/Pages/` and may need code changes during a major upgrade.

---

## 6. PHP Version Upgrades

PHP releases a new minor version annually and each version receives 2 years of active support plus 1 year of security-only fixes.

### PHP Support Timeline (as of 2026)

| Version | Active Support Until | Security Fixes Until | Status |
| ------- | -------------------- | -------------------- | ------ |
| PHP 8.2 | Dec 2024             | Dec 2025             | End of life |
| PHP 8.3 | Dec 2025             | Dec 2026             | Security fixes only |
| PHP 8.4 | Dec 2026             | Dec 2027             | Active support |

**Check the official schedule at https://www.php.net/supported-versions.php** -- the dates above may shift.

### Upgrade Checklist

Before upgrading PHP on the server:

1. **Verify Laravel compatibility.** Check `https://laravel.com/docs` for which PHP versions the current Laravel version supports. Laravel 12 requires PHP 8.2+, but this project requires PHP 8.3+ (due to PHPUnit 12).

2. **Test on a staging environment first.** Install the new PHP version on a test server, deploy the application, and verify everything works.

3. **Check for deprecated features.**
   ```bash
   # Enable deprecation logging temporarily in .env
   LOG_DEPRECATIONS_CHANNEL=single

   # Then browse the site and check storage/logs/laravel.log for deprecation warnings
   ```

4. **Verify PHP extensions are installed.** Laravel requires these PHP extensions:
   - `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `mbstring`
   - `openssl`, `pcre`, `pdo`, `session`, `tokenizer`, `xml`
   - `pdo_sqlite` (if using SQLite) or `pdo_mysql` (if using MySQL)

   ```bash
   # List installed PHP extensions
   php -m

   # Check PHP version
   php -v
   ```

5. **Perform the upgrade on the server:**
   ```bash
   # Ubuntu/Debian example
   sudo apt update
   sudo apt install php8.4 php8.4-fpm php8.4-cli \
       php8.4-mbstring php8.4-xml php8.4-curl php8.4-sqlite3 \
       php8.4-mysql php8.4-zip php8.4-gd php8.4-bcmath

   # Update the web server (Nginx or Apache) to use the new PHP-FPM socket
   # For Nginx, edit the site config and change:
   #   fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
   # to:
   #   fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;

   # Restart the web server
   sudo systemctl restart nginx   # or: sudo systemctl restart apache2
   sudo systemctl restart php8.4-fpm

   # Verify
   php -v
   ```

6. **After upgrading, clear all Laravel caches:**
   ```bash
   cd /path/to/hilotec
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

7. **Test the site thoroughly.** Load every page, test the admin panel, test form submissions.

---

## 7. Database Maintenance

This project uses SQLite for development and can use MySQL or PostgreSQL in production. The database stores all website content (services, references, team members, blog posts, contact submissions, settings) as well as sessions, cache, and job queues.

### Backups -- The Most Important Task

**Always back up before running migrations, updating dependencies, or making any changes.**

#### SQLite Backup

SQLite is a single file. Backing it up is as simple as copying the file:

```bash
# Find where the database file is
grep DB_DATABASE /path/to/hilotec/.env

# Back up (typically database/database.sqlite)
cp /path/to/hilotec/database/database.sqlite \
   /path/to/backups/hilotec-$(date +%Y%m%d-%H%M%S).sqlite
```

#### MySQL Backup

```bash
# Full database dump
mysqldump -u root -p hilotec_db > /path/to/backups/hilotec-$(date +%Y%m%d-%H%M%S).sql

# Compressed backup (recommended for larger databases)
mysqldump -u root -p hilotec_db | gzip > /path/to/backups/hilotec-$(date +%Y%m%d-%H%M%S).sql.gz
```

### Running Migrations

When you deploy new code that includes database changes, Laravel uses "migrations" to modify the database schema. The `deploy.sh` script runs migrations automatically, but you can also run them manually:

```bash
cd /path/to/hilotec

# See which migrations have not been run yet
php artisan migrate:status

# Run pending migrations (--force is required in production)
php artisan migrate --force
```

**Never run `migrate:fresh` or `migrate:rollback` in production** -- these destroy data.

### SQLite Maintenance

SQLite databases can become fragmented over time, especially after many inserts and deletes.

```bash
# VACUUM rebuilds the database file, reclaiming space and defragmenting
sqlite3 /path/to/hilotec/database/database.sqlite "VACUUM;"

# Check database integrity
sqlite3 /path/to/hilotec/database/database.sqlite "PRAGMA integrity_check;"
```

Run VACUUM monthly or after large data changes (e.g., deleting many contact submissions).

### MySQL Maintenance

```bash
# Optimize all tables (reclaim space, update statistics)
mysqlcheck -u root -p --optimize hilotec_db

# Check all tables for errors
mysqlcheck -u root -p --check hilotec_db
```

---

## 8. Log Management

Laravel writes application logs to `storage/logs/laravel.log`. By default, this project uses the `single` log channel, which means all log entries go into one file that grows indefinitely.

### Checking Logs

```bash
# View the most recent log entries
tail -100 /path/to/hilotec/storage/logs/laravel.log

# Search for errors
grep -i "error\|critical\|emergency" /path/to/hilotec/storage/logs/laravel.log | tail -50

# Check log file size
ls -lh /path/to/hilotec/storage/logs/laravel.log
```

### Switching to Daily Log Rotation (Recommended)

The default `single` channel writes everything to one file forever. Switch to the `daily` channel, which creates a new log file each day and automatically deletes files older than 14 days.

Edit the `.env` file on the server:

```ini
# Change this:
LOG_CHANNEL=stack
LOG_STACK=single

# To this:
LOG_CHANNEL=stack
LOG_STACK=daily
```

Then clear the config cache:

```bash
php artisan config:clear
php artisan config:cache
```

After this change, logs will appear as `storage/logs/laravel-2026-02-17.log` (one file per day), and files older than 14 days will be deleted automatically by Laravel. You can change the retention period by setting `LOG_DAILY_DAYS` in `.env`:

```ini
LOG_DAILY_DAYS=30
```

### Manual Log Cleanup

If you are still using the `single` channel and the log file has grown too large:

```bash
# Truncate the log file (keeps the file but empties its contents)
truncate -s 0 /path/to/hilotec/storage/logs/laravel.log

# Or rotate it manually
mv /path/to/hilotec/storage/logs/laravel.log \
   /path/to/hilotec/storage/logs/laravel-$(date +%Y%m%d).log.bak
```

There is no need to restart the application after truncating -- Laravel will continue writing to the same file path.

### Setting Up OS-Level Log Rotation (Alternative)

If you prefer to manage rotation at the OS level, create a logrotate configuration:

```bash
sudo nano /etc/logrotate.d/hilotec
```

```
/path/to/hilotec/storage/logs/laravel.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    copytruncate
}
```

The key setting here is `copytruncate`, which allows rotation without restarting the application.

---

## 9. Storage Management

Laravel uses the `storage/` directory for many purposes. Over time, it accumulates data that should be cleaned periodically.

### Storage Directory Structure

```
storage/
  app/
    public/          -- Uploaded files (accessible via /storage symlink)
  framework/
    cache/           -- Application cache data
    sessions/        -- User session files (if using file driver)
    views/           -- Compiled Blade templates
  logs/              -- Application log files
  quarantine/        -- Files quarantined by SecurityAudit command (master only)
```

### Clearing the Application Cache

Settings from the admin panel are cached for 60 minutes. If you change a setting and it does not appear on the site, clear the cache:

```bash
cd /path/to/hilotec

# Clear the application cache (includes settings cache)
php artisan cache:clear

# Clear compiled views
php artisan view:clear

# Clear config cache (only if you changed .env or config files)
php artisan config:clear
```

### Clearing Expired Sessions

This project stores sessions in the database (`SESSION_DRIVER=database`). Expired sessions are not automatically removed. Clean them periodically:

```bash
# For SQLite
sqlite3 /path/to/hilotec/database/database.sqlite \
  "DELETE FROM sessions WHERE last_activity < strftime('%s', 'now') - 86400;"

# For MySQL
mysql -u root -p hilotec_db -e \
  "DELETE FROM sessions WHERE last_activity < UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY);"
```

### Cleaning the Quarantine Directory

> **Note:** The SecurityAudit command and quarantine directory only exist on the `master` branch.

The SecurityAudit command (which runs every 6 hours) moves suspicious files to `storage/quarantine/`. Review these files periodically and delete old quarantine directories once you have confirmed they are not needed:

```bash
# List quarantined items
ls -la /path/to/hilotec/storage/quarantine/

# Remove quarantine directories older than 30 days
find /path/to/hilotec/storage/quarantine/ -type d -mtime +30 -exec rm -rf {} + 2>/dev/null
```

### Managing Uploaded Files

Files uploaded through the admin panel are stored in `storage/app/public/` and made accessible to the web through a symlink at `public/storage`. If the symlink is missing (e.g., after a server migration), recreate it:

```bash
php artisan storage:link
```

This creates `public/storage` -> `storage/app/public`.

### Disk Usage Check

```bash
# Overall disk usage of the project
du -sh /path/to/hilotec/

# Break down by subdirectory
du -sh /path/to/hilotec/storage/*
du -sh /path/to/hilotec/storage/framework/*
du -sh /path/to/hilotec/vendor/
du -sh /path/to/hilotec/node_modules/

# Note: node_modules/ should NOT exist on production.
# On master, the deploy.sh script runs "npm ci" and "npm run build" only if needed,
# and the built assets go into public/build/.
```

---

## 10. SSL Certificate Renewal

If the production server uses Let's Encrypt certificates (common for web hosting), the certificate must be renewed before it expires (every 90 days).

### Verifying Auto-Renewal

Most Let's Encrypt installations set up automatic renewal via a cron job or systemd timer. Verify this is working:

```bash
# Check if a renewal timer exists
sudo systemctl list-timers | grep certbot

# Or check for a cron job
sudo crontab -l | grep certbot
cat /etc/cron.d/certbot 2>/dev/null

# Check the current certificate expiration date
sudo certbot certificates

# Or check via OpenSSL
echo | openssl s_client -connect hilotec.com:443 -servername hilotec.com 2>/dev/null | \
  openssl x509 -noout -dates
```

### Manual Renewal (If Needed)

```bash
# Dry run (test without actually renewing)
sudo certbot renew --dry-run

# Actual renewal
sudo certbot renew

# Restart the web server after renewal
sudo systemctl restart nginx   # or apache2
```

### Monitoring Certificate Expiration

Set up a simple check that warns you when the certificate is close to expiring. Add this to a weekly cron job:

```bash
#!/bin/bash
DOMAIN="hilotec.com"
EXPIRY=$(echo | openssl s_client -connect $DOMAIN:443 -servername $DOMAIN 2>/dev/null | \
  openssl x509 -noout -enddate 2>/dev/null | cut -d= -f2)
EXPIRY_EPOCH=$(date -d "$EXPIRY" +%s)
NOW_EPOCH=$(date +%s)
DAYS_LEFT=$(( (EXPIRY_EPOCH - NOW_EPOCH) / 86400 ))

if [ "$DAYS_LEFT" -lt 14 ]; then
  echo "WARNING: SSL certificate for $DOMAIN expires in $DAYS_LEFT days!"
  # Add email notification here if desired
fi
```

---

## 11. Security Updates

### Automated Security Scanning

> **Note:** The `SecurityAudit` command and its scheduled scanning only exist on the `master` branch. This feature is not available on `design-v2`.

This project includes a built-in `SecurityAudit` artisan command that runs automatically every 6 hours via the Laravel scheduler. It scans the `public/` directory for:

- Unauthorized files (files not tracked in git)
- Dangerous file types (.php, .exe, etc.) in asset directories
- Injected `.htaccess` files
- Unauthorized symlinks
- World-writable file permissions
- Recently modified files

**For this to work, the Laravel scheduler must be running.** Verify the cron job exists on the production server:

```bash
# Check the crontab for the web server user (e.g., www-data, forge, or your user)
crontab -l

# The entry should look like this:
# * * * * * cd /path/to/hilotec && php artisan schedule:run >> /dev/null 2>&1
```

If the cron entry is missing, add it:

```bash
crontab -e
# Add this line:
* * * * * cd /path/to/hilotec && php artisan schedule:run >> /dev/null 2>&1
```

### Running Security Audits Manually

```bash
cd /path/to/hilotec

# Scan only (report findings but do not change anything)
php artisan security:audit

# Scan and automatically quarantine suspicious files
php artisan security:audit --fix

# Scan, quarantine, and send an email alert
php artisan security:audit --fix --notify
```

### Checking for Known Vulnerabilities in Dependencies

```bash
cd /path/to/hilotec

# Check PHP packages for known CVEs
composer audit

# Check JavaScript packages for known CVEs
npm audit

# Fix JavaScript vulnerabilities automatically (when possible)
npm audit fix
```

Run these checks weekly. If `composer audit` or `npm audit` reports HIGH or CRITICAL vulnerabilities, update the affected packages promptly.

### Keeping the Server Secure

In addition to application-level security, maintain the server itself:

```bash
# Check for OS security updates (Ubuntu/Debian)
sudo apt update
sudo apt list --upgradable

# Apply security updates
sudo apt upgrade

# Check that the firewall is active
sudo ufw status
```

### The deploy.sh Security Features

> **Note:** `deploy.sh` only exists on the `master` branch. It is not available on `design-v2`.

The deployment script (`deploy.sh`) includes built-in security hardening that runs on every deployment:

1. Removes unauthorized files from `public/` that are not tracked in git
2. Deletes PHP files from asset directories (`public/css/`, `public/js/`, `public/images/`, `public/fonts/`)
3. Removes injected `.htaccess` files from subdirectories
4. Sets proper file permissions (644 for files, 755 for directories in `public/`)
5. Makes `storage/` and `bootstrap/cache/` writable (775)
6. Restricts `.env` to owner and group only (640)
7. Runs the `security:audit` command

---

## 12. Performance Monitoring

### Response Time Monitoring

The simplest way to monitor the site's performance is to measure response time from outside:

```bash
# Measure homepage response time
curl -o /dev/null -s -w "HTTP %{http_code} - Time: %{time_total}s\n" https://hilotec.com

# Expected: < 1 second for the homepage
# If consistently > 2 seconds, investigate further
```

### Identifying Slow Queries

If the site feels slow, enable query logging temporarily to find slow database queries:

```bash
cd /path/to/hilotec

# Open Laravel's interactive console
php artisan tinker

# Inside tinker, enable query logging:
>>> DB::enableQueryLog();
>>> // Simulate a page load by calling a controller or model
>>> App\Models\Service::published()->get();
>>> DB::getQueryLog();
```

For SQLite, you can also check query performance directly:

```bash
sqlite3 /path/to/hilotec/database/database.sqlite
# Inside SQLite:
.timer on
SELECT * FROM services WHERE is_published = 1;
```

### Laravel Telescope (Optional)

Laravel Telescope is a debugging and profiling tool that provides a web dashboard showing requests, queries, exceptions, log entries, and more. It is useful for diagnosing production issues but adds overhead.

To install (on a staging environment first):

```bash
composer require laravel/telescope
php artisan telescope:install
php artisan migrate
```

**Important:** If installed in production, restrict access to the Telescope dashboard. It exposes sensitive information. See `https://laravel.com/docs/telescope#dashboard-authorization`.

### Cache Performance

The settings helper caches all settings for 60 minutes. If you notice settings not updating after changes in the admin panel, the cache is working correctly -- you just need to wait or clear it manually:

```bash
php artisan cache:clear
```

### Checking the Optimization Cache

In production, Laravel caches configuration, routes, and views for faster performance. The `deploy.sh` script sets these up automatically. To verify:

```bash
cd /path/to/hilotec

# Check if config is cached
test -f bootstrap/cache/config.php && echo "Config: CACHED" || echo "Config: NOT cached"

# Check if routes are cached
test -f bootstrap/cache/routes-v7.php && echo "Routes: CACHED" || echo "Routes: NOT cached"

# If caches seem stale, rebuild them
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 13. Health Checks

### What to Monitor

| Check | How | Expected Result | Alert If |
| ----- | --- | --------------- | -------- |
| Website reachable | HTTP GET to homepage | HTTP 200, page loads in < 2s | Non-200 or timeout |
| Admin panel reachable | HTTP GET to `/admin/login` | HTTP 200 | Non-200 |
| SSL certificate valid | OpenSSL check (see Section 10) | Valid, > 14 days until expiry | < 14 days to expiry |
| Disk space | `df -h` | < 80% used | > 90% used |
| PHP-FPM running | `systemctl status php8.x-fpm` | Active (running) | Inactive or failed |
| Web server running | `systemctl status nginx` (or apache2) | Active (running) | Inactive or failed |
| Cron / scheduler | Check crontab, verify recent runs | Cron entry exists | Entry missing |
| Database accessible | `php artisan tinker --execute="DB::select('SELECT 1');"` | Returns result | Error |
| Log file errors | Grep for CRITICAL/EMERGENCY in `laravel.log` | None or few | Sudden spike |
| Security audit clean (`master` only) | `php artisan security:audit` | "All clear" | Issues found |
| Storage symlink exists | `test -L public/storage` | Symlink exists | Missing |

### Recommended Monitoring Approach

For an IT infrastructure company, the simplest effective monitoring setup:

1. **Uptime monitor**: Use an external service (e.g., UptimeRobot free tier, Uptime Kuma self-hosted, or your existing monitoring stack) to ping the website every 5 minutes and alert on downtime.

2. **Simple health endpoint**: Laravel can respond to a health check URL. The default `/up` route returns HTTP 200 if the application is running. Configure your monitoring tool to check this URL.

3. **Server-side script**: Create a cron job that runs basic checks and sends alerts:

```bash
#!/bin/bash
# /usr/local/bin/hilotec-health-check.sh
# Run via cron every 15 minutes

SITE_URL="https://hilotec.com"
SITE_DIR="/path/to/hilotec"
ALERT_EMAIL="admin@hilotec.com"

# Check 1: Website responds
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "$SITE_URL" --max-time 10)
if [ "$HTTP_CODE" != "200" ]; then
  echo "ALERT: $SITE_URL returned HTTP $HTTP_CODE" | mail -s "HILOTEC Site Down" "$ALERT_EMAIL"
fi

# Check 2: Disk space
DISK_USAGE=$(df "$SITE_DIR" --output=pcent | tail -1 | tr -d ' %')
if [ "$DISK_USAGE" -gt 90 ]; then
  echo "ALERT: Disk usage at ${DISK_USAGE}%" | mail -s "HILOTEC Disk Warning" "$ALERT_EMAIL"
fi

# Check 3: Log file for critical errors in the last hour
RECENT_ERRORS=$(find "$SITE_DIR/storage/logs/" -name "*.log" -mmin -60 \
  -exec grep -c "CRITICAL\|EMERGENCY" {} + 2>/dev/null | \
  awk -F: '{sum+=$2} END {print sum+0}')
if [ "$RECENT_ERRORS" -gt 0 ]; then
  echo "ALERT: $RECENT_ERRORS critical errors in the last hour" | \
    mail -s "HILOTEC Application Errors" "$ALERT_EMAIL"
fi
```

```bash
# Add to crontab
crontab -e
# Add:
*/15 * * * * /usr/local/bin/hilotec-health-check.sh
```

---

## 14. Common Maintenance Commands Reference

### Everyday Commands

| Task | Command |
| ---- | ------- |
| Check site status | `curl -s -o /dev/null -w "%{http_code}" https://hilotec.com` |
| View recent logs | `tail -100 storage/logs/laravel.log` |
| Search logs for errors | `grep -i "error\|critical" storage/logs/laravel.log \| tail -30` |
| Clear all caches | `php artisan optimize:clear` |
| Rebuild all caches | `php artisan optimize` |
| Clear settings cache only | `php artisan cache:clear` |
| Run security scan (`master` only) | `php artisan security:audit` |
| Run security scan + fix (`master` only) | `php artisan security:audit --fix --notify` |

### Deployment and Updates

| Task | Command |
| ---- | ------- |
| Deploy latest code | `./deploy.sh` (`master` only; not present on `design-v2`) |
| Check PHP dependency security | `composer audit` |
| Check JS dependency security | `npm audit` |
| Update PHP dependencies | `composer update` |
| Update JS dependencies | `npm update && npm run build` |
| Run database migrations | `php artisan migrate --force` |
| Check migration status | `php artisan migrate:status` |

### Database Operations

| Task | Command |
| ---- | ------- |
| Back up SQLite | `cp database/database.sqlite /backups/hilotec-$(date +%Y%m%d).sqlite` |
| Back up MySQL | `mysqldump -u root -p hilotec_db > /backups/hilotec-$(date +%Y%m%d).sql` |
| Optimize SQLite | `sqlite3 database/database.sqlite "VACUUM;"` |
| Check SQLite integrity | `sqlite3 database/database.sqlite "PRAGMA integrity_check;"` |
| Optimize MySQL | `mysqlcheck -u root -p --optimize hilotec_db` |
| Clean expired sessions (SQLite) | `sqlite3 database/database.sqlite "DELETE FROM sessions WHERE last_activity < strftime('%s','now') - 86400;"` |

### Cache and Storage

| Task | Command |
| ---- | ------- |
| Clear application cache | `php artisan cache:clear` |
| Clear config cache | `php artisan config:clear` |
| Clear route cache | `php artisan route:clear` |
| Clear compiled views | `php artisan view:clear` |
| Clear everything | `php artisan optimize:clear` |
| Cache everything (production) | `php artisan optimize` |
| Recreate storage symlink | `php artisan storage:link` |
| Check storage disk usage | `du -sh storage/*` |

### Server and Services

| Task | Command |
| ---- | ------- |
| Check PHP version | `php -v` |
| Check PHP extensions | `php -m` |
| Check Composer version | `composer --version` |
| Check Node.js version | `node -v` |
| Restart PHP-FPM | `sudo systemctl restart php8.x-fpm` |
| Restart Nginx | `sudo systemctl restart nginx` |
| Restart Apache | `sudo systemctl restart apache2` |
| Check crontab | `crontab -l` |
| Check SSL expiration | `echo \| openssl s_client -connect hilotec.com:443 2>/dev/null \| openssl x509 -noout -enddate` |
| Check disk space | `df -h` |

### Emergency Procedures

| Situation | Action |
| --------- | ------ |
| Site is down | 1. Check web server: `systemctl status nginx` 2. Check PHP-FPM: `systemctl status php8.x-fpm` 3. Check logs: `tail -50 storage/logs/laravel.log` 4. Check disk: `df -h` |
| Site shows error page | 1. Check `storage/logs/laravel.log` for the error 2. Try `php artisan optimize:clear` 3. Check `.env` file exists and is readable |
| Admin panel broken after update | 1. Run `php artisan filament:upgrade` 2. Run `php artisan view:clear` 3. Run `php artisan optimize:clear` |
| Settings not updating | 1. Run `php artisan cache:clear` (settings are cached for 60 minutes) |
| "Storage link missing" error | Run `php artisan storage:link` |
| Database locked (SQLite) | 1. Check no other process is writing 2. Restart PHP-FPM 3. Check file permissions on the `.sqlite` file |
| Deployment failed | 1. Check `deploy.sh` output for errors (`master` only) 2. Check disk space 3. Verify git remote is accessible 4. Check Composer/npm can connect to package registries |

---

**Important reminder:** Always test changes on a staging environment before applying them to production. Keep backups current. When in doubt, do not make changes -- consult the development team.
