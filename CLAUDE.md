# Critical Rules

### NEVER Sign Commits
Do NOT add "Generated with Claude Code" or "Co-Authored-By: Claude" to commits!

# HILOTEC Website — Developer Guide

## Project Overview
Corporate website for HILOTEC Engineering + Consulting AG, a Swiss IT services company.
Laravel 11 + Filament 3 + Tailwind CSS 4 + Alpine.js. SQLite for local dev.

## Quick Start
```bash
cd /home/bugra/Projects/hilotec
cp .env.example .env
touch database/database.sqlite
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Admin panel: http://localhost:8000/admin (admin@hilotec.com / password)

## Architecture

### Stack
- Backend: Laravel 11, Filament 3 (admin CMS)
- Frontend: Blade templates + Tailwind CSS 4 + Alpine.js
- Database: SQLite (local), easily switchable to MySQL/PostgreSQL
- Fonts: Google Fonts — Sora (headings), DM Sans (body)

### Directory Conventions
- app/Models/ — Eloquent models, each with scopePublished() where applicable
- app/Filament/Resources/ — Filament admin resources
- app/Filament/Pages/ — Custom Filament pages (e.g., Settings)
- app/Http/Controllers/ — Frontend controllers (thin, delegate to models)
- app/View/Components/ — Blade component classes
- resources/views/components/ — Reusable Blade components (design system)
- resources/views/pages/ — Page-specific templates
- resources/views/partials/ — Shared partial templates
- database/seeders/ — Content seeders (all site content is seeded)
- public/images/ — Static images organized in subdirectories

### Design System (Tailwind CSS 4)
The site uses a consistent design system defined in `resources/css/app.css` via `@theme`:
- Colors: hilotec-dark, hilotec-gold, hilotec-blue, etc.
- Fonts: font-heading (Sora), font-body (DM Sans)
- Reusable Blade components for: buttons, cards, section headings, hero sections, icons

### Settings Helper
Use `setting('group.key')` to access site-wide settings from any Blade template.
Example: `setting('contact.phone_support_infra')` returns "+41 34 408 01 00"

### Key Patterns
- All frontend content comes from the database (seeded via DatabaseSeeder)
- Filament admin at /admin manages all content
- Published scope: `Model::published()->get()` filters by `is_published = true`
- Routes: public pages use descriptive German slugs (/angebot, /referenzen, /ueber-uns)
