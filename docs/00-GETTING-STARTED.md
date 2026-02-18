# Quick Start Guide

This guide walks you through setting up the HILOTEC corporate website on a local development machine. The audience is a sysadmin who may not be familiar with Laravel -- every step is explained.

## Prerequisites

Before you begin, make sure the following software is installed:

- [ ] **PHP 8.3+** with these extensions: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`
- [ ] **Composer 2.x** -- PHP's package manager ([getcomposer.org](https://getcomposer.org))
- [ ] **Node.js 18+** and **npm** -- for building frontend assets ([nodejs.org](https://nodejs.org))
- [ ] **SQLite 3** -- the default database for local development
- [ ] **Git** -- to clone the repository

To verify your versions:

```bash
php -v            # Should show 8.3 or higher
composer -V       # Should show 2.x
node -v           # Should show v18 or higher
npm -v            # Any version bundled with Node 18+ is fine
sqlite3 --version # Any recent version works
```

To check which PHP extensions are installed:

```bash
php -m | grep -iE 'pdo_sqlite|mbstring|openssl|tokenizer|xml|ctype|json|fileinfo'
```

You should see all eight listed. If any are missing, see [Troubleshooting](#common-issues-and-fixes) below.

---

## Branches

This repository has two main branches with different designs:

| Branch | Description |
|-------------|-------------|
| `master` | Original dark theme with full security hardening (SecurityHeaders, ThrottleAdminLogin, SecurityAudit, hardened .htaccess, deploy.sh, session hardening). The admin panel requires the `ADMIN_EMAILS` environment variable to be set. |
| `design-v2` | Modern hybrid light/dark design refresh with Alpine Precision theme, scroll animations, and animated counters. Security hardening has **not** been applied to this branch yet -- any authenticated user can access the admin panel. |

Pick the branch you want to work with before running the setup steps below. If you are unsure, start with `master`.

---

## Setup (10 Steps)

### Step 1 -- Clone the repository

```bash
git clone <repository-url> hilotec
cd hilotec
```

To use the `design-v2` branch instead of `master`:

```bash
git checkout design-v2
```

### Step 2 -- Create the environment file

Laravel reads its configuration from a `.env` file. Copy the example:

```bash
cp .env.example .env
```

### Step 3 -- Configure the admin email (master branch only)

> **Warning:** On the `master` branch, the admin panel checks the `ADMIN_EMAILS` environment variable to determine who can log in. If you skip this step, you will be able to authenticate but Filament will return a 403 Forbidden error.
>
> **On the `design-v2` branch, this step is not needed.** The `design-v2` branch does not implement the `FilamentUser` interface or `canAccessPanel()`, so any authenticated user can access the admin panel without configuring `ADMIN_EMAILS`.

Open `.env` in a text editor and add this line anywhere in the file:

```
ADMIN_EMAILS=admin@hilotec.com
```

You can list multiple emails separated by commas (e.g., `admin@hilotec.com,you@example.com`).

### Step 4 -- Create the SQLite database file

Laravel expects the database file to exist before running migrations:

```bash
touch database/database.sqlite
```

### Step 5 -- Install PHP dependencies

Composer will download Laravel, Filament, and all other PHP packages:

```bash
composer install
```

This typically takes 1-2 minutes on the first run.

### Step 6 -- Install frontend dependencies

npm will download Tailwind CSS, Alpine.js, Vite, and other frontend packages:

```bash
npm install
```

### Step 7 -- Generate the application key

Laravel uses an encryption key for sessions, cookies, and other security features. Generate one:

```bash
php artisan key:generate
```

This writes a random `APP_KEY=base64:...` value into your `.env` file.

### Step 8 -- Run database migrations and seed content

This creates all database tables and populates them with the full website content (services, references, team members, blog posts, settings, and the admin user):

```bash
php artisan migrate --seed
```

The seeder creates one admin account: **admin@hilotec.com** with password **password**.

> **Warning:** Change this default password immediately after your first login. Anyone with access to this guide knows the credentials.

### Step 9 -- Build frontend assets

Vite compiles the CSS (Tailwind) and JavaScript (Alpine.js) into optimized bundles:

```bash
npm run build
```

The output goes to `public/build/` and is loaded automatically via Laravel's `@vite()` directive.

### Step 10 -- Start the development server

```bash
php artisan serve
```

The site is now running at **http://localhost:8000**.

> **Tip:** For a richer development experience with hot-reloading CSS/JS, file watching, queue processing, and log tailing, run `composer dev` instead. This starts four services in parallel (web server, queue worker, log viewer, and Vite dev server).

---

## Accessing the Admin Panel

1. Open **http://localhost:8000/admin** in your browser
2. Log in with:
   - **Email:** admin@hilotec.com
   - **Password:** password
3. You will see the Filament dashboard with navigation on the left sidebar

> **Warning:** Change the default password immediately. Go to the account icon in the top-right corner of the admin panel to update your profile.

From the admin panel you can manage all website content: services (Leistungen), references (Referenzen), blog posts (Beitr&auml;ge), team members, partners, static pages, contact form submissions, and site-wide settings.

---

## Verify It Works

After completing the setup, confirm everything is functioning:

- [ ] **Homepage loads:** Open http://localhost:8000 -- you should see the HILOTEC corporate homepage
- [ ] **Navigation works:** Click through the German-language menu items (Angebot, Referenzen, Aktuelles, Kontakt, etc.)
- [ ] **Admin panel loads:** Open http://localhost:8000/admin -- you should see a login form
- [ ] **Admin login works:** Log in with the credentials above -- you should see the Filament dashboard
- [ ] **Content is seeded:** In the admin panel, navigate to Leistungen (Services) -- you should see 8 service entries
- [ ] **Assets are compiled:** The pages should have proper styling (custom fonts, gold accent colors, dark background on `master`). If everything looks unstyled, re-run `npm run build`

---

## Common Issues and Fixes

### "Could not find driver" or PDO errors

**Cause:** The `pdo_sqlite` PHP extension is not installed or not enabled.

**Fix (Ubuntu/Debian):**
```bash
sudo apt install php8.3-sqlite3
sudo phpenmod pdo_sqlite
```

**Fix (macOS with Homebrew):**
```bash
brew install php   # SQLite support is included by default
```

After installing, verify with `php -m | grep pdo_sqlite`.

### Missing PHP extensions (mbstring, xml, fileinfo, etc.)

**Fix (Ubuntu/Debian):**
```bash
sudo apt install php8.3-mbstring php8.3-xml php8.3-fileinfo php8.3-curl
```

Replace `8.3` with your PHP version number.

### 403 Forbidden on the admin panel after login

> **Note:** This issue only applies to the `master` branch. On `design-v2`, there is no `ADMIN_EMAILS` restriction -- any authenticated user can access the admin panel.

**Cause:** On the `master` branch, the `User` model implements `FilamentUser` and its `canAccessPanel()` method checks whether your email address is listed in the `ADMIN_EMAILS` environment variable.

**Fix:** Add this line to your `.env` file:

```
ADMIN_EMAILS=admin@hilotec.com
```

Then clear the config cache:

```bash
php artisan config:clear
```

### "No application encryption key has been specified"

**Cause:** You skipped Step 7 or the key generation failed silently.

**Fix:**
```bash
php artisan key:generate
```

### Styles are missing / page looks unstyled

**Cause:** Frontend assets were not compiled, or an old build is cached.

**Fix:**
```bash
npm run build
```

If you are running the Vite dev server (`npm run dev`), make sure it is still running -- Vite serves assets on-the-fly during development.

### "SQLSTATE[HY000]: General error: 8 attempt to write a readonly database"

**Cause:** The web server process does not have write permissions to the SQLite database file or its parent directory.

**Fix:**
```bash
chmod 664 database/database.sqlite
chmod 775 database/
```

### Storage directory permission errors

**Cause:** Laravel needs to write to several directories for logs, cache, and file uploads.

**Fix:**
```bash
chmod -R 775 storage/ bootstrap/cache/
```

### "Your Composer dependencies require a PHP version >= 8.3.0"

**Cause:** Your system's default PHP version is older than 8.3.

**Fix:** Install PHP 8.3+ and make sure it is the active version:

```bash
php -v   # Check current version
```

On Ubuntu, you can install a newer PHP via the `ppa:ondrej/php` repository. On macOS, use `brew install php`.

---

## What's Next

- **[03-ADMIN-GUIDE.md](03-ADMIN-GUIDE.md)** -- How to manage website content through the admin panel
- **[04-TECHNICAL.md](04-TECHNICAL.md)** -- Architecture, database schema, and code conventions
- **[05-DESIGN-SYSTEM.md](05-DESIGN-SYSTEM.md)** -- Tailwind theme tokens, Blade components, and design patterns
- **[02-DEPLOYMENT.md](02-DEPLOYMENT.md)** -- Production deployment instructions
