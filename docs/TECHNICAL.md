# Technical Architecture

## Overview

The HILOTEC website is a server-rendered Laravel application with a Filament-powered admin CMS. All page content is stored in a database and managed through the admin panel. The frontend uses Blade templates with Tailwind CSS for styling and Alpine.js for client-side interactivity.

## Stack Details

### Backend — Laravel 11
- Standard MVC architecture with thin controllers
- Eloquent ORM with published scopes and ordered scopes
- Global `setting()` helper for site-wide key-value configuration
- CSRF protection on all forms
- Server-side validation on contact form submissions

### Admin — Filament 3
- Located at `/admin`, protected by Laravel's built-in auth
- 8 CRUD resources for managing all content types
- Custom `ManageSettings` page with tabbed form UI
- File uploads for team photos and post images (stored in `storage/app/public`)

### Frontend — Blade + Tailwind CSS 4 + Alpine.js
- Reusable Blade component library (see [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md))
- Tailwind CSS 4 with custom design tokens defined via `@theme` in `resources/css/app.css`
- Alpine.js for: sticky header scroll detection, mobile menu toggle, reference category filters
- Google Fonts loaded via CDN `<link>` tags

### Build — Vite
- `resources/css/app.css` → compiled CSS with Tailwind
- `resources/js/app.js` → compiled JS with Alpine.js
- Output to `public/build/` with content-hashed filenames
- `@vite()` directive in layout template handles asset loading

## Database Schema

### Entity Relationship Diagram

```
users
  id, name, email, password, ...

settings
  id, group, key, value
  UNIQUE(group, key)

services
  id, title, slug (unique), icon, excerpt, body, sort_order, is_published

reference_categories
  id, name, slug (unique), sort_order

references
  id, reference_category_id → reference_categories.id (CASCADE)
  company_name, address, description, website, sort_order, is_published

team_members
  id, name, role, email, phone, photo, bio, sort_order, is_published

posts
  id, title, slug (unique), excerpt, body, featured_image
  is_published, published_at

pages
  id, title, slug (unique), hero_heading, hero_subheading, hero_image
  body, meta_title, meta_description, is_published

partners
  id, name, logo, website, description, sort_order, is_published

contact_submissions
  id, name, email, phone, message, is_read
```

### Key Relationships
- `ReferenceCategory` → has many `Reference` (cascade delete)
- `Reference` → belongs to `ReferenceCategory`

### Common Scopes
Most models with `is_published` have:
- `scopePublished($query)` — filters to `is_published = true`
- `scopeOrdered($query)` — orders by `sort_order ASC`

The `Post` model additionally:
- `scopePublished()` also checks `published_at <= now()`
- `scopeLatest()` orders by `published_at DESC`

## Settings System

### How It Works
Settings are stored as key-value pairs with a `group` column for organization.

```
┌──────────┬──────────────────┬────────────────┐
│ group    │ key              │ value          │
├──────────┼──────────────────┼────────────────┤
│ general  │ company_name     │ HILOTEC ...    │
│ contact  │ phone_support... │ +41 34 ...     │
│ footer   │ cta_heading      │ Sie haben ...  │
│ social   │ linkedin         │ https://...    │
└──────────┴──────────────────┴────────────────┘
```

### Usage in Code

```php
// In PHP (controllers, models, etc.)
$name = Setting::get('general.company_name');
Setting::set('contact.email', 'new@hilotec.com');

// In Blade templates (via global helper)
{{ setting('contact.phone_support_infra') }}
```

### Caching
All settings are loaded once and cached for 60 minutes (`Cache::remember`). The cache is automatically cleared when any setting is updated via `Setting::set()` or the admin panel.

### Settings Groups

| Group | Keys | Purpose |
|-------|------|---------|
| `general` | company_name, company_slogan, company_subtitle, founded_year, about_short | Company identity |
| `contact` | address_line1, address_zip_city, address_country, phone_support_infra, phone_label_infra, phone_support_software, phone_label_software, email, website, business_hours | Contact details |
| `footer` | cta_heading, cta_button_text, cta_button_url, copyright_text, teamviewer_text, teamviewer_url | Footer content |
| `social` | linkedin, github | Social media links |

