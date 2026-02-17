#!/bin/bash
# =============================================================================
# Hardened Deployment Script for Laravel Applications
#
# Usage: Copy to your project root, edit SITE_DIR, make executable:
#   chmod +x deploy.sh
#
# Use in Laravel Forge, GitHub Actions, or any CI/CD pipeline.
# =============================================================================

set -euo pipefail

# CUSTOMIZE: Set to your server path
SITE_DIR="${FORGE_SITE_PATH:-/home/forge/yoursite.com}"
BRANCH="${FORGE_SITE_BRANCH:-master}"
PHP_BIN="${FORGE_PHP:-php}"
COMPOSER_BIN="${FORGE_COMPOSER:-composer}"

cd "$SITE_DIR"

[ -d .git ] || { echo "ERROR: Git repo not found at $SITE_DIR"; exit 1; }

# =============================================================================
# 1. FETCH LATEST CODE
# =============================================================================
echo "=== Fetching latest code ==="
git fetch --depth=1 origin "$BRANCH"
git reset --hard "origin/$BRANCH"

# =============================================================================
# 2. SECURITY: Clean injected/unauthorized files from public/
#    This removes ANY file not tracked by git (except build/ and storage/)
# =============================================================================
echo "=== Cleaning unauthorized files from public/ ==="
git clean -fd public/ --exclude=public/build --exclude=public/storage

# Extra safety: explicitly remove PHP files from asset directories
find public/css/ public/js/ public/images/ public/fonts/ \
    -name '*.php' -o -name '*.phtml' -o -name '*.php5' \
    -type f -delete 2>/dev/null || true

# Remove .htaccess files injected into subdirectories
find public/ -mindepth 2 -name '.htaccess' -type f -delete 2>/dev/null || true

# =============================================================================
# 3. PHP DEPENDENCIES
# =============================================================================
echo "=== Installing PHP dependencies ==="
$COMPOSER_BIN install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# =============================================================================
# 4. FRONTEND ASSETS (if public/build is gitignored)
# =============================================================================
if grep -q '/public/build' .gitignore 2>/dev/null; then
    echo "=== Building frontend assets ==="
    npm ci
    npm run build
else
    echo "=== Skipping frontend build (build/ is tracked in git) ==="
fi

# =============================================================================
# 5. LARAVEL OPTIMIZATIONS
# =============================================================================
echo "=== Running Laravel optimizations ==="
if [ -f artisan ]; then
    $PHP_BIN artisan config:cache
    $PHP_BIN artisan route:cache || true
    $PHP_BIN artisan view:cache || true
    $PHP_BIN artisan migrate --force || true

    # Run security audit
    echo "=== Running security audit ==="
    $PHP_BIN artisan security:audit || true
fi

# =============================================================================
# 6. SECURITY: Lock down file permissions
# =============================================================================
echo "=== Setting file permissions ==="

# Public directory: read-only for web server
find public/ -type f -exec chmod 644 {} \;
find public/ -type d -exec chmod 755 {} \;

# Storage and cache: writable by web server
chmod -R 775 storage/ bootstrap/cache/

# .env: readable only by owner and group
chmod 640 .env 2>/dev/null || true

echo "=== Deployment complete ==="
