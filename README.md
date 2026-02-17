# HILOTEC Engineering + Consulting AG — Website

Corporate website for [HILOTEC Engineering + Consulting AG](https://www.hilotec.com), a Swiss IT services company for SMEs (KMU), based in Langnau im Emmental.

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | Laravel | 11 |
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

See [docs/ADMIN_GUIDE.md](docs/ADMIN_GUIDE.md) for the full client-facing admin manual.

## Documentation

| Document | Audience | Description |
|----------|----------|-------------|
| [README.md](README.md) | Developers | This file — project overview and quick start |
| [docs/TECHNICAL.md](docs/TECHNICAL.md) | Developers | Architecture, database schema, code patterns |
| [docs/DESIGN_SYSTEM.md](docs/DESIGN_SYSTEM.md) | Developers | Colors, fonts, Blade components, usage examples |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | DevOps / Developers | Production deployment, server config, environment |
| [docs/ADMIN_GUIDE.md](docs/ADMIN_GUIDE.md) | Client / Content Editors | How to use the admin panel to manage content |
| [CLAUDE.md](CLAUDE.md) | AI Assistants | Context for Claude Code / AI-assisted development |
| [DECISIONS.md](DECISIONS.md) | Developers | Log of architectural decisions with rationale |
| [PROGRESS.md](PROGRESS.md) | Developers | Build progress tracker |

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
│   ├── Filament/             # Admin panel (resources, pages)
│   ├── Http/Controllers/     # Frontend controllers
│   ├── Models/               # Eloquent models
│   └── helpers.php           # Global setting() helper
├── database/
│   ├── migrations/           # Schema definitions
│   └── seeders/              # Content data
├── docs/                     # Documentation
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
