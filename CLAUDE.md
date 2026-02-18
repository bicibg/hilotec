# Critical Rules

### NEVER Sign Commits
Do NOT add "Generated with Claude Code" or "Co-Authored-By: Claude" to commits!

# HILOTEC Website — Developer Guide

## Project Overview
Corporate website for HILOTEC Engineering + Consulting AG, a Swiss IT services company.
Laravel 12 + Filament 4 + Tailwind CSS 4 + Alpine.js 3. SQLite for local dev.

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
- Backend: Laravel 12 (`laravel/framework: ^12.0`), PHP ^8.3
- Admin CMS: Filament 4
- Frontend: Blade templates + Tailwind CSS 4 + Alpine.js 3
- Build: Vite 7
- Database: SQLite (local), MySQL/PostgreSQL (production)
- Fonts: Google Fonts — Sora (headings), DM Sans (body)

### Models (10)
User, Setting, Service, ReferenceCategory, Reference, TeamMember, Post, Page, Partner, ContactSubmission

Content models use `scopePublished()` and `scopeOrdered()` scopes.

### Controllers (8)
HomeController, ServiceController, ReferenceController, AboutController, PostController, ContactController, PageController, Controller (base)

Controllers are thin — delegate to model scopes for queries.

### Routes
German slugs: `/` (home), `/angebot` (services), `/referenzen` (references), `/ueber-uns` (about), `/aktuelles` (blog), `/kontakt` (contact), `/{slug}` (catch-all pages), `/admin` (Filament)

### Directory Conventions
- app/Models/ — Eloquent models with scopePublished() where applicable
- app/Filament/Resources/ — 8 Filament admin resources
- app/Filament/Pages/ — Custom Filament pages (ManageSettings with 4 tabs)
- app/Http/Controllers/ — Frontend controllers
- app/Http/Middleware/ — SecurityHeaders, ThrottleAdminLogin
- app/Console/Commands/ — SecurityAudit artisan command
- resources/views/components/ — Reusable Blade components (design system)
- resources/views/pages/ — Page-specific templates
- resources/views/partials/ — Shared partial templates
- database/seeders/ — Content seeders (all site content is seeded)
- public/images/ — Static images organized in subdirectories

### Design System (Tailwind CSS 4)
Defined in `resources/css/app.css` via `@theme`:
- Colors: hilotec-dark, hilotec-gold, hilotec-blue, etc.
- Fonts: font-heading (Sora), font-body (DM Sans)
- Reusable Blade components: buttons, cards, section headings, hero sections, icons

### Settings Helper
Use `setting('group.key')` to access site-wide settings (cached 60 min).
Example: `setting('contact.phone_support_infra')` returns "+41 34 408 01 00"

### Security
- SecurityHeaders middleware: CSP, HSTS, X-Frame-Options, Permissions-Policy, COOP, CORP
- ThrottleAdminLogin middleware: 5 POST attempts/min/IP on /admin/login
- SecurityAudit command: `php artisan security:audit` (scheduled every 6 hours)
- FilamentUser interface: canAccessPanel() gated by ADMIN_EMAILS env var
- Session: encrypted, secure cookies, SameSite=strict
- Hardened .htaccess with security headers

### Key Patterns
- All frontend content comes from the database (seeded via DatabaseSeeder)
- Filament admin at /admin manages all content
- Published scope: `Model::published()->get()` filters by `is_published = true`
- Routes: public pages use descriptive German slugs

### Branches
- `master`: Dark theme + full security hardening
- `design-v2`: Light/dark hybrid with scroll animations (NO security hardening yet)
- See docs/10-BRANCH-COMPARISON.md for detailed diff and merge guide

## Documentation
Full handover docs in `docs/` (00-GETTING-STARTED through 10-BRANCH-COMPARISON).
See docs/README.md for the complete index.
