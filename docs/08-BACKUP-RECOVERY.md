# Backup & Disaster Recovery Guide

This guide covers backup strategies, automated backup scripts, and step-by-step disaster recovery procedures for the HILOTEC website. It is written for sysadmins who manage Linux servers and are comfortable with bash, but may not be familiar with Laravel internals.

Throughout this document, `/var/www/hilotec` refers to the application root on the production server. Replace it with your actual path.

---

## Table of Contents

1. [What Needs to Be Backed Up](#1-what-needs-to-be-backed-up)
2. [Database Backup](#2-database-backup)
3. [File Backup](#3-file-backup)
4. [Full-site Backup Script](#4-full-site-backup-script)
5. [Backup Rotation & Retention](#5-backup-rotation--retention)
6. [Off-site Backup](#6-off-site-backup)
7. [Testing Backups](#7-testing-backups)
8. [Disaster Recovery Procedures](#8-disaster-recovery-procedures)
9. [Laravel-specific Backup Package (spatie/laravel-backup)](#9-laravel-specific-backup-package-spatielaravel-backup)
10. [Recovery Checklist](#10-recovery-checklist)
11. [Backup Monitoring](#11-backup-monitoring)

---

## 1. What Needs to Be Backed Up

Not everything on the server is equally important. This section categorizes every component so you can prioritize backup frequency and storage allocation.

### Critical (data loss is unrecoverable without backup)

| Item | Location | Why |
|------|----------|-----|
| **Database** | MySQL: your configured DB; SQLite: `database/database.sqlite` | All site content: services, references, team members, posts, pages, partners, settings, contact form submissions, admin users |
| **`.env` file** | Project root (`.env`) | Contains `APP_KEY` (encrypts sessions and cookies), database credentials, `ADMIN_EMAILS`, mail configuration, reCAPTCHA keys |
| **Uploaded media** | `storage/app/public/posts/`, `storage/app/public/team/`, `storage/app/public/partners/` | Blog post images, team member photos, partner logos -- uploaded through the Filament admin panel |

### Important (loss causes inconvenience, significant effort to rebuild)

| Item | Location | Why |
|------|----------|-----|
| **Web server config** | `/etc/nginx/sites-available/hilotec` or `/etc/apache2/sites-available/hilotec.conf` | Nginx/Apache virtual host, SSL settings, security headers |
| **PHP config overrides** | `/etc/php/8.x/fpm/conf.d/99-hardening.ini` (or equivalent) | Custom PHP hardening settings (the project ships `php-hardening.ini`) |
| **SSL certificates** | `/etc/letsencrypt/` (if using Certbot) | Certificate + private key. Can be regenerated, but causes downtime |
| **Crontab** | `crontab -l` output for the web user | Laravel scheduler entry and any backup cron jobs |
| **Quarantined files** | `storage/quarantine/` | Files flagged by the `security:audit` command -- useful for forensics |
| **Log files** | `storage/logs/laravel.log` | Application logs. Not essential for recovery, but valuable for post-incident analysis |

### Regenerable (can be rebuilt from source code)

| Item | How to Regenerate | Notes |
|------|-------------------|-------|
| **Compiled assets** (`public/build/`) | `npm install && npm run build` | CSS and JavaScript bundles |
| **Composer packages** (`vendor/`) | `composer install --no-dev` | PHP dependencies |
| **Node modules** (`node_modules/`) | `npm install` | JS build dependencies (not needed on prod after build) |
| **Cached config/routes/views** (`storage/framework/cache/`, `storage/framework/views/`, `bootstrap/cache/`) | `php artisan optimize` | Laravel performance caches |
| **Sessions** (`storage/framework/sessions/`) | Users simply log in again | Transient data |

> **Rule of thumb:** Back up the "Critical" items on every run. Include "Important" items daily. Never waste backup space on "Regenerable" items.

---

## 2. Database Backup

### 2.1 SQLite (Development / Small Deployments)

SQLite stores the entire database in a single file. Backup is a file copy.

```bash
# Simple copy
cp /var/www/hilotec/database/database.sqlite \
   /backups/hilotec/database/database_$(date +%Y%m%d_%H%M%S).sqlite

# Safer: use sqlite3 .backup command (handles locks correctly)
sqlite3 /var/www/hilotec/database/database.sqlite \
   ".backup '/backups/hilotec/database/database_$(date +%Y%m%d_%H%M%S).sqlite'"
```

> **Warning:** Never copy the SQLite file while the application is actively writing to it. Use the `sqlite3 .backup` command or put the application in maintenance mode first:
> ```bash
> cd /var/www/hilotec
> php artisan down
> cp database/database.sqlite /backups/hilotec/database/database_$(date +%Y%m%d_%H%M%S).sqlite
> php artisan up
> ```

### 2.2 MySQL (Recommended for Production)

```bash
# Full dump with all tables
mysqldump \
  --user=hilotec_user \
  --password='YOUR_DB_PASSWORD' \
  --single-transaction \
  --routines \
  --triggers \
  --databases hilotec \
  | gzip > /backups/hilotec/database/mysql_$(date +%Y%m%d_%H%M%S).sql.gz
```

**Key tables in this application:**

| Table | Content |
|-------|---------|
| `users` | Admin users |
| `settings` | Site-wide settings (group/key/value pairs used throughout templates) |
| `services` | Service offerings shown on the website |
| `references` | Client references / case studies |
| `reference_categories` | Categories for references |
| `team_members` | Team member profiles and photos |
| `posts` | Blog posts |
| `pages` | CMS pages |
| `partners` | Partner logos and info |
| `contact_submissions` | Contact form submissions from visitors |
| `cache` | Application cache (regenerable) |
| `sessions` | User sessions (regenerable) |
| `jobs` | Queue jobs (transient) |

**Dump only critical tables** (faster, smaller file):

```bash
mysqldump \
  --user=hilotec_user \
  --password='YOUR_DB_PASSWORD' \
  --single-transaction \
  hilotec \
  users settings services references reference_categories \
  team_members posts pages partners contact_submissions migrations \
  | gzip > /backups/hilotec/database/mysql_critical_$(date +%Y%m%d_%H%M%S).sql.gz
```

### 2.3 PostgreSQL

```bash
pg_dump \
  --username=hilotec_user \
  --format=custom \
  --file=/backups/hilotec/database/pgsql_$(date +%Y%m%d_%H%M%S).dump \
  hilotec

# Or as compressed plain SQL
pg_dump \
  --username=hilotec_user \
  hilotec \
  | gzip > /backups/hilotec/database/pgsql_$(date +%Y%m%d_%H%M%S).sql.gz
```

For password-less automated dumps, create a `~/.pgpass` file:

```
localhost:5432:hilotec:hilotec_user:YOUR_DB_PASSWORD
```

```bash
chmod 600 ~/.pgpass
```

### 2.4 Scheduling Automated Database Backups

Add to the crontab of the user that runs the web application (often `www-data` or a dedicated deploy user):

```bash
# Edit crontab
crontab -e
```

```cron
# Database backup every 6 hours
0 */6 * * * /var/www/hilotec/scripts/backup-database.sh >> /var/log/hilotec-backup.log 2>&1
```

Example `/var/www/hilotec/scripts/backup-database.sh`:

```bash
#!/bin/bash
set -euo pipefail

BACKUP_DIR="/backups/hilotec/database"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

mkdir -p "$BACKUP_DIR"

# MySQL example (adjust for your DB engine)
mysqldump \
  --user=hilotec_user \
  --password='YOUR_DB_PASSWORD' \
  --single-transaction \
  --routines \
  --triggers \
  hilotec \
  | gzip > "${BACKUP_DIR}/mysql_${TIMESTAMP}.sql.gz"

echo "[$(date)] Database backup completed: mysql_${TIMESTAMP}.sql.gz"
```

```bash
chmod +x /var/www/hilotec/scripts/backup-database.sh
```

---

## 3. File Backup

### 3.1 Uploaded Media

All files uploaded through the Filament admin panel are stored in `storage/app/public/` and symlinked to `public/storage/`. The relevant subdirectories are:

```
storage/app/public/
  posts/      -- Blog post featured images
  team/       -- Team member photos
  partners/   -- Partner logos
```

```bash
# Backup uploaded media
tar czf /backups/hilotec/files/media_$(date +%Y%m%d_%H%M%S).tar.gz \
  -C /var/www/hilotec/storage/app/public .
```

### 3.2 Environment File

The `.env` file is the single most important configuration file. It contains:

- `APP_KEY` -- Used for encryption. If lost, all encrypted data (sessions, cookies) becomes unreadable.
- Database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- `ADMIN_EMAILS` -- Controls who can access the `/admin` panel
- Mail configuration (`MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD`)
- reCAPTCHA keys (`RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`)

```bash
# Backup .env (with restricted permissions)
cp /var/www/hilotec/.env /backups/hilotec/env/env_$(date +%Y%m%d_%H%M%S)
chmod 600 /backups/hilotec/env/env_$(date +%Y%m%d_%H%M%S)
```

> **Security:** Store `.env` backups encrypted or in a secure vault. This file contains every secret your application uses. See [Section 6](#6-off-site-backup) for encrypted off-site transfer.

### 3.3 Server Configuration Files

```bash
# Backup web server and PHP configs
tar czf /backups/hilotec/files/server_config_$(date +%Y%m%d_%H%M%S).tar.gz \
  /etc/nginx/sites-available/hilotec* \
  /etc/nginx/snippets/ \
  /etc/php/*/fpm/conf.d/99-hardening.ini \
  /etc/letsencrypt/live/ \
  /etc/letsencrypt/renewal/ \
  2>/dev/null

# Backup crontab
crontab -l > /backups/hilotec/files/crontab_$(date +%Y%m%d_%H%M%S).txt 2>/dev/null || true
```

---

## 4. Full-site Backup Script

Save this as `/var/www/hilotec/scripts/backup-full.sh` and make it executable.

```bash
#!/bin/bash
# =============================================================================
# HILOTEC Website - Full Backup Script
#
# Usage:
#   ./backup-full.sh              # Run full backup
#   ./backup-full.sh --db-only    # Database only
#
# Prerequisites:
#   - Writable backup directory
#   - Database credentials (set variables below or use .env)
#   - Sufficient disk space
#
# Schedule via cron:
#   0 2 * * * /var/www/hilotec/scripts/backup-full.sh >> /var/log/hilotec-backup.log 2>&1
# =============================================================================

set -euo pipefail

# ---------------------------------------------------------------------------
# Configuration - EDIT THESE
# ---------------------------------------------------------------------------
SITE_DIR="/var/www/hilotec"
BACKUP_ROOT="/backups/hilotec"
DB_ENGINE="mysql"          # Options: sqlite, mysql, pgsql
DB_NAME="hilotec"
DB_USER="hilotec_user"
DB_PASS="YOUR_DB_PASSWORD" # Or read from .env (see below)
KEEP_DAYS=30               # Delete local backups older than this

# Alternatively, read credentials from .env:
# DB_PASS=$(grep '^DB_PASSWORD=' "${SITE_DIR}/.env" | cut -d '=' -f2-)
# DB_USER=$(grep '^DB_USERNAME=' "${SITE_DIR}/.env" | cut -d '=' -f2-)
# DB_NAME=$(grep '^DB_DATABASE=' "${SITE_DIR}/.env" | cut -d '=' -f2-)

# ---------------------------------------------------------------------------
# Derived paths
# ---------------------------------------------------------------------------
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DATE_DIR=$(date +%Y-%m-%d)
BACKUP_DIR="${BACKUP_ROOT}/${DATE_DIR}"
LOG_PREFIX="[$(date '+%Y-%m-%d %H:%M:%S')]"

# ---------------------------------------------------------------------------
# Functions
# ---------------------------------------------------------------------------
log() {
    echo "${LOG_PREFIX} $1"
}

fail() {
    log "ERROR: $1"
    exit 1
}

check_disk_space() {
    local available_mb
    available_mb=$(df -m "${BACKUP_ROOT}" | awk 'NR==2 {print $4}')
    if [ "$available_mb" -lt 500 ]; then
        fail "Less than 500 MB free on backup volume. Aborting."
    fi
    log "Disk space OK: ${available_mb} MB available on backup volume"
}

backup_database() {
    local db_file="${BACKUP_DIR}/database_${TIMESTAMP}"

    case "$DB_ENGINE" in
        sqlite)
            local sqlite_path="${SITE_DIR}/database/database.sqlite"
            if [ ! -f "$sqlite_path" ]; then
                fail "SQLite file not found: ${sqlite_path}"
            fi
            sqlite3 "$sqlite_path" ".backup '${db_file}.sqlite'"
            gzip "${db_file}.sqlite"
            log "SQLite backup: database_${TIMESTAMP}.sqlite.gz"
            ;;
        mysql)
            mysqldump \
                --user="$DB_USER" \
                --password="$DB_PASS" \
                --single-transaction \
                --routines \
                --triggers \
                "$DB_NAME" \
                | gzip > "${db_file}.sql.gz"
            log "MySQL backup: database_${TIMESTAMP}.sql.gz"
            ;;
        pgsql)
            PGPASSWORD="$DB_PASS" pg_dump \
                --username="$DB_USER" \
                --host=localhost \
                "$DB_NAME" \
                | gzip > "${db_file}.sql.gz"
            log "PostgreSQL backup: database_${TIMESTAMP}.sql.gz"
            ;;
        *)
            fail "Unknown DB_ENGINE: ${DB_ENGINE}. Use sqlite, mysql, or pgsql."
            ;;
    esac
}

backup_env() {
    cp "${SITE_DIR}/.env" "${BACKUP_DIR}/env_${TIMESTAMP}"
    chmod 600 "${BACKUP_DIR}/env_${TIMESTAMP}"
    log "Environment file backed up"
}

backup_media() {
    local media_dir="${SITE_DIR}/storage/app/public"
    if [ -d "$media_dir" ] && [ "$(ls -A "$media_dir" 2>/dev/null)" ]; then
        tar czf "${BACKUP_DIR}/media_${TIMESTAMP}.tar.gz" \
            -C "$media_dir" .
        log "Media backup: media_${TIMESTAMP}.tar.gz"
    else
        log "No uploaded media found, skipping"
    fi
}

backup_server_config() {
    local config_files=()

    # Collect existing config files
    for f in \
        /etc/nginx/sites-available/hilotec* \
        /etc/nginx/nginx.conf \
        /etc/apache2/sites-available/hilotec* \
        /etc/php/*/fpm/conf.d/99-hardening.ini \
        /etc/php/*/fpm/pool.d/www.conf
    do
        [ -f "$f" ] && config_files+=("$f")
    done

    if [ ${#config_files[@]} -gt 0 ]; then
        tar czf "${BACKUP_DIR}/server_config_${TIMESTAMP}.tar.gz" \
            "${config_files[@]}" 2>/dev/null || true
        log "Server config backed up"
    else
        log "No server config files found, skipping"
    fi

    # SSL certificates (if Let's Encrypt is used)
    if [ -d /etc/letsencrypt/live ]; then
        tar czf "${BACKUP_DIR}/ssl_certs_${TIMESTAMP}.tar.gz" \
            /etc/letsencrypt/live/ \
            /etc/letsencrypt/renewal/ 2>/dev/null || true
        log "SSL certificates backed up"
    fi

    # Crontab
    crontab -l > "${BACKUP_DIR}/crontab_${TIMESTAMP}.txt" 2>/dev/null || true
    log "Crontab saved"
}

backup_quarantine() {
    local quarantine_dir="${SITE_DIR}/storage/quarantine"
    if [ -d "$quarantine_dir" ] && [ "$(ls -A "$quarantine_dir" 2>/dev/null)" ]; then
        tar czf "${BACKUP_DIR}/quarantine_${TIMESTAMP}.tar.gz" \
            -C "$quarantine_dir" .
        log "Quarantine files backed up"
    fi
}

cleanup_old_backups() {
    log "Cleaning up backups older than ${KEEP_DAYS} days..."
    find "${BACKUP_ROOT}" -type f -mtime +${KEEP_DAYS} -delete 2>/dev/null || true
    find "${BACKUP_ROOT}" -type d -empty -delete 2>/dev/null || true
    log "Cleanup completed"
}

create_manifest() {
    local manifest="${BACKUP_DIR}/MANIFEST_${TIMESTAMP}.txt"
    {
        echo "HILOTEC Backup Manifest"
        echo "======================="
        echo "Date:      $(date)"
        echo "Server:    $(hostname)"
        echo "Site dir:  ${SITE_DIR}"
        echo "DB engine: ${DB_ENGINE}"
        echo ""
        echo "Files in this backup:"
        ls -lh "${BACKUP_DIR}/"
        echo ""
        echo "Laravel version:"
        php "${SITE_DIR}/artisan" --version 2>/dev/null || echo "Could not determine"
        echo ""
        echo "PHP version:"
        php -v | head -1
    } > "$manifest"
    log "Manifest created"
}

# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------
log "===== HILOTEC Backup Started ====="

# Ensure backup directory exists
mkdir -p "$BACKUP_DIR"

# Pre-flight checks
check_disk_space

if [ "${1:-}" = "--db-only" ]; then
    backup_database
    log "===== Database-only backup completed ====="
    exit 0
fi

# Full backup
backup_database
backup_env
backup_media
backup_server_config
backup_quarantine
cleanup_old_backups
create_manifest

# Summary
TOTAL_SIZE=$(du -sh "${BACKUP_DIR}" | cut -f1)
log "===== Backup completed: ${BACKUP_DIR} (${TOTAL_SIZE}) ====="
```

Make it executable and create the backup directory:

```bash
chmod +x /var/www/hilotec/scripts/backup-full.sh
mkdir -p /backups/hilotec
```

---

## 5. Backup Rotation & Retention

A good retention strategy balances storage cost against recovery flexibility. Use the following daily/weekly/monthly scheme.

### Strategy

| Tier | Frequency | Retention | How |
|------|-----------|-----------|-----|
| **Daily** | Every day at 02:00 | Keep 7 days | Full backup via `backup-full.sh` |
| **Weekly** | Every Sunday at 03:00 | Keep 4 weeks | Copy daily backup to weekly folder |
| **Monthly** | 1st of each month at 04:00 | Keep 12 months | Copy daily backup to monthly folder |

### Crontab Entries

```cron
# Daily full backup at 02:00
0 2 * * * /var/www/hilotec/scripts/backup-full.sh >> /var/log/hilotec-backup.log 2>&1

# Weekly rotation: copy Sunday's backup to weekly folder
0 3 * * 0 cp -r /backups/hilotec/$(date +\%Y-\%m-\%d) /backups/hilotec-weekly/$(date +\%Y-\%m-\%d) 2>/dev/null

# Monthly rotation: copy 1st-of-month backup to monthly folder
0 4 1 * * cp -r /backups/hilotec/$(date +\%Y-\%m-\%d) /backups/hilotec-monthly/$(date +\%Y-\%m-\%d) 2>/dev/null

# Clean up: daily backups older than 7 days
0 5 * * * find /backups/hilotec -maxdepth 1 -type d -mtime +7 -exec rm -rf {} + 2>/dev/null

# Clean up: weekly backups older than 28 days
0 5 * * 0 find /backups/hilotec-weekly -maxdepth 1 -type d -mtime +28 -exec rm -rf {} + 2>/dev/null

# Clean up: monthly backups older than 365 days
0 5 1 * * find /backups/hilotec-monthly -maxdepth 1 -type d -mtime +365 -exec rm -rf {} + 2>/dev/null
```

### Directory Structure After Several Weeks

```
/backups/
  hilotec/                    # Daily (7 days)
    2026-02-10/
    2026-02-11/
    ...
    2026-02-17/
  hilotec-weekly/             # Weekly (4 weeks)
    2026-01-25/
    2026-02-01/
    2026-02-08/
    2026-02-15/
  hilotec-monthly/            # Monthly (12 months)
    2026-01-01/
    2026-02-01/
```

---

## 6. Off-site Backup

Local backups protect against accidental deletion and database corruption, but not against hardware failure, fire, or ransomware. At least one copy must be stored off-site.

### 6.1 Rsync to a Remote Server

```bash
#!/bin/bash
# /var/www/hilotec/scripts/backup-offsite.sh
# Run after the daily backup completes.

BACKUP_DIR="/backups/hilotec/$(date +%Y-%m-%d)"
REMOTE_USER="backup"
REMOTE_HOST="backup-server.hilotec.ch"
REMOTE_PATH="/backups/hilotec/"

# Rsync with SSH (key-based auth assumed)
rsync -azv --delete \
  "${BACKUP_DIR}/" \
  "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}$(date +%Y-%m-%d)/"

echo "[$(date)] Off-site sync completed to ${REMOTE_HOST}"
```

**Cron entry** (runs 30 minutes after the local backup):

```cron
30 2 * * * /var/www/hilotec/scripts/backup-offsite.sh >> /var/log/hilotec-backup.log 2>&1
```

**SSH key setup** (one-time, on the web server):

```bash
# Generate a dedicated key pair for backups
ssh-keygen -t ed25519 -f ~/.ssh/hilotec_backup -N "" -C "hilotec-backup"

# Copy public key to backup server
ssh-copy-id -i ~/.ssh/hilotec_backup.pub backup@backup-server.hilotec.ch

# Test connection
ssh -i ~/.ssh/hilotec_backup backup@backup-server.hilotec.ch "echo OK"
```

### 6.2 S3-compatible Object Storage (e.g., AWS S3, MinIO, Backblaze B2)

```bash
#!/bin/bash
# /var/www/hilotec/scripts/backup-s3.sh
# Requires: aws-cli (apt install awscli)

BACKUP_DIR="/backups/hilotec/$(date +%Y-%m-%d)"
S3_BUCKET="s3://hilotec-backups"
S3_PREFIX="$(date +%Y-%m-%d)"

# Upload entire backup directory
aws s3 sync \
  "${BACKUP_DIR}/" \
  "${S3_BUCKET}/${S3_PREFIX}/" \
  --storage-class STANDARD_IA \
  --sse AES256

# Apply lifecycle policy to auto-expire old backups
# (Configure this once in the S3 console or with a lifecycle JSON policy)

echo "[$(date)] S3 upload completed to ${S3_BUCKET}/${S3_PREFIX}/"
```

**For non-AWS S3-compatible storage** (MinIO, Backblaze B2), add the endpoint:

```bash
aws s3 sync \
  "${BACKUP_DIR}/" \
  "${S3_BUCKET}/${S3_PREFIX}/" \
  --endpoint-url https://s3.eu-central-1.backblazeb2.com
```

### 6.3 Encrypting Off-site Backups

Backups contain the `.env` file with all secrets. Always encrypt before transferring off-site.

```bash
# Create an encrypted archive of the day's backup
tar czf - -C "/backups/hilotec/$(date +%Y-%m-%d)" . \
  | gpg --symmetric --cipher-algo AES256 --batch --passphrase-file /root/.backup-passphrase \
  > "/tmp/hilotec_$(date +%Y%m%d).tar.gz.gpg"

# Upload encrypted file
aws s3 cp "/tmp/hilotec_$(date +%Y%m%d).tar.gz.gpg" \
  "s3://hilotec-backups/encrypted/"

# Clean up temp file
rm -f "/tmp/hilotec_$(date +%Y%m%d).tar.gz.gpg"
```

**Decrypting** (on recovery):

```bash
gpg --decrypt --batch --passphrase-file /root/.backup-passphrase \
  hilotec_20260217.tar.gz.gpg | tar xzf - -C /restore/hilotec/
```

> **Store the passphrase separately** from the backup. A password manager or a printed copy in a safe are good options.

---

## 7. Testing Backups

A backup that has never been tested is not a backup. Run these verification procedures regularly.

### 7.1 Quick Integrity Check (Weekly)

```bash
#!/bin/bash
# /var/www/hilotec/scripts/backup-verify.sh

BACKUP_DIR="/backups/hilotec/$(date +%Y-%m-%d)"
ERRORS=0

echo "=== HILOTEC Backup Verification: $(date) ==="

# 1. Check backup directory exists and is not empty
if [ ! -d "$BACKUP_DIR" ] || [ -z "$(ls -A "$BACKUP_DIR")" ]; then
    echo "FAIL: Backup directory missing or empty: ${BACKUP_DIR}"
    ERRORS=$((ERRORS + 1))
else
    echo "OK:   Backup directory exists with $(ls -1 "$BACKUP_DIR" | wc -l) files"
fi

# 2. Check database dump is not empty
DB_FILE=$(ls -t "${BACKUP_DIR}"/database_*.gz 2>/dev/null | head -1)
if [ -z "$DB_FILE" ]; then
    echo "FAIL: No database backup found"
    ERRORS=$((ERRORS + 1))
elif [ "$(stat -c%s "$DB_FILE")" -lt 1024 ]; then
    echo "FAIL: Database backup is suspiciously small ($(stat -c%s "$DB_FILE") bytes)"
    ERRORS=$((ERRORS + 1))
else
    echo "OK:   Database backup: $(du -h "$DB_FILE" | cut -f1)"
    # Test that the gzip is not corrupt
    if gzip -t "$DB_FILE" 2>/dev/null; then
        echo "OK:   Database backup gzip integrity verified"
    else
        echo "FAIL: Database backup gzip is corrupt"
        ERRORS=$((ERRORS + 1))
    fi
fi

# 3. Check media backup
MEDIA_FILE=$(ls -t "${BACKUP_DIR}"/media_*.tar.gz 2>/dev/null | head -1)
if [ -n "$MEDIA_FILE" ]; then
    if tar tzf "$MEDIA_FILE" > /dev/null 2>&1; then
        echo "OK:   Media archive integrity verified ($(du -h "$MEDIA_FILE" | cut -f1))"
    else
        echo "FAIL: Media archive is corrupt"
        ERRORS=$((ERRORS + 1))
    fi
else
    echo "WARN: No media backup found (may be OK if no uploads exist)"
fi

# 4. Check .env backup
ENV_FILE=$(ls -t "${BACKUP_DIR}"/env_* 2>/dev/null | head -1)
if [ -z "$ENV_FILE" ]; then
    echo "FAIL: No .env backup found"
    ERRORS=$((ERRORS + 1))
elif ! grep -q "APP_KEY=" "$ENV_FILE"; then
    echo "FAIL: .env backup does not contain APP_KEY"
    ERRORS=$((ERRORS + 1))
else
    echo "OK:   .env backup contains APP_KEY"
fi

# Summary
echo ""
if [ "$ERRORS" -gt 0 ]; then
    echo "RESULT: ${ERRORS} error(s) found. Investigate immediately!"
    exit 1
else
    echo "RESULT: All checks passed."
    exit 0
fi
```

### 7.2 Full Restore Test (Monthly)

Perform a complete restore to a test environment at least once a month. Use a separate VM or Docker container.

```bash
# On a test server or container:

# 1. Extract media
mkdir -p /tmp/hilotec-test/storage/app/public
tar xzf /backups/hilotec/2026-02-17/media_*.tar.gz -C /tmp/hilotec-test/storage/app/public/

# 2. Restore database (MySQL example)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS hilotec_test;"
zcat /backups/hilotec/2026-02-17/database_*.sql.gz | mysql -u root hilotec_test

# 3. Check critical tables have data
mysql -u root hilotec_test -e "
  SELECT 'settings' AS tbl, COUNT(*) AS rows FROM settings
  UNION ALL SELECT 'services', COUNT(*) FROM services
  UNION ALL SELECT 'references', COUNT(*) FROM \`references\`
  UNION ALL SELECT 'team_members', COUNT(*) FROM team_members
  UNION ALL SELECT 'posts', COUNT(*) FROM posts
  UNION ALL SELECT 'pages', COUNT(*) FROM pages
  UNION ALL SELECT 'partners', COUNT(*) FROM partners
  UNION ALL SELECT 'users', COUNT(*) FROM users;
"

# 4. Verify row counts match production
#    (compare output against known production counts)

# 5. Clean up
mysql -u root -e "DROP DATABASE hilotec_test;"
rm -rf /tmp/hilotec-test
```

### 7.3 What to Verify

| Check | Command | Expected |
|-------|---------|----------|
| Database dump decompresses | `gzip -t backup.sql.gz` | Exit code 0 |
| Database can be restored | `zcat ... \| mysql ...` | No errors |
| All critical tables have rows | `SELECT COUNT(*) FROM ...` | Non-zero counts |
| Media archive extracts | `tar tzf media.tar.gz` | Lists files |
| `.env` backup contains `APP_KEY` | `grep APP_KEY env_*` | Non-empty value |
| Backup size is reasonable | `du -sh backup_dir/` | Not drastically different from last run |

---

## 8. Disaster Recovery Procedures

### 8.1 Complete Server Failure (Rebuild from Scratch)

This is the worst-case scenario: the server is gone and you must rebuild from zero.

**Prerequisites:**
- A recent backup (database dump, media archive, `.env` file)
- Access to the git repository
- A fresh Ubuntu 22.04/24.04 server

**Steps:**

```bash
# 1. Set up the server (see docs/02-DEPLOYMENT.md for full details)
sudo apt update && sudo apt upgrade -y
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-sqlite3 php8.3-mysql php8.3-zip php8.3-gd \
  php8.3-intl php8.3-bcmath composer nginx mysql-server unzip git

# 2. Clone the application
cd /var/www
git clone YOUR_REPO_URL hilotec
cd /var/www/hilotec

# 3. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 4. Restore the .env file from backup
cp /path/to/backup/env_YYYYMMDD_HHMMSS /var/www/hilotec/.env
# Verify APP_KEY is present:
grep APP_KEY /var/www/hilotec/.env

# 5. Create the database (MySQL)
mysql -u root -e "CREATE DATABASE hilotec CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE USER 'hilotec_user'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';"
mysql -u root -e "GRANT ALL PRIVILEGES ON hilotec.* TO 'hilotec_user'@'localhost'; FLUSH PRIVILEGES;"

# 6. Restore the database from backup
zcat /path/to/backup/database_YYYYMMDD_HHMMSS.sql.gz | mysql -u root hilotec

# 7. Restore uploaded media
mkdir -p /var/www/hilotec/storage/app/public
tar xzf /path/to/backup/media_YYYYMMDD_HHMMSS.tar.gz -C /var/www/hilotec/storage/app/public/

# 8. Create the storage symlink
cd /var/www/hilotec
php artisan storage:link

# 9. Build frontend assets
npm install
npm run build

# 10. Set permissions
sudo chown -R www-data:www-data /var/www/hilotec/storage /var/www/hilotec/bootstrap/cache
sudo chmod -R 775 /var/www/hilotec/storage /var/www/hilotec/bootstrap/cache

# 11. Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 12. Restore web server configuration from backup
sudo tar xzf /path/to/backup/server_config_*.tar.gz -C /
sudo nginx -t && sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm

# 13. Restore SSL certificates (if backed up)
sudo tar xzf /path/to/backup/ssl_certs_*.tar.gz -C /
sudo systemctl restart nginx
# Or regenerate with Certbot:
# sudo certbot --nginx -d yourdomain.com

# 14. Restore crontab
crontab /path/to/backup/crontab_YYYYMMDD_HHMMSS.txt

# 15. Verify the site is working
curl -I https://yourdomain.com
curl -I https://yourdomain.com/admin
```

**Estimated recovery time:** 30-60 minutes with all backups available.

### 8.2 Database Corruption (Restore from Backup)

Symptoms: 500 errors, "SQLSTATE" errors in `storage/logs/laravel.log`, admin panel shows no content.

```bash
# 1. Put the site in maintenance mode
cd /var/www/hilotec
php artisan down --secret="recovery-token-here"
# Access site during maintenance by visiting: https://yourdomain.com/recovery-token-here

# 2. Identify the latest good backup
ls -lt /backups/hilotec/*/database_*.sql.gz | head -5

# 3. Drop and recreate the database (MySQL)
mysql -u root -e "DROP DATABASE hilotec; CREATE DATABASE hilotec CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "GRANT ALL PRIVILEGES ON hilotec.* TO 'hilotec_user'@'localhost'; FLUSH PRIVILEGES;"

# 4. Restore from the chosen backup
zcat /backups/hilotec/2026-02-17/database_20260217_020000.sql.gz | mysql -u root hilotec

# 5. Clear Laravel caches (the cached data may reference stale DB state)
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 6. Re-optimize
php artisan config:cache
php artisan route:cache

# 7. Bring the site back up
php artisan up

# 8. Verify in the admin panel
# Log in at https://yourdomain.com/admin and check that content is intact
```

**For SQLite:**

```bash
php artisan down
cp /backups/hilotec/2026-02-17/database_20260217_020000.sqlite /var/www/hilotec/database/database.sqlite
chown www-data:www-data /var/www/hilotec/database/database.sqlite
chmod 664 /var/www/hilotec/database/database.sqlite
php artisan cache:clear
php artisan up
```

### 8.3 Accidental Content Deletion (Restore Specific Records)

If someone accidentally deletes a team member, post, or other content via the admin panel, you can restore individual records without a full database restore.

**Option A: Restore specific tables from a dump (MySQL)**

```bash
# 1. Restore the backup into a temporary database
mysql -u root -e "CREATE DATABASE hilotec_temp;"
zcat /backups/hilotec/2026-02-17/database_20260217_020000.sql.gz | mysql -u root hilotec_temp

# 2. Identify the missing records
mysql -u root hilotec_temp -e "SELECT id, name FROM team_members;"

# 3. Copy specific records from temp to production
mysql -u root -e "
INSERT INTO hilotec.team_members
SELECT * FROM hilotec_temp.team_members
WHERE id = 5;
"

# 4. If the record references uploaded files, restore those too
# Check the record's image column for the file path, then:
tar xzf /backups/hilotec/2026-02-17/media_*.tar.gz -C /tmp/media_restore/
cp /tmp/media_restore/team/missing-photo.jpg /var/www/hilotec/storage/app/public/team/

# 5. Clean up
mysql -u root -e "DROP DATABASE hilotec_temp;"
rm -rf /tmp/media_restore
```

**Option B: Use Laravel Tinker (quick single-record inspection)**

```bash
cd /var/www/hilotec

# List all team members currently in the database
php artisan tinker --execute="App\Models\TeamMember::all(['id','name'])->dump();"

# Check what is in a backup database (SQLite example)
sqlite3 /backups/hilotec/2026-02-17/database_20260217_020000.sqlite \
  "SELECT id, name FROM team_members;"
```

### 8.4 Compromised Server (Clean Restore with Security Audit)

If you suspect the server has been compromised (defaced pages, unknown files, suspicious processes), perform a clean rebuild rather than trying to patch the existing installation.

```bash
# 1. IMMEDIATELY: Take the site offline
cd /var/www/hilotec
php artisan down

# 2. Preserve evidence (BEFORE restoring anything)
tar czf /tmp/compromised_$(date +%Y%m%d_%H%M%S).tar.gz \
  /var/www/hilotec/storage/logs/ \
  /var/www/hilotec/public/ \
  /var/log/nginx/ \
  /var/log/auth.log \
  /var/log/syslog
# Move evidence archive off-server for later analysis

# 3. Run the built-in security audit (if the artisan command is still trustworthy)
php artisan security:audit --fix
# This scans public/ for unauthorized files and quarantines them to storage/quarantine/

# 4. If compromise is severe, rebuild from scratch
#    Follow the "Complete Server Failure" procedure in Section 8.1
#    Use a FRESH server or completely wipe and reinstall the OS

# 5. Use a backup from BEFORE the compromise
#    Check logs to determine when the breach occurred
#    Restore from the last clean backup

# 6. After restore, harden:
#    - Change ALL passwords (database, admin users, SSH keys, .env secrets)
#    - Generate a new APP_KEY (see Section 8.5)
#    - Update ADMIN_EMAILS if accounts were compromised
#    - Review and update all SSH authorized_keys
#    - Apply all OS and PHP security updates
#    - Review docs/02-DEPLOYMENT.md for hardening checklist
```

### 8.5 Lost `.env` / `APP_KEY` (Implications and Recovery)

The `APP_KEY` is a 32-character encryption key used by Laravel for:
- Encrypting session data
- Encrypting cookies
- Any data encrypted with `Crypt::encrypt()`

**If `.env` is lost but you have a backup:**

```bash
# Restore from the most recent .env backup
cp /backups/hilotec/2026-02-17/env_20260217_020000 /var/www/hilotec/.env
chmod 600 /var/www/hilotec/.env
chown www-data:www-data /var/www/hilotec/.env
```

**If `APP_KEY` is lost entirely (no backup exists):**

```bash
cd /var/www/hilotec

# Generate a new APP_KEY
php artisan key:generate
```

**Consequences of a new `APP_KEY`:**
- All existing sessions are invalidated (all admin users must log in again)
- All encrypted cookies become unreadable (browser sessions break -- users just need to clear cookies)
- Any data encrypted with `Crypt::encrypt()` in the database cannot be decrypted

**For this specific application:** The HILOTEC site stores most content as plain text in the database (services, references, posts, settings). Generating a new `APP_KEY` will **not** destroy content data. The main impact is that all admin sessions are invalidated and users must log in again.

**If the full `.env` is lost and no backup exists**, you must reconstruct it manually:

```bash
# 1. Start from the production example
cp /var/www/hilotec/.env.production.example /var/www/hilotec/.env

# 2. Generate a new APP_KEY
php artisan key:generate

# 3. Fill in database credentials
#    Edit .env and set DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Set required values
#    APP_URL=https://yourdomain.com
#    ADMIN_EMAILS=admin@hilotec.com  (comma-separated admin email addresses)
#    MAIL_* settings for your mail provider
#    RECAPTCHA_SITE_KEY and RECAPTCHA_SECRET_KEY (from Google reCAPTCHA console)

# 5. Restart PHP-FPM to pick up the new config
sudo systemctl restart php8.3-fpm
```

> **Lesson:** Always back up the `.env` file. Always.

---

## 9. Laravel-specific Backup Package (spatie/laravel-backup)

For a more integrated solution, consider the [spatie/laravel-backup](https://spatie.be/docs/laravel-backup) package. It handles database dumps, file backups, cleanup strategies, and monitoring notifications from within Laravel itself.

### Installation

```bash
cd /var/www/hilotec
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

This creates `config/backup.php` where you configure what to back up and where to store it.

### Configuration

Edit `config/backup.php`:

```php
// Key sections to configure:

'backup' => [
    'name' => 'hilotec',

    'source' => [
        'files' => [
            'include' => [
                storage_path('app/public'),  // Uploaded media (posts, team, partners)
                base_path('.env'),            // Environment file
            ],
            'exclude' => [
                storage_path('app/public/cache'),
            ],
        ],

        'databases' => ['mysql'],  // or 'sqlite', 'pgsql'
    ],

    'destination' => [
        'disks' => ['local'],  // Add 's3' for off-site
    ],
],

'cleanup' => [
    'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
    'default_strategy' => [
        'keep_all_backups_for_days'                            => 7,
        'keep_daily_backups_for_days'                          => 30,
        'keep_weekly_backups_for_weeks'                        => 8,
        'keep_monthly_backups_for_months'                      => 4,
        'keep_yearly_backups_for_years'                        => 0,
        'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
    ],
],

'monitor_backups' => [
    [
        'name' => 'hilotec',
        'disks' => ['local'],
        'health_checks' => [
            \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
            \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
        ],
    ],
],
```

### Scheduling

Add to `routes/console.php` (or `app/Console/Kernel.php`):

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run')->dailyAt('02:00');
Schedule::command('backup:clean')->dailyAt('03:00');
Schedule::command('backup:monitor')->dailyAt('03:30');
```

Make sure the Laravel scheduler is running via cron:

```cron
* * * * * cd /var/www/hilotec && php artisan schedule:run >> /dev/null 2>&1
```

### Running Manually

```bash
# Full backup (database + files)
php artisan backup:run

# Database only
php artisan backup:run --only-db

# Files only
php artisan backup:run --only-files

# List existing backups
php artisan backup:list

# Clean old backups
php artisan backup:clean

# Check backup health
php artisan backup:monitor
```

### Notifications

The package can send notifications via mail, Slack, or other channels when:
- A backup succeeds or fails
- A backup health check fails
- Cleanup completes

Configure in `config/backup.php` under the `notifications` key.

### S3 Off-site with Spatie

In `config/filesystems.php`, add an S3 disk, then include `'s3'` in the `destination.disks` array in `config/backup.php`. The package handles the upload automatically.

> **Recommendation:** Even if you use `spatie/laravel-backup`, keep the standalone bash scripts as a fallback. If the application is broken (e.g., Composer dependencies are corrupt), the bash scripts still work.

---

## 10. Recovery Checklist

Print this section and keep it with your server documentation.

### Checklist A: Complete Server Rebuild

```
[ ] 1. Provision fresh server (Ubuntu 22.04/24.04)
[ ] 2. Install PHP 8.3+, Composer, Nginx/Apache, MySQL/PostgreSQL
[ ] 3. Clone git repository to /var/www/hilotec
[ ] 4. Run: composer install --no-dev --optimize-autoloader
[ ] 5. Restore .env from backup
[ ] 6. Verify APP_KEY is present in .env
[ ] 7. Create database and database user
[ ] 8. Restore database from backup
[ ] 9. Restore uploaded media to storage/app/public/
[ ] 10. Run: php artisan storage:link
[ ] 11. Run: npm install && npm run build
[ ] 12. Set permissions: chown -R www-data:www-data storage/ bootstrap/cache/
[ ] 13. Run: php artisan config:cache && php artisan route:cache && php artisan view:cache
[ ] 14. Restore web server configuration (Nginx/Apache)
[ ] 15. Restore or regenerate SSL certificates
[ ] 16. Test: curl -I https://yourdomain.com (expect 200)
[ ] 17. Test: log in to https://yourdomain.com/admin
[ ] 18. Verify all content appears: services, references, team, posts
[ ] 19. Verify uploaded images display correctly
[ ] 20. Restore crontab (backup jobs + Laravel scheduler)
[ ] 21. Send test contact form submission
[ ] 22. Run: php artisan security:audit
```

### Checklist B: Database Restore Only

```
[ ] 1. Put site in maintenance mode: php artisan down
[ ] 2. Identify correct backup file (check dates, file sizes)
[ ] 3. Drop and recreate database
[ ] 4. Restore database from backup
[ ] 5. Clear caches: php artisan cache:clear && php artisan config:clear
[ ] 6. Bring site up: php artisan up
[ ] 7. Verify content in admin panel
[ ] 8. Verify public-facing pages load correctly
```

### Checklist C: Compromised Server

```
[ ] 1. Take site offline immediately: php artisan down
[ ] 2. Preserve evidence (logs, modified files) to external storage
[ ] 3. Run: php artisan security:audit --fix (if artisan is trustworthy)
[ ] 4. Determine date/time of compromise from logs
[ ] 5. Select backup from before compromise date
[ ] 6. Rebuild server from scratch (follow Checklist A)
[ ] 7. Change ALL passwords: database, admin users, SSH keys
[ ] 8. Generate new APP_KEY: php artisan key:generate
[ ] 9. Update ADMIN_EMAILS in .env
[ ] 10. Revoke and regenerate all API keys (reCAPTCHA, mail provider)
[ ] 11. Review authorized_keys on all accounts
[ ] 12. Apply all OS and PHP security updates
[ ] 13. Run: php artisan security:audit to verify clean state
[ ] 14. Monitor logs closely for 48 hours after restore
```

### Checklist D: Lost `.env` File

```
[ ] 1. Check for .env backup: ls /backups/hilotec/*/env_*
[ ] 2. If found: restore and restart PHP-FPM
[ ] 3. If NOT found:
       [ ] a. Copy .env.production.example to .env
       [ ] b. Generate new APP_KEY: php artisan key:generate
       [ ] c. Set DB_* credentials (check database server for existing databases)
       [ ] d. Set APP_URL to the correct domain
       [ ] e. Set ADMIN_EMAILS
       [ ] f. Configure MAIL_* settings
       [ ] g. Set RECAPTCHA keys (from Google reCAPTCHA console)
       [ ] h. Restart PHP-FPM
[ ] 4. Test admin login at /admin
[ ] 5. Test contact form submission
[ ] 6. Immediately create a new backup of the .env file
```

---

## 11. Backup Monitoring

Backups that silently fail are worse than no backups at all -- they give you false confidence. Set up monitoring to catch failures early.

### 11.1 Log File Monitoring

The backup scripts above log to `/var/log/hilotec-backup.log`. Check for failures:

```bash
# Check for errors in today's log
grep -i "error\|fail\|abort" /var/log/hilotec-backup.log

# Check that today's backup ran
grep "$(date +%Y-%m-%d)" /var/log/hilotec-backup.log
```

### 11.2 Automated Monitoring Script

Save as `/var/www/hilotec/scripts/backup-monitor.sh`:

```bash
#!/bin/bash
# Check that backups are current and send alerts if not.
# Schedule: 0 8 * * * /var/www/hilotec/scripts/backup-monitor.sh

BACKUP_ROOT="/backups/hilotec"
ALERT_EMAIL="admin@hilotec.com"
MAX_AGE_HOURS=26  # Alert if newest backup is older than 26 hours

# Find the most recent backup directory
LATEST_DIR=$(ls -dt "${BACKUP_ROOT}"/2* 2>/dev/null | head -1)

if [ -z "$LATEST_DIR" ]; then
    echo "CRITICAL: No backup directories found in ${BACKUP_ROOT}" \
      | mail -s "[HILOTEC] Backup MISSING" "$ALERT_EMAIL"
    exit 1
fi

# Check the age of the most recent database backup
LATEST_DB=$(find "$LATEST_DIR" -name "database_*" -type f 2>/dev/null | sort -r | head -1)

if [ -z "$LATEST_DB" ]; then
    echo "CRITICAL: Latest backup directory (${LATEST_DIR}) contains no database dump" \
      | mail -s "[HILOTEC] Backup INCOMPLETE" "$ALERT_EMAIL"
    exit 1
fi

# Check age
FILE_AGE_SECONDS=$(( $(date +%s) - $(stat -c %Y "$LATEST_DB") ))
FILE_AGE_HOURS=$(( FILE_AGE_SECONDS / 3600 ))

if [ "$FILE_AGE_HOURS" -gt "$MAX_AGE_HOURS" ]; then
    echo "WARNING: Latest database backup is ${FILE_AGE_HOURS} hours old (threshold: ${MAX_AGE_HOURS}h)
File: ${LATEST_DB}
Directory: ${LATEST_DIR}" \
      | mail -s "[HILOTEC] Backup STALE" "$ALERT_EMAIL"
    exit 1
fi

# Check minimum file size (a valid MySQL dump for this site should be at least 10KB)
FILE_SIZE=$(stat -c%s "$LATEST_DB")
if [ "$FILE_SIZE" -lt 10240 ]; then
    echo "WARNING: Latest database backup is only ${FILE_SIZE} bytes (expected >10KB).
Possible empty or corrupt backup.
File: ${LATEST_DB}" \
      | mail -s "[HILOTEC] Backup SUSPICIOUS SIZE" "$ALERT_EMAIL"
    exit 1
fi

echo "[$(date)] Backup monitoring: OK (latest: ${LATEST_DB}, age: ${FILE_AGE_HOURS}h, size: ${FILE_SIZE} bytes)"
exit 0
```

### 11.3 Cron Entry for Monitoring

```cron
# Run backup monitor every morning at 08:00
0 8 * * * /var/www/hilotec/scripts/backup-monitor.sh >> /var/log/hilotec-backup.log 2>&1
```

### 11.4 Disk Space Monitoring

Backups grow over time. Monitor available disk space:

```bash
# Add to crontab: alert if backup volume drops below 1 GB
0 9 * * * [ $(df -m /backups | awk 'NR==2{print $4}') -lt 1024 ] && echo "Backup disk below 1 GB free" | mail -s "[HILOTEC] Backup Disk LOW" admin@hilotec.com
```

### 11.5 External Health Check (Optional)

For critical production sites, use an external "dead man's switch" service (e.g., Healthchecks.io, Cronitor, or UptimeRobot). These services expect a periodic ping; if the ping stops, they alert you.

Add to the end of `backup-full.sh`:

```bash
# Ping healthcheck endpoint on success
curl -fsS --retry 3 https://hc-ping.com/YOUR-UUID-HERE > /dev/null
```

If the backup script fails or never runs, the service notices the missing ping and sends an alert.

---

## Quick Reference: Complete Crontab

Here is the full crontab for a production HILOTEC server with all backup and monitoring jobs:

```cron
# Laravel scheduler (required for queued jobs, scheduled commands)
* * * * * cd /var/www/hilotec && php artisan schedule:run >> /dev/null 2>&1

# Daily full backup at 02:00
0 2 * * * /var/www/hilotec/scripts/backup-full.sh >> /var/log/hilotec-backup.log 2>&1

# Off-site sync at 02:30
30 2 * * * /var/www/hilotec/scripts/backup-offsite.sh >> /var/log/hilotec-backup.log 2>&1

# Weekly rotation: Sunday at 03:00
0 3 * * 0 cp -r /backups/hilotec/$(date +\%Y-\%m-\%d) /backups/hilotec-weekly/$(date +\%Y-\%m-\%d) 2>/dev/null

# Monthly rotation: 1st of month at 04:00
0 4 1 * * cp -r /backups/hilotec/$(date +\%Y-\%m-\%d) /backups/hilotec-monthly/$(date +\%Y-\%m-\%d) 2>/dev/null

# Cleanup: daily older than 7 days
0 5 * * * find /backups/hilotec -maxdepth 1 -type d -name "20*" -mtime +7 -exec rm -rf {} + 2>/dev/null

# Cleanup: weekly older than 28 days
0 5 * * 0 find /backups/hilotec-weekly -maxdepth 1 -type d -mtime +28 -exec rm -rf {} + 2>/dev/null

# Cleanup: monthly older than 365 days
0 5 1 * * find /backups/hilotec-monthly -maxdepth 1 -type d -mtime +365 -exec rm -rf {} + 2>/dev/null

# Backup verification at 06:00
0 6 * * * /var/www/hilotec/scripts/backup-verify.sh >> /var/log/hilotec-backup.log 2>&1

# Backup monitoring at 08:00
0 8 * * * /var/www/hilotec/scripts/backup-monitor.sh >> /var/log/hilotec-backup.log 2>&1

# Disk space alert at 09:00
0 9 * * * [ $(df -m /backups | awk 'NR==2{print $4}') -lt 1024 ] && echo "Backup disk below 1 GB" | mail -s "[HILOTEC] Backup Disk LOW" admin@hilotec.com
```
