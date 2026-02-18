# Local Development Environment

This guide walks you through setting up the HILOTEC website project on your local machine, from zero to a fully running development server. It is written for developers who may be new to Laravel or this particular stack.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Step-by-Step Setup](#step-by-step-setup)
3. [Understanding the Dev Workflow](#understanding-the-dev-workflow)
4. [Working with Branches](#working-with-branches)
5. [Essential Artisan Commands](#essential-artisan-commands)
6. [IDE Setup Recommendations](#ide-setup-recommendations)
7. [Common Dev Pitfalls](#common-dev-pitfalls)

---

## Prerequisites

You need five pieces of software installed before you begin. Below are installation instructions for both macOS (Homebrew) and Ubuntu/Debian (apt).

### PHP 8.3+

Laravel 12 and PHPUnit 12 require PHP 8.3 or higher. The following PHP extensions are required by the framework and this project specifically:

| Extension      | Why It Is Needed                                             |
|----------------|--------------------------------------------------------------|
| `ctype`        | Character type checking (Laravel validation)                 |
| `curl`         | HTTP requests (Composer, external APIs)                      |
| `dom`          | XML/HTML parsing (used internally by Laravel)                |
| `fileinfo`     | MIME type detection for file uploads (Filament media)        |
| `filter`       | Input filtering and validation                               |
| `hash`         | Hashing (passwords, CSRF tokens)                             |
| `mbstring`     | Multibyte string handling (UTF-8 support)                    |
| `openssl`      | Encryption, HTTPS, app key generation                        |
| `pcre`         | Regular expressions (routing, validation)                    |
| `pdo`          | Database abstraction layer                                   |
| `pdo_sqlite`   | SQLite driver -- this project uses SQLite for local dev      |
| `sqlite3`      | SQLite3 library (required alongside pdo_sqlite)              |
| `session`      | Session handling                                             |
| `tokenizer`    | PHP code tokenization (used by artisan, Pint)                |
| `xml`          | XML parsing (PHPUnit, various packages)                      |
| `intl`         | Internationalization (date/number formatting)                |
| `gd`           | Image processing (Filament image uploads, thumbnails)        |
| `bcmath`       | Arbitrary precision math                                     |

**macOS (Homebrew):**

```bash
brew install php@8.3
```

Homebrew's PHP formula ships with most extensions enabled by default. Verify with:

```bash
php -v          # Should show 8.3.x or higher
php -m          # Lists all loaded extensions
```

If you need a specific extension that is missing:

```bash
pecl install <extension-name>
```

**Ubuntu / Debian (apt):**

```bash
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-curl \
    php8.3-mbstring php8.3-xml php8.3-zip php8.3-sqlite3 \
    php8.3-gd php8.3-intl php8.3-bcmath php8.3-tokenizer \
    php8.3-fileinfo php8.3-dom
```

> **Note:** On Ubuntu, you may need to add the `ondrej/php` PPA first if PHP 8.3 is not available in the default repositories:
>
> ```bash
> sudo add-apt-repository ppa:ondrej/php
> sudo apt update
> ```

---

### Composer 2.x

Composer is PHP's dependency manager. It reads `composer.json` and installs all PHP packages (Laravel, Filament, etc.) into the `vendor/` directory.

**macOS (Homebrew):**

```bash
brew install composer
```

**Ubuntu / Debian:**

```bash
# Download and install globally
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"
```

Verify:

```bash
composer --version   # Should show 2.x
```

---

### Node.js 18+

Node.js is required for the frontend build toolchain: Vite 7 (bundler), Tailwind CSS 4, and Alpine.js. We recommend using **nvm** (Node Version Manager) so you can switch Node versions across projects without conflicts.

**Install nvm (both platforms):**

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.0/install.sh | bash
```

Close and reopen your terminal, then:

```bash
nvm install 18
nvm use 18
nvm alias default 18
```

**Alternative -- macOS (Homebrew):**

```bash
brew install node@18
```

**Alternative -- Ubuntu / Debian (apt):**

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

Verify:

```bash
node -v    # Should show v18.x or higher
npm -v     # Should show 9.x or higher
```

---

### SQLite

This project uses SQLite for local development. SQLite is a file-based database -- no server process to manage, no ports to configure. The entire database lives in a single file at `database/database.sqlite`.

**macOS:** SQLite comes preinstalled. Nothing to do.

**Ubuntu / Debian:**

```bash
sudo apt install -y sqlite3
```

---

### Git

**macOS (Homebrew):**

```bash
brew install git
```

**Ubuntu / Debian:**

```bash
sudo apt install -y git
```

---

## Step-by-Step Setup

Follow these steps in order. Each step builds on the previous one.

### 1. Clone the Repository

```bash
git clone <repository-url>
cd hilotec
```

### 2. Create Your Environment File

```bash
cp .env.example .env
```

**What is `.env`?** Laravel uses a `.env` file for environment-specific configuration -- database credentials, app URL, debug mode, API keys, etc. The `.env.example` file is a template checked into version control; your personal `.env` is gitignored so you never accidentally commit secrets. Each developer has their own copy.

### 3. Install PHP Dependencies

```bash
composer install
```

This reads `composer.json` and `composer.lock`, then downloads every PHP package the project needs (Laravel framework, Filament admin panel, testing tools, etc.) into the `vendor/` directory. The `composer.lock` file ensures every developer gets the exact same versions -- never edit it by hand.

> **Tip:** If you see errors about missing PHP extensions, revisit the [Prerequisites](#php-82) section and install the missing extension.

### 4. Generate the Application Key

```bash
php artisan key:generate
```

This creates a random 32-character encryption key and writes it to `APP_KEY=` in your `.env` file. Laravel uses this key for:

- Encrypting session data
- Generating CSRF tokens
- Encrypting any data you store via the `Crypt` facade

Without this key, Laravel will refuse to start. **Never share your app key or commit it to version control.**

### 5. Create the SQLite Database File

```bash
touch database/database.sqlite
```

The `.env.example` already sets `DB_CONNECTION=sqlite`. Laravel looks for the database file at `database/database.sqlite` by convention. The `touch` command creates an empty file -- the next step will fill it with tables.

### 6. Run Database Migrations

```bash
php artisan migrate
```

Migrations are version-controlled database schema definitions. Each migration file in `database/migrations/` describes a table to create or modify. Running `migrate` executes all pending migrations in chronological order, creating the following tables:

- `users` -- admin user accounts
- `settings` -- site-wide key/value configuration
- `services` -- service offerings (Angebot)
- `reference_categories` -- groupings for client references
- `references` -- client project references (Referenzen)
- `team_members` -- team member profiles (Ueber Uns)
- `posts` -- blog/news articles (Aktuelles)
- `pages` -- generic CMS pages (Impressum, Datenschutz)
- `partners` -- partner logos
- `contact_submissions` -- contact form entries
- `sessions`, `cache`, `jobs` -- Laravel infrastructure tables

### 7. Seed the Database

```bash
php artisan db:seed
```

Seeders populate the database with initial content. This project uses seeders for **all site content** -- every service, reference, team member, and page you see on the site comes from seeded data. The seeders run in this order:

1. `AdminUserSeeder` -- creates the admin account (`admin@hilotec.com` / `password`)
2. `SettingsSeeder` -- populates site-wide settings (phone numbers, addresses, etc.)
3. `ServicesSeeder` -- IT service offerings
4. `ReferencesSeeder` -- client project references
5. `PagesSeeder` -- static CMS pages
6. `PostsSeeder` -- blog/news articles

> **Note:** You can combine steps 6 and 7 into one command: `php artisan migrate --seed`

### 8. Configure Admin Access

Open your `.env` file and add this line:

```
ADMIN_EMAILS=admin@hilotec.com
```

**Why is this necessary?** On the `master` branch, the `User` model implements Filament's `FilamentUser` interface. The `canAccessPanel()` method checks whether the logged-in user's email is in the `ADMIN_EMAILS` environment variable. Without this line, even the seeded admin user will be denied access to the `/admin` panel with a 403 Forbidden error.

If you create additional admin users, add their emails as a comma-separated list:

```
ADMIN_EMAILS=admin@hilotec.com,you@hilotec.com
```

### 9. Install Frontend Dependencies

```bash
npm install
```

This reads `package.json` and downloads all JavaScript packages into `node_modules/`. Key dependencies include:

| Package                | Purpose                                            |
|------------------------|----------------------------------------------------|
| `vite` (^7.0)         | Lightning-fast frontend bundler and dev server      |
| `tailwindcss` (^4.0)  | Utility-first CSS framework                        |
| `@tailwindcss/vite`   | Vite plugin that integrates Tailwind CSS 4          |
| `@tailwindcss/typography` | Prose styling for rich-text content             |
| `laravel-vite-plugin` | Bridges Vite with Laravel's Blade templates         |
| `alpinejs` (^3.15)    | Lightweight JS framework for interactive components |
| `concurrently`        | Runs multiple dev processes in one terminal         |

### 10. Start the Development Servers

You need **two processes** running simultaneously: the Laravel backend server and the Vite frontend dev server.

**Option A -- Two terminals (recommended for beginners):**

Terminal 1 -- Laravel backend:

```bash
php artisan serve
```

Terminal 2 -- Vite frontend:

```bash
npm run dev
```

**Option B -- One terminal with `composer dev`:**

```bash
composer dev
```

This runs four processes concurrently using the `concurrently` package (defined in `composer.json`):

| Process              | Color    | What It Does                                   |
|----------------------|----------|-------------------------------------------------|
| `php artisan serve`  | Blue     | Laravel dev server on http://localhost:8000      |
| `php artisan queue:listen` | Purple | Processes queued jobs (emails, etc.)       |
| `php artisan pail`   | Pink     | Real-time log viewer in the terminal            |
| `npm run dev`        | Orange   | Vite dev server with hot module replacement     |

Press `Ctrl+C` to stop all four processes at once.

### 11. Verify Everything Works

Open your browser and check:

| URL                                | What You Should See                          |
|------------------------------------|----------------------------------------------|
| http://localhost:8000              | HILOTEC corporate homepage                   |
| http://localhost:8000/angebot      | Services listing page                        |
| http://localhost:8000/referenzen   | Client references page                       |
| http://localhost:8000/ueber-uns    | About us / team page                         |
| http://localhost:8000/kontakt      | Contact page                                 |
| http://localhost:8000/admin        | Filament admin login                         |

Log into the admin panel with:

- **Email:** `admin@hilotec.com`
- **Password:** `password`

You should see the Filament dashboard with resources for Services, References, Posts, Pages, Team Members, Partners, and Contact Submissions.

---

## Understanding the Dev Workflow

### When Things Auto-Refresh vs. When You Must Restart

Understanding what triggers automatic updates and what requires a manual restart saves a lot of confusion:

| Change You Made                         | What to Do                                   |
|-----------------------------------------|----------------------------------------------|
| Edited a `.blade.php` template          | Vite auto-refreshes the browser instantly     |
| Edited `resources/css/app.css`          | Vite auto-refreshes (HMR)                    |
| Edited `resources/js/app.js`            | Vite auto-refreshes (HMR)                    |
| Edited a Controller                     | Next browser request picks it up automatically |
| Edited a Model                          | Next request picks it up automatically        |
| Edited `routes/web.php`                 | Next request picks it up automatically        |
| Edited `.env`                           | **Restart `artisan serve`** + run `php artisan config:clear` |
| Edited `config/*.php`                   | Run `php artisan config:clear`               |
| Added a new Composer package            | Run `composer install`, then restart server   |
| Added a new npm package                 | Run `npm install`, then restart `npm run dev` |
| Changed a migration file                | Run `php artisan migrate:fresh --seed`        |
| Changed a seeder file                   | Run `php artisan db:seed` (or `migrate:fresh --seed`) |
| Added/modified a Filament Resource      | Next request picks it up automatically        |

> **Why does `.env` require a restart?** Laravel reads environment variables once at boot time and caches them. Changing `.env` while the server is running has no effect until you restart it.

### `npm run dev` vs. `npm run build`

- **`npm run dev`** starts Vite's development server. It serves your CSS and JS with hot module replacement (HMR), meaning changes appear in the browser without a full page reload. The assets are **not** written to disk -- they are served from memory. This is for development only.

- **`npm run build`** compiles, minifies, and writes the final CSS and JS files to `public/build/`. These are the files that get deployed to production. Run this before deploying, or when you want to test the production build locally.

> **Important:** If `npm run dev` is not running and you have not run `npm run build`, your pages will load without any styling or JavaScript. You will see a Vite manifest error. Either start the dev server or run a build.

### Clearing Caches

Laravel caches various things for performance. During development, stale caches can cause confusing behavior. Here are the cache-clearing commands:

```bash
# Clear all caches at once (the nuclear option)
php artisan optimize:clear

# Or clear individually:
php artisan config:clear    # Cached config from .env and config/*.php
php artisan view:clear      # Compiled Blade templates
php artisan cache:clear     # Application cache (database/file)
php artisan route:clear     # Cached route definitions
php artisan event:clear     # Cached event/listener mappings
```

> **When in doubt, run `php artisan optimize:clear`.** It clears everything and takes less than a second.

---

## Working with Branches

This project has two main branches:

| Branch       | Theme                  | Description                                      |
|--------------|------------------------|--------------------------------------------------|
| `master`     | Dark theme             | Production branch with security hardening (ADMIN_EMAILS) |
| `design-v2`  | Light/dark hybrid      | Redesign branch with updated visual language     |

### Switching Branches

```bash
# Save any uncommitted work first
git stash

# Switch to the other branch
git checkout design-v2    # or: git checkout master

# Install dependencies (both branches may differ)
composer install
npm install

# Rebuild the database (schema/seeders may differ between branches)
php artisan migrate:fresh --seed

# Build frontend assets for the new branch
npm run dev
```

### Why `migrate:fresh --seed`?

Both branches share the same `database/database.sqlite` file. If you simply switch branches without resetting the database, you may encounter:

- Missing columns that a migration on the other branch expects
- Seeded data that references columns or tables that do not exist on the current branch
- Foreign key constraint errors

`migrate:fresh --seed` drops all tables, re-runs every migration from scratch, and re-seeds all content. This gives you a clean slate matched to the branch you are on.

> **Warning:** `migrate:fresh` destroys all data in the database. If you have created test content through the admin panel that you want to keep, back up the SQLite file first:
>
> ```bash
> cp database/database.sqlite database/database.sqlite.backup
> ```

### Branch-Specific Environment Notes

On the **master** branch, remember to set `ADMIN_EMAILS=admin@hilotec.com` in your `.env` file. The `design-v2` branch may not require this depending on its admin access implementation. Check the `User` model's `canAccessPanel()` method if you are unsure.

---

## Essential Artisan Commands

Laravel's `artisan` CLI is your primary tool for interacting with the application. Here is a quick reference:

### Server & Development

| Command                         | Description                                              |
|---------------------------------|----------------------------------------------------------|
| `php artisan serve`             | Start the local dev server at http://localhost:8000       |
| `composer dev`                  | Start server + queue + logs + Vite all at once            |
| `php artisan tinker`            | Interactive PHP REPL with your app loaded (great for testing queries) |

### Database

| Command                         | Description                                              |
|---------------------------------|----------------------------------------------------------|
| `php artisan migrate`           | Run all pending migrations                               |
| `php artisan migrate:status`    | Show which migrations have run and which are pending      |
| `php artisan migrate:rollback`  | Undo the last batch of migrations                        |
| `php artisan migrate:fresh`     | Drop all tables and re-run all migrations from scratch    |
| `php artisan migrate:fresh --seed` | Drop, re-migrate, and re-seed (full database reset)   |
| `php artisan db:seed`           | Run all seeders                                          |
| `php artisan db:seed --class=ServicesSeeder` | Run a specific seeder only                  |

### Cache Management

| Command                         | Description                                              |
|---------------------------------|----------------------------------------------------------|
| `php artisan optimize:clear`    | Clear ALL caches at once                                 |
| `php artisan config:clear`      | Clear cached configuration                               |
| `php artisan view:clear`        | Clear compiled Blade views                               |
| `php artisan cache:clear`       | Clear application cache                                  |
| `php artisan route:clear`       | Clear cached routes                                      |

### Code Quality & Testing

| Command                         | Description                                              |
|---------------------------------|----------------------------------------------------------|
| `php artisan test`              | Run the PHPUnit test suite                               |
| `composer test`                 | Clear config cache, then run tests                       |
| `./vendor/bin/pint`             | Run Laravel Pint (code style fixer, PSR-12)              |

### Filament

| Command                         | Description                                              |
|---------------------------------|----------------------------------------------------------|
| `php artisan make:filament-resource ModelName` | Generate a new Filament admin resource      |
| `php artisan filament:upgrade`  | Sync Filament assets after an update                     |

### Utility

| Command                         | Description                                              |
|---------------------------------|----------------------------------------------------------|
| `php artisan route:list`        | Show all registered routes                               |
| `php artisan make:model Name -mfs` | Create a model with migration, factory, and seeder    |
| `php artisan make:controller NameController` | Create a new controller                      |
| `php artisan queue:listen`      | Process queued jobs (runs continuously)                  |
| `php artisan pail`              | Real-time log viewer in terminal                         |

---

## IDE Setup Recommendations

### VS Code Extensions

| Extension                        | Publisher         | Why You Need It                                    |
|----------------------------------|-------------------|----------------------------------------------------|
| **Laravel Blade Snippets**       | Winnie Lin        | Syntax highlighting and snippets for `.blade.php`  |
| **Laravel Blade Formatter**      | Shufo             | Auto-format Blade templates on save                |
| **PHP Intelephense**             | Ben Mewburn       | PHP autocompletion, go-to-definition, diagnostics  |
| **Tailwind CSS IntelliSense**    | Tailwind Labs     | Autocomplete for Tailwind classes, hover previews  |
| **Alpine.js IntelliSense**       | Adrian Wilczynski | Autocomplete for Alpine.js directives              |
| **SQLite Viewer**                | Florian Klampfer  | Browse `database.sqlite` without leaving VS Code   |
| **EditorConfig for VS Code**     | EditorConfig      | Enforces consistent coding style from `.editorconfig` |
| **DotENV**                       | mikestead         | Syntax highlighting for `.env` files               |

### Recommended VS Code Settings

Add to your workspace `.vscode/settings.json`:

```json
{
    "emmet.includeLanguages": {
        "blade": "html"
    },
    "files.associations": {
        "*.blade.php": "blade"
    },
    "tailwindCSS.includeLanguages": {
        "blade": "html"
    },
    "[php]": {
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client"
    },
    "[blade]": {
        "editor.defaultFormatter": "shufo.vscode-blade-formatter"
    }
}
```

### PhpStorm / JetBrains

If you use PhpStorm, install the **Laravel Idea** plugin (paid, but excellent) and the **Tailwind CSS** plugin. PhpStorm has built-in support for Blade templates, Alpine.js, and database browsing.

---

## Common Dev Pitfalls

| Problem | Cause | Solution |
|---------|-------|----------|
| **Page loads with no CSS/JS** | Vite dev server is not running and no production build exists | Run `npm run dev` to start Vite, or `npm run build` to create a static build |
| **"Vite manifest not found" error** | Same as above -- Laravel cannot find the Vite manifest file at `public/build/manifest.json` | Run `npm run dev` (dev) or `npm run build` (production build) |
| **403 Forbidden on `/admin`** | `ADMIN_EMAILS` is not set in `.env`, or does not include your user's email | Add `ADMIN_EMAILS=admin@hilotec.com` to `.env` and restart the server |
| **"No application encryption key"** | You skipped `php artisan key:generate` | Run `php artisan key:generate` |
| **"Database does not exist" or SQLite errors** | The `database/database.sqlite` file was not created | Run `touch database/database.sqlite` then `php artisan migrate --seed` |
| **"Table not found" after branch switch** | Database schema from the previous branch does not match the current branch's expectations | Run `php artisan migrate:fresh --seed` to rebuild the database from scratch |
| **Changes to `.env` not taking effect** | Laravel caches config at boot; `.env` changes are not picked up by a running server | Restart `php artisan serve` and run `php artisan config:clear` |
| **"Class not found" after `composer install`** | Composer autoload map is stale | Run `composer dump-autoload` |
| **Filament assets look broken after update** | Filament publishes its own frontend assets that can get out of sync | Run `php artisan filament:upgrade` |
| **Port 8000 already in use** | Another process (or a previous `artisan serve`) is already using port 8000 | Kill the old process (`lsof -ti:8000 \| xargs kill`) or use a different port: `php artisan serve --port=8080` |
| **npm install fails with Node version error** | Node.js version is too old for Tailwind CSS 4 or Vite 7 | Upgrade to Node 18+: `nvm install 18 && nvm use 18` |
| **Stale Blade views rendering old content** | Compiled Blade views are cached in `storage/framework/views/` | Run `php artisan view:clear` |

---

## Quick Reference: Complete Setup in Under 2 Minutes

For experienced developers who just want the commands:

```bash
git clone <repository-url>
cd hilotec
cp .env.example .env
echo "ADMIN_EMAILS=admin@hilotec.com" >> .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
composer dev
```

Then open http://localhost:8000 (site) and http://localhost:8000/admin (admin panel, login: `admin@hilotec.com` / `password`).
