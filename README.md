# HILOTEC Engineering + Consulting AG — Website

Corporate website for [HILOTEC Engineering + Consulting AG](https://www.hilotec.com), a Swiss IT services company based in Langnau im Emmental. Built with Laravel 11, Filament 3, Tailwind CSS 4, and Alpine.js.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- SQLite (included with PHP by default)

## Setup

```bash
git clone <repo-url> hilotec
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

Visit http://localhost:8000 to see the website.

## Admin Panel

URL: http://localhost:8000/admin

Default credentials:
- Email: `admin@hilotec.com`
- Password: `password`

The admin panel (Filament 3) allows managing:
- Services (IT offerings)
- Reference categories and references (client list)
- Team members
- Blog posts
- Static pages (Impressum, Datenschutz, etc.)
- Site-wide settings (contact info, footer, social links)
- Contact form submissions

## Architecture

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 |
| Admin CMS | Filament 3 |
| Frontend | Blade templates |
| Styling | Tailwind CSS 4 |
| Interactivity | Alpine.js |
| Database | SQLite (local) |
| Fonts | Google Fonts (Sora + DM Sans) |
| Build | Vite |

### Key Directories

```
app/
├── Models/              Eloquent models
├── Http/Controllers/    Frontend controllers
├── Filament/
│   ├── Resources/       CRUD admin resources
│   └── Pages/           Custom admin pages (Settings)
├── helpers.php          Global setting() helper

resources/views/
├── components/          Blade components (design system)
├── pages/               Page templates
├── errors/              Error pages (404)
└── filament/            Filament custom views

database/
├── migrations/          Schema definitions
└── seeders/             Content data

public/images/           Static assets
├── heroes/              Hero background images
├── backgrounds/         Section backgrounds
├── icons/               Service SVG icons
├── branding/            Logo, swoosh, TeamViewer badge
└── meta/                Favicons, OG image
```

## Development

```bash
# Start dev server with hot reload
npm run dev &
php artisan serve

# Or use the built-in composer dev script
composer dev
```

## Pages

| Route | Page |
|-------|------|
| `/` | Home |
| `/angebot` | Services overview |
| `/angebot/{slug}` | Service detail |
| `/referenzen` | References (client list) |
| `/ueber-uns` | About us |
| `/aktuelles` | Blog listing |
| `/aktuelles/{slug}` | Blog post |
| `/kontakt` | Contact form |
| `/impressum` | Legal notice |
| `/datenschutz` | Privacy policy |
| `/admin` | Admin panel |

## License

Proprietary — HILOTEC Engineering + Consulting AG