## Routing

### Route Registration Order
Routes are defined in `routes/web.php`. The catch-all `/{slug}` route for generic pages (Impressum, Datenschutz) **must be last** to avoid intercepting named routes.

```
GET  /                  → HomeController@index
GET  /angebot           → ServiceController@index
GET  /angebot/{slug}    → ServiceController@show
GET  /referenzen        → ReferenceController@index
GET  /ueber-uns         → AboutController@index
GET  /aktuelles         → PostController@index
GET  /aktuelles/{slug}  → PostController@show
GET  /kontakt           → ContactController@index
POST /kontakt           → ContactController@send
GET  /{slug}            → PageController@show          ← catch-all, must be last
```

### Named Routes
All routes have names (`home`, `services.index`, `services.show`, etc.) for use with `route()` helper.

## Filament Admin Panel

### Resources

| Resource | Model | Navigation Group | Features |
|----------|-------|-----------------|----------|
| ServiceResource | Service | Inhalte | Rich editor, drag-reorder |
| PostResource | Post | Inhalte | Rich editor, image upload, datetime picker |
| PageResource | Page | Inhalte | Rich editor, hero config |
| TeamMemberResource | TeamMember | Inhalte | Image upload, drag-reorder |
| PartnerResource | Partner | Inhalte | Image upload, drag-reorder |
| ReferenceCategoryResource | ReferenceCategory | Referenzen | Drag-reorder, reference count |
| ReferenceResource | Reference | Referenzen | Category select, published toggle |
| ContactSubmissionResource | ContactSubmission | Kontakt | View-only, read status |

### Custom Pages

| Page | Path | Description |
|------|------|-------------|
| ManageSettings | `/admin/manage-settings` | Tabbed settings form (Allgemein, Kontakt, Footer, Social Media) |

### Admin Authentication
Uses Laravel's built-in `users` table. Any user in the `users` table can log into the admin panel. The default seeded admin is `admin@hilotec.com` / `password`.

## Contact Form

### Flow
1. User submits form at `/kontakt` (POST with CSRF token)
2. `ContactController@send` validates: name (required), email (required, email), phone (optional), message (required, max 5000 chars)
3. Valid submissions are saved to `contact_submissions` table
4. User sees a success flash message
5. Admin can view submissions in the admin panel under "Anfragen"

### Security
- CSRF protection via `@csrf` token
- Server-side validation with Laravel's `$request->validate()`
- XSS protection: user input is escaped by Blade's `{{ }}` syntax
- No raw SQL: all queries go through Eloquent ORM

## Static Assets

```
public/images/
├── heroes/
│   ├── home_hero_bg.jpg          # 1920×1080 — Home full-screen hero
│   ├── ueber_uns_hero_bg.jpg     # 1920×1080 — Über uns full-screen hero
│   └── inner_page_hero_bg.jpg    # 1920×400  — Inner pages compact hero
├── backgrounds/
│   ├── footer_cta_bg.jpg         # 1920×500  — CTA section matrix background
│   └── circuit_pattern_tile.png  # 400×400   — Tileable circuit pattern
├── icons/                        # 64×64 SVGs with #d4a843 gold stroke
│   ├── server.svg, security.svg, cloud.svg, backup.svg
│   ├── software.svg, phone.svg, virtualization.svg, consulting.svg
├── branding/
│   ├── logo.png                  # HILOTEC company logo
│   ├── logo_swoosh.svg           # Gold swoosh decoration
│   └── teamviewer_badge.png      # TeamViewer remote support badge
└── meta/
    ├── favicon.ico, favicon-32x32.png, favicon-512x512.png
    ├── apple-touch-icon.png
    └── og_image.jpg              # 1200×630 Open Graph image
```
