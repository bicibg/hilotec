# HILOTEC Engineering + Consulting AG — Website

Corporate website for [HILOTEC Engineering + Consulting AG](https://www.hilotec.com), a Swiss IT services company for SMEs (KMU), based in Langnau im Emmental.

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel | 12 |
| Admin CMS | Filament | 3 |
| Styling | Tailwind CSS | 4 |
| Interactivity | Alpine.js | 3 |
| Database | SQLite (local) / MySQL (production) | — |
| Fonts | Google Fonts (Sora + DM Sans) | — |
| Build Tool | Vite | 7 |

## Requirements

- PHP 8.2+ with extensions: `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer 2.x
- Node.js 18+ & npm
- SQLite 3 (included with PHP by default)

## Quick Start

```bash
git clone https://github.com/bicibg/hilotec.git
cd hilotec
cp .env.example .env
touch database/database.sqlite
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Open http://localhost:8000 in your browser.

## Admin Panel

**URL:** http://localhost:8000/admin

| | |
|---|---|
| Email | `admin@hilotec.com` |
| Password | `password` |

See [docs/03-ADMIN-GUIDE.md](docs/03-ADMIN-GUIDE.md) for the full admin manual.

## Documentation

Complete handover documentation is in the [`docs/`](docs/) directory. See [docs/README.md](docs/README.md) for the full index.

| Document | Audience | Description |
|----------|----------|-------------|
| [00-GETTING-STARTED.md](docs/00-GETTING-STARTED.md) | Everyone | Quick start in 10 steps |
| [01-DEVELOPMENT-SETUP.md](docs/01-DEVELOPMENT-SETUP.md) | Developers | IDE, tooling, dev workflow |
| [02-DEPLOYMENT.md](docs/02-DEPLOYMENT.md) | DevOps | Production server setup, SSL, deploy scripts |
| [03-ADMIN-GUIDE.md](docs/03-ADMIN-GUIDE.md) | Content Editors | Admin panel usage for all content types |
| [04-TECHNICAL.md](docs/04-TECHNICAL.md) | Developers | Architecture, models, controllers, patterns |
| [05-DESIGN-SYSTEM.md](docs/05-DESIGN-SYSTEM.md) | Developers | Colors, fonts, Blade components |
| [06-SEO.md](docs/06-SEO.md) | Marketing / DevOps | SEO setup, structured data, analytics |
| [07-MAINTENANCE.md](docs/07-MAINTENANCE.md) | DevOps | Routine maintenance, updates, monitoring |
| [08-BACKUP-RECOVERY.md](docs/08-BACKUP-RECOVERY.md) | DevOps | Backup strategies, disaster recovery |
| [09-SECURITY.md](docs/09-SECURITY.md) | DevOps / Developers | Security architecture, hardening, incident response |
| [10-BRANCH-COMPARISON.md](docs/10-BRANCH-COMPARISON.md) | Developers | master vs design-v2 branch differences |

## Development

```bash
# Hot-reload development (two terminals)
npm run dev          # Terminal 1: Vite dev server
php artisan serve    # Terminal 2: Laravel server

# Or single command with concurrently
composer dev
```

### Common Commands

```bash
php artisan migrate --seed    # Reset database with fresh content
php artisan cache:clear       # Clear all caches (including settings)
npm run build                 # Production asset build
```

## Project Structure

```
hilotec/
├── app/
│   ├── Console/Commands/     # Artisan commands (SecurityAudit)
│   ├── Filament/             # Admin panel (resources, pages)
│   ├── Http/
│   │   ├── Controllers/      # Frontend controllers
│   │   └── Middleware/        # SecurityHeaders, ThrottleAdminLogin
│   ├── Models/               # Eloquent models
│   └── helpers.php           # Global setting() helper
├── database/
│   ├── migrations/           # Schema definitions
│   └── seeders/              # Content data
├── docs/                     # Handover documentation (11 guides)
├── public/images/            # Static assets
├── resources/
│   ├── css/app.css           # Tailwind config + design tokens
│   ├── js/app.js             # Alpine.js setup
│   └── views/
│       ├── components/       # Reusable Blade components
│       ├── pages/            # Page templates
│       └── errors/           # Error pages
└── routes/web.php            # All public routes
```

## Pages

| Route | Page | Controller |
|-------|------|------------|
| `/` | Home | `HomeController` |
| `/angebot` | Services listing | `ServiceController@index` |
| `/angebot/{slug}` | Service detail | `ServiceController@show` |
| `/referenzen` | References by category | `ReferenceController@index` |
| `/ueber-uns` | About us | `AboutController@index` |
| `/aktuelles` | Blog listing | `PostController@index` |
| `/aktuelles/{slug}` | Blog post detail | `PostController@show` |
| `/kontakt` | Contact form | `ContactController` |
| `/impressum` | Legal notice | `PageController@show` |
| `/datenschutz` | Privacy policy | `PageController@show` |
| `/admin` | Admin panel | Filament |

## License

Proprietary — HILOTEC Engineering + Consulting AG
