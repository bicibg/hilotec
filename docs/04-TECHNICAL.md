# Technical Architecture & Learning Guide

> **Audience:** IT infrastructure professionals (sysadmins, network engineers, IT consultants) who may not have prior Laravel experience. This guide explains every architectural decision and codebase pattern in plain terms.
>
> **Related docs:** [Getting Started](00-GETTING-STARTED.md) | [Development Setup](01-DEVELOPMENT-SETUP.md) | [Deployment](02-DEPLOYMENT.md) | [Admin Guide](03-ADMIN-GUIDE.md)

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Technology Stack](#2-technology-stack)
3. [Directory Structure](#3-directory-structure)
4. [Database Schema](#4-database-schema)
5. [Models](#5-models)
6. [Controllers](#6-controllers)
7. [Routing](#7-routing)
8. [Filament Admin](#8-filament-admin)
9. [Blade Components](#9-blade-components)
10. [Frontend Architecture](#10-frontend-architecture)
11. [Settings System](#11-settings-system)
12. [Security Architecture](#12-security-architecture)
13. [Key Patterns](#13-key-patterns)
14. [File Upload & Storage](#14-file-upload--storage)
15. [How to Add New Content Types](#15-how-to-add-new-content-types)

---

## 1. Architecture Overview

### What is MVC?

Laravel uses the **Model-View-Controller (MVC)** pattern. If you are familiar with networking, think of it as a three-tier architecture:

- **Model** = the data layer (like a database schema + queries). Models represent tables and contain the business logic for reading/writing data.
- **View** = the presentation layer (HTML templates). In Laravel these are called "Blade templates" -- files that mix HTML with simple PHP directives.
- **Controller** = the application logic layer (like an API handler). Controllers receive HTTP requests, call models to fetch data, and pass that data to views.

### Request Lifecycle Diagram

```
Browser Request
     |
     v
+------------------+
|   Web Server     |  (Apache/Nginx or PHP built-in server)
|   (public/)      |
+------------------+
     |
     v
+------------------+
|  bootstrap/      |  Laravel bootstraps the application:
|  app.php         |  loads config, registers middleware
+------------------+
     |
     v
+------------------+
|   Middleware      |  Request passes through middleware stack:
|   Pipeline       |  - SecurityHeaders (master only; not on design-v2)
|                  |  - CSRF verification
|                  |  - Session handling
+------------------+
     |
     v
+------------------+
|  routes/web.php  |  Router matches URL to a controller method.
|                  |  Example: GET /angebot -> ServiceController@index
+------------------+
     |
     v
+------------------+
|   Controller     |  Controller calls the Model to fetch data.
|                  |  Example: Service::published()->ordered()->get()
+------------------+
     |
     v
+------------------+
|     Model        |  Model queries the database (SQLite/MySQL/PostgreSQL)
|  (Eloquent ORM)  |  and returns PHP objects.
+------------------+
     |
     v
+------------------+
|     View         |  Controller passes data to a Blade template.
|  (Blade template)|  Template renders HTML with that data.
+------------------+
     |
     v
+------------------+
|   Middleware      |  Response passes back through middleware:
|   (outbound)     |  - SecurityHeaders adds HTTP headers (master only)
+------------------+
     |
     v
Browser receives HTML + CSS + JS
```

### Admin Panel Lifecycle

The admin panel at `/admin` uses **Filament 3**, which adds its own middleware stack and renders its own UI. Filament uses **Livewire** under the hood (a framework for building reactive server-rendered components). You do not need to understand Livewire to use or extend the admin panel -- Filament abstracts it away behind PHP class configuration.

```
Browser: /admin/services
     |
     v
  Filament Middleware Stack
  (Authentication, CSRF, Session, Rate Limiting)
     |
     v
  AdminPanelProvider -> discovers ServiceResource class
     |
     v
  ServiceResource defines:
    - form() -> what fields the edit form shows
    - table() -> what columns the list view shows
    - getPages() -> which pages exist (list, create, edit)
     |
     v
  Filament renders its own Blade/Livewire UI
     |
     v
  Browser receives admin panel HTML
```

---

## 2. Technology Stack

| Technology | Version | What It Does |
|---|---|---|
| **PHP** | ^8.2 | Server-side programming language. Runs all backend code. |
| **Laravel** | ^12.0 | PHP web framework. Provides routing, database ORM, templating, security, caching, and hundreds of other features out of the box. Think of it as the "operating system" for the web app. |
| **Filament** | ^3.0 | Admin panel framework built on top of Laravel. Generates a full CRUD (Create/Read/Update/Delete) interface for each content type from a single PHP class. |
| **Tailwind CSS** | ^4.0.0 | Utility-first CSS framework. Instead of writing custom CSS, you add classes directly to HTML elements (e.g., `text-white bg-blue-500 p-4`). Version 4 uses a new `@theme` directive for design tokens. |
| **Alpine.js** | ^3.15.8 | Lightweight JavaScript framework for client-side interactivity. Used for the mobile menu toggle, sticky header scroll detection, and reference category filters. Think of it as "jQuery replacement" but declarative. |
| **Vite** | ^7.0.7 | Frontend build tool. Compiles Tailwind CSS and bundles JavaScript into optimized, versioned files for production. In development, it provides instant hot-reload. |
| **SQLite** | (system) | File-based database used for local development. The entire database is a single file at `database/database.sqlite`. No server process needed. |
| **MySQL/PostgreSQL** | (production) | Full-featured relational database for production use. Switchable via the `DB_CONNECTION` environment variable. |
| **Composer** | (system) | PHP dependency manager. Reads `composer.json` and installs PHP packages into the `vendor/` directory. Analogous to `apt` or `yum` for PHP libraries. |
| **npm** | (system) | Node.js dependency manager. Reads `package.json` and installs JavaScript packages into `node_modules/`. Used only at build time -- no Node.js is needed in production. |
| **Laravel Pint** | ^1.24 (dev) | PHP code style fixer. Automatically formats PHP code to follow Laravel's coding conventions. |
| **PHPUnit** | ^11.5.3 (dev) | PHP testing framework. Run tests with `php artisan test`. |
| **@tailwindcss/typography** | ^0.5.19 | Tailwind plugin that adds beautiful typographic defaults for rich-text content (the `prose` class). |
| **laravel-vite-plugin** | ^2.0.0 | Bridges Vite and Laravel. Handles asset versioning and the `@vite()` Blade directive. |

### Version Pinning Philosophy

Dependencies use caret (`^`) versioning in `composer.json` and `package.json`. This means:
- `^12.0` = "any version >= 12.0.0 and < 13.0.0"
- `^4.0.0` = "any version >= 4.0.0 and < 5.0.0"

Lock files (`composer.lock` and `package-lock.json`) pin exact versions. Always commit these files and run `composer install` (not `update`) in production.

---

## 3. Directory Structure

```
hilotec/
|
|-- app/                            # Application code (PHP)
|   |-- Console/
|   |   |-- Commands/
|   |       |-- SecurityAudit.php   # Artisan command: php artisan security:audit (master only)
|   |
|   |-- Filament/                   # Filament admin panel code
|   |   |-- Pages/
|   |   |   |-- ManageSettings.php  # Custom page for site-wide settings
|   |   |-- Resources/              # One resource per content type (8 total)
|   |       |-- ServiceResource.php
|   |       |-- PostResource.php
|   |       |-- PageResource.php
|   |       |-- TeamMemberResource.php
|   |       |-- PartnerResource.php
|   |       |-- ReferenceCategoryResource.php
|   |       |-- ReferenceResource.php
|   |       |-- ContactSubmissionResource.php
|   |       |-- ServiceResource/Pages/          # List/Create/Edit pages per resource
|   |       |-- PostResource/Pages/
|   |       |-- ...etc
|   |
|   |-- Http/
|   |   |-- Controllers/            # Frontend controllers (7 + base)
|   |   |   |-- Controller.php      # Abstract base controller
|   |   |   |-- HomeController.php
|   |   |   |-- ServiceController.php
|   |   |   |-- ReferenceController.php
|   |   |   |-- AboutController.php
|   |   |   |-- PostController.php
|   |   |   |-- ContactController.php
|   |   |   |-- PageController.php
|   |   |-- Middleware/              # Custom middleware (master only; not present on design-v2)
|   |       |-- SecurityHeaders.php      # CSP, HSTS, X-Frame-Options, etc.
|   |       |-- ThrottleAdminLogin.php   # Rate limiting for admin login
|   |
|   |-- Models/                     # Eloquent models (10 total)
|   |   |-- User.php
|   |   |-- Setting.php
|   |   |-- Service.php
|   |   |-- ReferenceCategory.php
|   |   |-- Reference.php
|   |   |-- TeamMember.php
|   |   |-- Post.php
|   |   |-- Page.php
|   |   |-- Partner.php
|   |   |-- ContactSubmission.php
|   |
|   |-- Providers/
|   |   |-- Filament/
|   |       |-- AdminPanelProvider.php  # Configures the admin panel
|   |
|   |-- helpers.php                 # Global setting() helper function
|
|-- bootstrap/
|   |-- app.php                     # Application bootstrap (middleware registration)
|
|-- config/                         # Configuration files (one per concern)
|   |-- app.php, database.php, filesystems.php, etc.
|
|-- database/
|   |-- migrations/                 # Database schema definitions (12 files)
|   |   |-- 0001_01_01_000000_create_users_table.php
|   |   |-- 0001_01_01_000001_create_cache_table.php
|   |   |-- 0001_01_01_000002_create_jobs_table.php
|   |   |-- 2024_01_01_000001_create_settings_table.php
|   |   |-- 2024_01_01_000002_create_services_table.php
|   |   |-- 2024_01_01_000003_create_reference_categories_table.php
|   |   |-- 2024_01_01_000004_create_references_table.php
|   |   |-- 2024_01_01_000005_create_team_members_table.php
|   |   |-- 2024_01_01_000006_create_posts_table.php
|   |   |-- 2024_01_01_000007_create_pages_table.php
|   |   |-- 2024_01_01_000008_create_partners_table.php
|   |   |-- 2024_01_01_000009_create_contact_submissions_table.php
|   |-- seeders/                    # Seed data (pre-populates the database)
|   |   |-- DatabaseSeeder.php
|   |   |-- AdminUserSeeder.php
|   |   |-- SettingsSeeder.php
|   |   |-- ServicesSeeder.php
|   |   |-- ReferencesSeeder.php
|   |   |-- PagesSeeder.php
|   |   |-- PostsSeeder.php
|   |-- database.sqlite             # SQLite database file (local dev only)
|
|-- public/                         # Web root (only this directory is exposed)
|   |-- index.php                   # Application entry point
|   |-- .htaccess                   # Apache URL rewriting rules
|   |-- robots.txt
|   |-- favicon.ico
|   |-- images/                     # Static images
|   |   |-- heroes/                 # Hero background images
|   |   |-- backgrounds/            # Section backgrounds
|   |   |-- icons/                  # Service icon SVGs
|   |   |-- branding/               # Logo and brand assets
|   |   |-- meta/                   # Favicons and OG image
|   |-- build/                      # Compiled CSS/JS (Vite output)
|   |-- storage -> ../storage/app/public  # Symlink for uploaded files
|
|-- resources/
|   |-- css/
|   |   |-- app.css                 # Tailwind CSS entry point with @theme tokens
|   |-- js/
|   |   |-- app.js                  # Alpine.js entry point
|   |-- views/
|       |-- components/             # Reusable Blade components (10)
|       |   |-- layout.blade.php
|       |   |-- header.blade.php
|       |   |-- footer.blade.php
|       |   |-- footer-cta.blade.php
|       |   |-- hero.blade.php
|       |   |-- button.blade.php
|       |   |-- section-heading.blade.php
|       |   |-- service-card.blade.php
|       |   |-- post-card.blade.php
|       |   |-- reference-item.blade.php
|       |-- pages/                  # Page-specific templates
|       |   |-- home.blade.php
|       |   |-- about.blade.php
|       |   |-- contact.blade.php
|       |   |-- references.blade.php
|       |   |-- generic.blade.php
|       |   |-- services/
|       |   |   |-- index.blade.php
|       |   |   |-- show.blade.php
|       |   |-- posts/
|       |       |-- index.blade.php
|       |       |-- show.blade.php
|       |-- filament/
|           |-- pages/
|               |-- manage-settings.blade.php  # Custom admin settings page template
|
|-- routes/
|   |-- web.php                     # All public URL routes (10 routes)
|   |-- console.php                 # Artisan console/scheduled command routes
|
|-- storage/
|   |-- app/
|   |   |-- public/                 # Uploaded files (team photos, post images, partner logos)
|   |       |-- team/
|   |       |-- posts/
|   |       |-- partners/
|   |-- framework/
|   |   |-- cache/                  # Application cache (settings cache lives here)
|   |   |-- sessions/               # Session files (if using file driver)
|   |   |-- views/                  # Compiled Blade templates
|   |-- logs/                       # Application log files
|   |-- quarantine/                 # SecurityAudit quarantined files
|
|-- vite.config.js                  # Vite build configuration
|-- composer.json                   # PHP dependencies
|-- package.json                    # JavaScript dependencies
|-- .env                            # Environment variables (not in git)
|-- .env.example                    # Template for .env
|-- .env.production.example         # Production-specific .env settings (master only)
```

### Key Principle: Only `public/` Is Web-Accessible

The web server only exposes the `public/` directory. All PHP source code, configuration, `.env` secrets, and the database file are **outside** the web root. This is a fundamental security boundary -- even if an attacker finds a misconfiguration, they cannot directly access `app/`, `config/`, `database/`, or `.env`.

---

## 4. Database Schema

### Migrations Explained

In Laravel, database tables are not created by running raw SQL. Instead, you write **migration files** -- PHP classes that define the table structure. This approach is like version control for your database schema:

- `php artisan migrate` -- applies all pending migrations (creates/modifies tables)
- `php artisan migrate:rollback` -- undoes the last batch of migrations
- `php artisan migrate:fresh --seed` -- drops ALL tables, re-runs all migrations, and seeds data

Migration files live in `database/migrations/` and are run in filename order.

### Entity Relationship Diagram (ERD)

```
+-------------------+          +------------------------+
|      users        |          |       settings         |
+-------------------+          +------------------------+
| id (PK)           |          | id (PK)                |
| name              |          | group                  |
| email (UNIQUE)    |          | key                    |
| email_verified_at |          | value (nullable)       |
| password          |          | created_at             |
| remember_token    |          | updated_at             |
| created_at        |          +------------------------+
| updated_at        |          UNIQUE(group, key)
+-------------------+

+-------------------+          +------------------------+
|     services      |          |        pages           |
+-------------------+          +------------------------+
| id (PK)           |          | id (PK)                |
| title             |          | title                  |
| slug (UNIQUE)     |          | slug (UNIQUE)          |
| icon (nullable)   |          | hero_heading (nullable)|
| excerpt (nullable)|          | hero_subheading (null) |
| body (nullable)   |          | hero_image (nullable)  |
| sort_order [0]    |          | body (nullable)        |
| is_published [T]  |          | meta_title (nullable)  |
| created_at        |          | meta_description (null)|
| updated_at        |          | is_published [T]       |
+-------------------+          | created_at             |
                               | updated_at             |
                               +------------------------+

+-------------------+          +------------------------+
|      posts        |          |    team_members        |
+-------------------+          +------------------------+
| id (PK)           |          | id (PK)                |
| title             |          | name                   |
| slug (UNIQUE)     |          | role (nullable)        |
| excerpt (nullable)|          | email (nullable)       |
| body (nullable)   |          | phone (nullable)       |
| featured_image    |          | photo (nullable)       |
|   (nullable)      |          | bio (nullable)         |
| is_published [T]  |          | sort_order [0]         |
| published_at      |          | is_published [T]       |
|   (nullable)      |          | created_at             |
| created_at        |          | updated_at             |
| updated_at        |          +------------------------+
+-------------------+

+-------------------+          +------------------------+
|     partners      |          | contact_submissions    |
+-------------------+          +------------------------+
| id (PK)           |          | id (PK)                |
| name              |          | name                   |
| logo (nullable)   |          | email                  |
| website (nullable)|          | phone (nullable)       |
| description (null)|          | message                |
| sort_order [0]    |          | is_read [F]            |
| is_published [T]  |          | created_at             |
| created_at        |          | updated_at             |
| updated_at        |          +------------------------+
+-------------------+

+-------------------------+        +------------------------+
|  reference_categories   |        |      references        |
+-------------------------+        +------------------------+
| id (PK)                 |<-------| id (PK)                |
| name                    |   1:N  | reference_category_id  |
| slug (UNIQUE)           |        |   (FK -> ref_cats.id)  |
| sort_order [0]          |        |   CASCADE ON DELETE     |
| created_at              |        | company_name           |
| updated_at              |        | address (nullable)     |
+-------------------------+        | description (nullable) |
                                   | website (nullable)     |
                                   | sort_order [0]         |
                                   | is_published [T]       |
                                   | created_at             |
                                   | updated_at             |
                                   +------------------------+

Legend:
  PK = Primary Key (auto-incrementing integer)
  FK = Foreign Key
  UNIQUE = Unique constraint
  [T] = Default true
  [F] = Default false
  [0] = Default 0
  1:N = One-to-many relationship
```

### Laravel Framework Tables

These tables are created by Laravel itself and are used for internal framework operations:

| Table | Purpose |
|---|---|
| `sessions` | Stores user session data (login state, flash messages). Each row = one browser session. |
| `password_reset_tokens` | Temporary tokens for the "Forgot Password" flow. |
| `cache` | Key-value store for cached data (including the settings cache). |
| `cache_locks` | Prevents race conditions when multiple processes try to update the same cache key simultaneously. |
| `jobs` | Queue for background tasks (not currently used, but available). |
| `job_batches` | Groups of queued jobs that should run together. |
| `failed_jobs` | Records of jobs that failed, for debugging. |

### All Tables Summary

| Table | Columns | Relationships | Purpose |
|---|---|---|---|
| `users` | id, name, email, email_verified_at, password, remember_token, timestamps | -- | Admin user accounts |
| `settings` | id, group, key, value, timestamps | UNIQUE(group, key) | Site-wide key-value configuration |
| `services` | id, title, slug, icon, excerpt, body, sort_order, is_published, timestamps | -- | IT service offerings |
| `reference_categories` | id, name, slug, sort_order, timestamps | has many `references` | Groupings for client references |
| `references` | id, reference_category_id, company_name, address, description, website, sort_order, is_published, timestamps | belongs to `reference_categories` | Individual client references |
| `team_members` | id, name, role, email, phone, photo, bio, sort_order, is_published, timestamps | -- | Team member profiles |
| `posts` | id, title, slug, excerpt, body, featured_image, is_published, published_at, timestamps | -- | Blog/news articles |
| `pages` | id, title, slug, hero_heading, hero_subheading, hero_image, body, meta_title, meta_description, is_published, timestamps | -- | Generic pages (Impressum, Datenschutz) |
| `partners` | id, name, logo, website, description, sort_order, is_published, timestamps | -- | Technology partners |
| `contact_submissions` | id, name, email, phone, message, is_read, timestamps | -- | Contact form submissions |

---

## 5. Models

Models are PHP classes that represent database tables. Each model lives in `app/Models/` and maps to exactly one table. Laravel's ORM (called "Eloquent") lets you query the database using PHP method chains instead of raw SQL.

### 5.1 User (`app/Models/User.php`)

**Table:** `users`

The User model handles authentication.

> **Branch difference:** On the `master` branch, the User model implements Filament's `FilamentUser` interface with a `canAccessPanel()` method that restricts admin access to emails listed in `ADMIN_EMAILS`. On the `design-v2` branch, the User model does **not** implement `FilamentUser` and has no `canAccessPanel()` method -- any authenticated user can access the admin panel.

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `name`, `email`, `password` |
| `$hidden` | array | `password`, `remember_token` (excluded from JSON/array output) |
| `casts()` | method | `email_verified_at` -> datetime, `password` -> hashed (auto-hashes on assignment) |
| `canAccessPanel(Panel)` | method | (`master` only) Returns `true` only if the user's email is in the `ADMIN_EMAILS` env variable |

**Admin Access Gate (master branch only):**
```php
public function canAccessPanel(Panel $panel): bool
{
    $adminEmails = array_map('trim', explode(',', env('ADMIN_EMAILS', '')));
    return in_array($this->email, $adminEmails);
}
```

This means: parse the comma-separated `ADMIN_EMAILS` environment variable, and check if the current user's email is in that list. If `ADMIN_EMAILS` is not set or empty, nobody can access the admin panel.

### 5.2 Setting (`app/Models/Setting.php`)

**Table:** `settings`

A key-value store for site configuration. Settings are organized into groups (general, contact, footer, social). See [Section 11: Settings System](#11-settings-system) for full details.

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `group`, `key`, `value` |
| `get(string $dotKey, $default)` | static method | Retrieves a setting by "group.key" notation with 60-minute cache |
| `set(string $dotKey, ?string $value)` | static method | Updates or creates a setting, clears the cache |

**Scopes:** None (settings are always accessed via the static `get()`/`set()` methods, not through query scopes).

### 5.3 Service (`app/Models/Service.php`)

**Table:** `services`

Represents an IT service offering (e.g., "Server & Netzwerk", "Cloud & Virtualisierung").

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `title`, `slug`, `icon`, `excerpt`, `body`, `sort_order`, `is_published` |
| `$casts` | array | `is_published` -> boolean, `sort_order` -> integer |
| `scopePublished()` | scope | Filters to `is_published = true` |
| `scopeOrdered()` | scope | Orders by `sort_order ASC` |

**Relationships:** None.

**Usage example:**
```php
// Get all published services in display order
$services = Service::published()->ordered()->get();

// Find one service by its URL slug
$service = Service::published()->where('slug', 'server-netzwerk')->firstOrFail();
```

### 5.4 ReferenceCategory (`app/Models/ReferenceCategory.php`)

**Table:** `reference_categories`

Groups client references into categories (e.g., "Gemeinden/Schulen", "Industrie/Gewerbe").

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `name`, `slug`, `sort_order` |
| `$casts` | array | `sort_order` -> integer |
| `references()` | relationship | Has many `Reference` (one category contains many client references) |
| `scopeOrdered()` | scope | Orders by `sort_order ASC` |

**Note:** No `scopePublished()` -- categories are always visible. The published scope is on the individual references.

### 5.5 Reference (`app/Models/Reference.php`)

**Table:** `references`

An individual client reference (a company that HILOTEC has worked with).

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `reference_category_id`, `company_name`, `address`, `description`, `website`, `sort_order`, `is_published` |
| `$casts` | array | `is_published` -> boolean, `sort_order` -> integer |
| `category()` | relationship | Belongs to `ReferenceCategory` (via `reference_category_id` foreign key) |
| `scopePublished()` | scope | Filters to `is_published = true` |
| `scopeOrdered()` | scope | Orders by `sort_order ASC` |

**Cascade delete:** If a `ReferenceCategory` is deleted, all its `Reference` records are automatically deleted too (defined in the migration's `cascadeOnDelete()`).

### 5.6 TeamMember (`app/Models/TeamMember.php`)

**Table:** `team_members`

A team member profile displayed on the "Uber uns" page.

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `name`, `role`, `email`, `phone`, `photo`, `bio`, `sort_order`, `is_published` |
| `$casts` | array | `is_published` -> boolean, `sort_order` -> integer |
| `scopePublished()` | scope | Filters to `is_published = true` |
| `scopeOrdered()` | scope | Orders by `sort_order ASC` |

**Relationships:** None. The `photo` field stores a file path relative to `storage/app/public/` (see [Section 14](#14-file-upload--storage)).

### 5.7 Post (`app/Models/Post.php`)

**Table:** `posts`

A news/blog article displayed on the "Aktuelles" page.

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `title`, `slug`, `excerpt`, `body`, `featured_image`, `is_published`, `published_at` |
| `$casts` | array | `is_published` -> boolean, `published_at` -> datetime |
| `scopePublished()` | scope | Filters to `is_published = true` AND `published_at` is not null AND `published_at <= now()` |
| `scopeLatest()` | scope | Orders by `published_at DESC` (newest first) |

**Important:** The `scopePublished()` method on Post has **three conditions**, unlike other models which only check `is_published`. This allows you to write a post, set it as published, and schedule it for a future date -- it will not appear on the site until that date arrives.

**Relationships:** None. The `featured_image` field stores a file path relative to `storage/app/public/`.

### 5.8 Page (`app/Models/Page.php`)

**Table:** `pages`

A generic content page served by the catch-all route (e.g., Impressum, Datenschutz). Also used by the AboutController to fetch the "ueber-uns" page content.

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `title`, `slug`, `hero_heading`, `hero_subheading`, `hero_image`, `body`, `meta_title`, `meta_description`, `is_published` |
| `$casts` | array | `is_published` -> boolean |
| `scopePublished()` | scope | Filters to `is_published = true` |

**Relationships:** None.

### 5.9 Partner (`app/Models/Partner.php`)

**Table:** `partners`

A technology partner displayed on the site (e.g., Microsoft, VMware).

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `name`, `logo`, `website`, `description`, `sort_order`, `is_published` |
| `$casts` | array | `is_published` -> boolean, `sort_order` -> integer |
| `scopePublished()` | scope | Filters to `is_published = true` |
| `scopeOrdered()` | scope | Orders by `sort_order ASC` |

**Relationships:** None. The `logo` field stores a file path relative to `storage/app/public/`.

### 5.10 ContactSubmission (`app/Models/ContactSubmission.php`)

**Table:** `contact_submissions`

A form submission from the contact page. This model has no scopes -- submissions are only viewed in the admin panel.

| Property/Method | Type | Description |
|---|---|---|
| `$fillable` | array | `name`, `email`, `phone`, `message`, `is_read` |
| `$casts` | array | `is_read` -> boolean |

**Relationships:** None. **Scopes:** None.

---

## 6. Controllers

Controllers live in `app/Http/Controllers/`. Each controller is intentionally thin -- it fetches data from models and passes it to a view. There is no business logic in controllers.

### 6.1 Controller (Base) (`app/Http/Controllers/Controller.php`)

```php
abstract class Controller
{
    //
}
```

An empty abstract base class. All other controllers extend this. Laravel uses it as a place to add shared functionality if needed.

### 6.2 HomeController (`app/Http/Controllers/HomeController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `index()` | `GET /` | `Service::published()->ordered()->get()` | `pages.home` |

Fetches all published services (in sort order) and passes them to the homepage template, which displays them as service cards in a grid.

### 6.3 ServiceController (`app/Http/Controllers/ServiceController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `index()` | `GET /angebot` | `Service::published()->ordered()->get()` | `pages.services.index` |
| `show($slug)` | `GET /angebot/{slug}` | Single service by slug + all services for sidebar | `pages.services.show` |

The `show()` method uses `firstOrFail()` -- if no published service matches the slug, Laravel automatically returns a 404 error page.

### 6.4 ReferenceController (`app/Http/Controllers/ReferenceController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `index()` | `GET /referenzen` | All categories (ordered) with their published, ordered references | `pages.references` |

Uses **eager loading** to avoid the "N+1 query problem":
```php
$categories = ReferenceCategory::ordered()
    ->with(['references' => fn ($q) => $q->published()->ordered()])
    ->get();
```

This loads all categories and their references in just 2 SQL queries, instead of 1 + N queries (where N = number of categories).

### 6.5 AboutController (`app/Http/Controllers/AboutController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `index()` | `GET /ueber-uns` | The "ueber-uns" Page record + all published, ordered TeamMembers | `pages.about` |

Fetches the page content (hero heading, body text) from the `pages` table and team member profiles from the `team_members` table.

### 6.6 PostController (`app/Http/Controllers/PostController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `index()` | `GET /aktuelles` | `Post::published()->latest()->get()` | `pages.posts.index` |
| `show($slug)` | `GET /aktuelles/{slug}` | Single published post by slug | `pages.posts.show` |

The `latest()` scope on Post orders by `published_at DESC` (newest first). The `published()` scope ensures only posts with `is_published = true` AND `published_at <= now()` are shown.

### 6.7 ContactController (`app/Http/Controllers/ContactController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `index()` | `GET /kontakt` | None | `pages.contact` |
| `send(Request)` | `POST /kontakt` | None (creates data) | Redirect back with success message |

The `send()` method validates the form submission:

| Field | Rules |
|---|---|
| `name` | Required, string, max 255 characters |
| `email` | Required, valid email format, max 255 characters |
| `phone` | Optional, string, max 255 characters |
| `message` | Required, string, max 5000 characters |

If validation passes, a `ContactSubmission` record is created and the user sees a success flash message: "Vielen Dank fur Ihre Nachricht. Wir melden uns so bald wie moglich bei Ihnen."

If validation fails, Laravel automatically redirects back to the form with error messages attached.

### 6.8 PageController (`app/Http/Controllers/PageController.php`)

| Method | Route | Data Fetched | View Returned |
|---|---|---|---|
| `show($slug)` | `GET /{slug}` | Single published page by slug | `pages.generic` |

This is the **catch-all** controller. Any URL that does not match a specific route (like `/impressum` or `/datenschutz`) falls through to this controller. It looks up the slug in the `pages` table and renders a generic page template. If no page matches, it returns a 404.

---

## 7. Routing

All routes are defined in `routes/web.php`. Routes map URLs to controller methods.

### Complete Route Table

| HTTP Method | URL | Controller | Method | Route Name | Purpose |
|---|---|---|---|---|---|
| GET | `/` | HomeController | `index()` | `home` | Homepage |
| GET | `/angebot` | ServiceController | `index()` | `services.index` | Services listing |
| GET | `/angebot/{slug}` | ServiceController | `show($slug)` | `services.show` | Service detail page |
| GET | `/referenzen` | ReferenceController | `index()` | `references.index` | Client references |
| GET | `/ueber-uns` | AboutController | `index()` | `about` | About us page |
| GET | `/aktuelles` | PostController | `index()` | `posts.index` | News/blog listing |
| GET | `/aktuelles/{slug}` | PostController | `show($slug)` | `posts.show` | Single news article |
| GET | `/kontakt` | ContactController | `index()` | `contact` | Contact form |
| POST | `/kontakt` | ContactController | `send()` | `contact.send` | Contact form submission |
| GET | `/{slug}` | PageController | `show($slug)` | `pages.show` | Generic pages (catch-all) |

### Route Names and the `route()` Helper

Every route has a name (e.g., `services.show`). In Blade templates, you can generate URLs using the `route()` helper instead of hardcoding paths:

```blade
{{-- Instead of hardcoding: --}}
<a href="/angebot/server-netzwerk">

{{-- Use the route helper: --}}
<a href="{{ route('services.show', 'server-netzwerk') }}">
```

This means if you ever change the URL structure (e.g., from `/angebot` to `/services`), you only change it in `routes/web.php` and all links update automatically.

### Route Order Matters

The catch-all route `/{slug}` **must be the last route** in `web.php`. If it were placed first, it would intercept every URL (including `/angebot`, `/referenzen`, etc.) and try to look them up as generic pages.

### Admin Routes (Automatic)

Filament automatically registers admin panel routes under `/admin/*`. These are not defined in `web.php` -- they are generated by the `AdminPanelProvider` class. The admin routes include:

- `/admin` -- Dashboard
- `/admin/login` -- Login page
- `/admin/services` -- Service management
- `/admin/posts` -- Post management
- `/admin/manage-settings` -- Settings page
- ...and so on for each resource

---

## 8. Filament Admin

### What is Filament?

Filament is a framework that generates a complete admin panel from PHP class definitions. Instead of building admin forms, tables, and pages by hand, you define a "Resource" class that describes your data model, and Filament generates the entire CRUD interface.

### Panel Configuration (`app/Providers/Filament/AdminPanelProvider.php`)

The admin panel is configured in a single provider class:

```php
return $panel
    ->default()
    ->id('admin')
    ->path('admin')                    // URL prefix: /admin
    ->login()                          // Enable login page
    ->passwordReset()                  // Enable password reset flow
    ->colors(['primary' => Color::Amber])  // Gold/amber theme
    ->discoverResources(...)           // Auto-discover resource classes
    ->discoverPages(...)               // Auto-discover custom pages
    ->middleware([...])                 // Middleware stack (see below)
    ->authMiddleware([Authenticate::class]);
```

**Admin middleware stack** (applied to every admin request):

| Middleware | Purpose |
|---|---|
| `EncryptCookies` | Encrypts cookie values |
| `AddQueuedCookiesToResponse` | Sends queued cookies back to the browser |
| `StartSession` | Starts the PHP session |
| `AuthenticateSession` | Validates the session has not been tampered with |
| `ShareErrorsFromSession` | Makes validation errors available to views |
| `VerifyCsrfToken` | Prevents cross-site request forgery attacks |
| `SubstituteBindings` | Resolves route model bindings (URL parameters to model instances) |
| `DisableBladeIconComponents` | Filament-specific optimization |
| `DispatchServingFilamentEvent` | Fires the Filament "serving" event |
| `ThrottleRequests:60,1` | Rate limits to 60 requests per minute per IP |
| `Authenticate` (auth middleware) | Requires login + `canAccessPanel()` check |

### Resources (8 Total)

Each resource class generates a list view, create form, and edit form.

#### ServiceResource

| Property | Value |
|---|---|
| **Model** | Service |
| **Navigation Group** | Inhalte |
| **Navigation Sort** | 1 |
| **Navigation Icon** | heroicon-o-wrench-screwdriver |
| **Form Fields** | title (TextInput, required), slug (TextInput, required, unique), icon (TextInput), excerpt (Textarea), body (RichEditor), sort_order (TextInput, numeric), is_published (Toggle) |
| **Table Columns** | title (searchable, sortable), sort_order (sortable), is_published (icon) |
| **Table Features** | Default sort by sort_order, drag-reorder by sort_order, bulk delete |
| **Pages** | List, Create, Edit |

#### PostResource

| Property | Value |
|---|---|
| **Model** | Post |
| **Navigation Group** | Inhalte |
| **Navigation Sort** | 2 |
| **Navigation Label** | Beitrage |
| **Navigation Icon** | heroicon-o-newspaper |
| **Form Fields** | title, slug (unique), excerpt, body (RichEditor), featured_image (FileUpload, image, directory: "posts"), published_at (DateTimePicker), is_published (Toggle) |
| **Table Columns** | title (searchable, sortable), published_at (date format: d.m.Y, sortable), is_published (icon) |
| **Table Features** | Default sort by published_at DESC, bulk delete |
| **Pages** | List, Create, Edit |

#### PageResource

| Property | Value |
|---|---|
| **Model** | Page |
| **Navigation Group** | Inhalte |
| **Navigation Sort** | 3 |
| **Navigation Label** | Seiten |
| **Navigation Icon** | heroicon-o-document-text |
| **Form Fields** | title, slug (unique), hero_heading, hero_subheading, hero_image, body (RichEditor), meta_title, meta_description (Textarea), is_published (Toggle) |
| **Table Columns** | title (searchable, sortable), slug, is_published (icon) |
| **Table Features** | Bulk delete |
| **Pages** | List, Create, Edit |

#### TeamMemberResource

| Property | Value |
|---|---|
| **Model** | TeamMember |
| **Navigation Group** | Inhalte |
| **Navigation Sort** | 4 |
| **Navigation Icon** | heroicon-o-user-group |
| **Form Fields** | name (required), role, email, phone, photo (FileUpload, image, directory: "team"), bio (Textarea), sort_order (numeric), is_published (Toggle) |
| **Table Columns** | name (searchable, sortable), role, sort_order (sortable), is_published (icon) |
| **Table Features** | Default sort by sort_order, drag-reorder by sort_order, bulk delete |
| **Pages** | List, Create, Edit |

#### PartnerResource

| Property | Value |
|---|---|
| **Model** | Partner |
| **Navigation Group** | Inhalte |
| **Navigation Sort** | 5 |
| **Navigation Icon** | heroicon-o-link |
| **Form Fields** | name (required), logo (FileUpload, image, directory: "partners"), website, description (Textarea), sort_order (numeric), is_published (Toggle) |
| **Table Columns** | name (searchable, sortable), website, sort_order (sortable), is_published (icon) |
| **Table Features** | Default sort by sort_order, drag-reorder by sort_order, bulk delete |
| **Pages** | List, Create, Edit |

#### ReferenceCategoryResource

| Property | Value |
|---|---|
| **Model** | ReferenceCategory |
| **Navigation Group** | Referenzen |
| **Navigation Sort** | 1 |
| **Navigation Label** | Kategorien |
| **Navigation Icon** | heroicon-o-folder |
| **Form Fields** | name (required), slug (unique), sort_order (numeric) |
| **Table Columns** | name (searchable, sortable), references_count (count of associated references), sort_order (sortable) |
| **Table Features** | Default sort by sort_order, drag-reorder by sort_order, bulk delete |
| **Pages** | List, Create, Edit |

#### ReferenceResource

| Property | Value |
|---|---|
| **Model** | Reference |
| **Navigation Group** | Referenzen |
| **Navigation Sort** | 2 |
| **Navigation Icon** | heroicon-o-building-office-2 |
| **Form Fields** | reference_category_id (Select with relationship to category name, required, searchable, preloaded), company_name (required), address, description (Textarea), website, sort_order (numeric), is_published (Toggle) |
| **Table Columns** | company_name (searchable, sortable), category.name (sortable), website, is_published (icon) |
| **Table Features** | Default sort by company_name, bulk delete |
| **Pages** | List, Create, Edit |

#### ContactSubmissionResource

| Property | Value |
|---|---|
| **Model** | ContactSubmission |
| **Navigation Group** | Kontakt |
| **Navigation Label** | Anfragen |
| **Navigation Icon** | heroicon-o-envelope |
| **Form Fields** | name (disabled), email (disabled), phone (disabled), message (Textarea, disabled), is_read (Toggle) |
| **Table Columns** | name (searchable, sortable), email (searchable), created_at (date format: d.m.Y H:i, sortable), is_read (icon) |
| **Table Features** | Default sort by created_at DESC, bulk delete |
| **Pages** | List, View (not Edit -- submissions are read-only except for the is_read toggle) |

### Navigation Groups

The admin sidebar organizes resources into groups:

```
Dashboard
Inhalte
  |-- Services (sort: 1)
  |-- Beitrage (sort: 2)
  |-- Seiten (sort: 3)
  |-- Team Members (sort: 4)
  |-- Partners (sort: 5)
Referenzen
  |-- Kategorien (sort: 1)
  |-- References (sort: 2)
Kontakt
  |-- Anfragen
Einstellungen (sort: 99, at bottom)
```

### ManageSettings Custom Page (`app/Filament/Pages/ManageSettings.php`)

This is a custom Filament page (not a resource) that provides a tabbed form for editing site-wide settings. It is not auto-generated from a model -- it is hand-coded.

**Key implementation details:**

1. **mount():** On page load, fetches all settings from the database and fills the form. Field names use double-underscore notation (`group__key`) because Filament form field names cannot contain dots.

2. **form():** Defines a `Tabs` component with 4 tabs:
   - **Allgemein** (General): company_name, company_slogan, company_subtitle, founded_year, about_short
   - **Kontakt** (Contact): address_line1, address_zip_city, address_country, phone_support_infra, phone_label_infra, phone_support_software, phone_label_software, email, website, business_hours
   - **Footer**: cta_heading, cta_button_text, cta_button_url, copyright_text, teamviewer_text, teamviewer_url
   - **Social Media**: linkedin, github

3. **save():** Iterates over all form fields, converts `group__key` back to `group.key`, and calls `Setting::set()` for each. Each `set()` call clears the settings cache.

**Template** (`resources/views/filament/pages/manage-settings.blade.php`):
```blade
<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        <div class="mt-6">
            <x-filament::button type="submit">Speichern</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
```

---

## 9. Blade Components

> **Note:** On the `design-v2` branch, Blade components have been redesigned for the Alpine Precision theme (light/dark hybrid with scroll animations). Refer to [05-DESIGN-SYSTEM.md](05-DESIGN-SYSTEM.md) for details on the updated component designs and variants.

Blade components are reusable HTML building blocks. They live in `resources/views/components/` and are used in templates with the `<x-component-name>` syntax. This project has 10 anonymous components (no backing PHP class -- just Blade template files).

### 9.1 Layout (`layout.blade.php`)

The master layout that wraps every page. Defines the HTML document structure, loads fonts, CSS, JS, and includes the header/footer.

**Props:**

| Prop | Type | Default | Description |
|---|---|---|---|
| `title` | string | null | Page title (appended to company name with " -- " separator) |
| `metaDescription` | string | null | Meta description for SEO |
| `metaImage` | string | null | OG image path |
| `fullHero` | bool | false | Whether page has full-height hero (not currently used in layout logic) |

**Usage:**
```blade
<x-layout title="Unsere Leistungen" metaDescription="IT-Dienstleistungen...">
    {{-- Page content goes in the $slot --}}
    <x-hero heading="Angebot" />
    <section>...</section>
</x-layout>
```

**What it renders:**
- `<html lang="de">` (German language)
- CSRF meta tag (for form security)
- `<title>` tag: "Page Title -- HILOTEC Engineering + Consulting AG"
- Open Graph meta tags (og:title, og:description, og:image)
- Favicon links
- Google Fonts preconnect + stylesheet link (Sora + DM Sans)
- `@vite()` directive (loads compiled CSS and JS)
- `<x-header />` component
- `<main>{{ $slot }}</main>` (page content)
- `<x-footer-cta />` component
- `<x-footer />` component

### 9.2 Header (`header.blade.php`)

Sticky site header with transparent-to-solid scroll transition and mobile hamburger menu.

**Props:** None.

**Alpine.js data:**
- `scrolled` -- tracks whether the page has scrolled past 50px (triggers background change)
- `mobileOpen` -- toggles the mobile navigation menu

**Navigation items** are hardcoded in the component (not from database):
- Home (`/`)
- Angebot (`/angebot`)
- Referenzen (`/referenzen`)
- Uber uns (`/ueber-uns`)
- Aktuelles (`/aktuelles`)
- Kontakt (`/kontakt`)

**Active state:** The current page's nav link is highlighted in gold with a small gold dot underneath. Detection uses `request()->is()` to match the current URL path.

**Usage:**
```blade
{{-- Used automatically inside <x-layout> --}}
<x-header />
```

### 9.3 Footer (`footer.blade.php`)

Five-column footer with gold headings. Pulls content from settings.

**Props:** None.

**Columns:**
1. Company logo
2. Fernwartung (remote support) with TeamViewer badge
3. Navigation links
4. Anschrift (address) from settings
5. Kontakt (phone numbers and email) from settings

**Bottom bar:** Copyright text, Impressum/Datenschutz links, LinkedIn/GitHub icons (conditionally shown based on settings).

**Usage:**
```blade
{{-- Used automatically inside <x-layout> --}}
<x-footer />
```

### 9.4 Footer CTA (`footer-cta.blade.php`)

A call-to-action section that appears above the footer on every page. Gold card on a dark matrix-style background.

**Props:** None.

**Content from settings:** `footer.cta_heading`, `footer.cta_button_text`, `footer.cta_button_url`.

**Usage:**
```blade
{{-- Used automatically inside <x-layout> --}}
<x-footer-cta />
```

### 9.5 Hero (`hero.blade.php`)

Full-width hero section with background image, heading, optional subheading, and optional CTA button.

**Props:**

| Prop | Type | Default | Description |
|---|---|---|---|
| `heading` | string | (required) | Main heading text |
| `subheading` | string | null | Subtitle text |
| `image` | string | `heroes/inner_page_hero_bg.jpg` | Background image path relative to `/images/` |
| `ctaText` | string | null | CTA button label |
| `ctaUrl` | string | null | CTA button URL |
| `fullHeight` | bool | false | Full viewport height (`min-h-screen`) or compact |
| `centered` | bool | false | Center-align text or left-align |

**Usage:**
```blade
{{-- Full-height home hero with CTA --}}
<x-hero
    heading="HILOTEC Engineering + Consulting AG"
    subheading="Ihr Partner fur IT-Infrastruktur"
    image="heroes/home_hero_bg.jpg"
    ctaText="Unsere Leistungen entdecken"
    ctaUrl="/angebot"
    :fullHeight="true"
/>

{{-- Compact inner-page hero --}}
<x-hero heading="Angebot" subheading="Unsere IT-Dienstleistungen" />
```

When `fullHeight` is true, a bouncing "Entdecken" arrow appears at the bottom that smooth-scrolls to the next section.

### 9.6 Button (`button.blade.php`)

A versatile button/link component with three visual variants and two sizes.

**Props:**

| Prop | Type | Default | Description |
|---|---|---|---|
| `href` | string | null | If provided, renders `<a>` tag; otherwise renders `<button>` |
| `variant` | string | `blue` | Visual style: `blue`, `gold`, or `outline` |
| `size` | string | `md` | Size: `md` or `lg` |
| `type` | string | `button` | HTML button type (only for `<button>`, not `<a>`) |

**Variants:**
- `blue` -- Blue background, white text (primary action)
- `gold` -- Gold background, black text (accent action)
- `outline` -- Gold border, gold text, fills gold on hover

**Usage:**
```blade
<x-button href="/kontakt" variant="blue" size="lg">Kontakt aufnehmen</x-button>
<x-button type="submit" variant="gold">Absenden</x-button>
<x-button href="/angebot" variant="outline">Mehr erfahren</x-button>
```

### 9.7 Section Heading (`section-heading.blade.php`)

Consistent section headings with optional subtitle.

**Props:**

| Prop | Type | Default | Description |
|---|---|---|---|
| `title` | string | (required) | Section title |
| `subtitle` | string | null | Section subtitle |
| `centered` | bool | true | Center alignment |
| `light` | bool | true | Light text (for dark backgrounds) |

**Usage:**
```blade
<x-section-heading
    title="Unsere Leistungen"
    subtitle="Wir bieten umfassende IT-Dienstleistungen"
/>
```

### 9.8 Service Card (`service-card.blade.php`)

Displays a service as a clickable card with icon, title, excerpt, and "Mehr erfahren" link.

**Props:**

| Prop | Type | Description |
|---|---|---|
| `service` | `App\Models\Service` | Service model instance |

**Usage:**
```blade
@foreach($services as $service)
    <x-service-card :service="$service" />
@endforeach
```

Renders: icon (from `images/icons/{service.icon}`), title, truncated excerpt (150 chars), and a "Mehr erfahren" link to the service detail page. The entire card is a clickable `<a>` tag.

### 9.9 Post Card (`post-card.blade.php`)

Displays a blog post as a clickable card with featured image, date, title, and excerpt.

**Props:**

| Prop | Type | Description |
|---|---|---|
| `post` | `App\Models\Post` | Post model instance |

**Usage:**
```blade
@foreach($posts as $post)
    <x-post-card :post="$post" />
@endforeach
```

Renders: featured image (from storage), publication date (format: d.m.Y), title, truncated excerpt (160 chars), and a "Weiterlesen" link. The featured image uses `asset('storage/' . $post->featured_image)`.

### 9.10 Reference Item (`reference-item.blade.php`)

Displays a single client reference as a list item with company name, address, and description.

**Props:**

| Prop | Type | Description |
|---|---|---|
| `reference` | `App\Models\Reference` | Reference model instance |

**Usage:**
```blade
@foreach($category->references as $reference)
    <x-reference-item :reference="$reference" />
@endforeach
```

Renders: company name (with external link icon if website is set), address, and description. The company name links to `https://{website}` with `target="_blank"` and `rel="noopener noreferrer"`.

---

## 10. Frontend Architecture

### Tailwind CSS 4 with @theme

Tailwind CSS 4 introduces a new way to define design tokens. Instead of a `tailwind.config.js` file (used in Tailwind v3), you define your design system directly in your CSS file using the `@theme` directive.

**File:** `resources/css/app.css`

```css
@import 'tailwindcss';

@plugin '@tailwindcss/typography';

@theme {
    /* Colors */
    --color-hilotec-dark: #0a0a0a;
    --color-hilotec-darker: #050505;
    --color-hilotec-surface: #111318;
    --color-hilotec-gold: #d4a843;
    --color-hilotec-gold-dark: #b8922e;
    --color-hilotec-gold-light: #e4be5a;
    --color-hilotec-blue: #2563eb;
    --color-hilotec-blue-dark: #1d4ed8;
    --color-hilotec-gray: #9ca3af;
    --color-hilotec-gray-light: #d1d5db;
    --color-hilotec-gray-dark: #4b5563;

    /* Font families */
    --font-heading: 'Sora', ui-sans-serif, system-ui, sans-serif;
    --font-body: 'DM Sans', ui-sans-serif, system-ui, sans-serif;

    /* Max widths */
    --container-content: 1280px;
}
```

**What @theme does:** It registers custom CSS variables as Tailwind utility classes. After defining `--color-hilotec-gold: #d4a843`, you can use:
- `text-hilotec-gold` -- gold text color
- `bg-hilotec-gold` -- gold background
- `border-hilotec-gold` -- gold border
- `ring-hilotec-gold` -- gold focus ring

Similarly, `--font-heading` enables the `font-heading` class.

**Color palette:**

| Token | Hex | Usage |
|---|---|---|
| `hilotec-dark` | #0a0a0a | Page background |
| `hilotec-darker` | #050505 | Footer background |
| `hilotec-surface` | #111318 | Card backgrounds |
| `hilotec-gold` | #d4a843 | Primary accent (brand gold) |
| `hilotec-gold-dark` | #b8922e | Gold hover state |
| `hilotec-gold-light` | #e4be5a | Gold light variant |
| `hilotec-blue` | #2563eb | Secondary accent (buttons, links) |
| `hilotec-blue-dark` | #1d4ed8 | Blue hover state |
| `hilotec-gray` | #9ca3af | Body text on dark backgrounds |
| `hilotec-gray-light` | #d1d5db | Subtitle text |
| `hilotec-gray-dark` | #4b5563 | Muted text |

**Base styles** (applied globally via `@layer base`):
- `body`: DM Sans font, dark background (#0a0a0a), white text, anti-aliased rendering
- `h1-h6`: Sora font (headings)

**Content source directives:**
```css
@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

These tell Tailwind where to scan for utility class usage. Without them, Tailwind would not know which classes to include in the compiled CSS.

### Alpine.js

**File:** `resources/js/app.js`

> **Note:** On the `design-v2` branch, `app.js` also imports the Alpine.js Intersect plugin (`@alpinejs/intersect`) for scroll-triggered reveal animations and includes an animated counter feature for the statistics section. See [05-DESIGN-SYSTEM.md](05-DESIGN-SYSTEM.md) for details.

```javascript
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

Alpine.js is used in three places:

1. **Header scroll detection** -- `x-data="{ scrolled: false }"` with a scroll event listener that changes the header's background from transparent to solid.

2. **Mobile menu toggle** -- `mobileOpen` boolean that shows/hides the mobile navigation drawer with slide/fade transitions.

3. **Reference category filters** -- (in the references page) Alpine may be used for client-side category filtering.

Alpine.js uses HTML attributes prefixed with `x-`:
- `x-data` -- declares a component's reactive data
- `x-show` -- conditionally shows/hides an element
- `x-cloak` -- hides an element until Alpine initializes (prevents flash of unstyled content)
- `x-transition` -- adds enter/leave animations
- `@click` -- listens for click events
- `:class` -- dynamically binds CSS classes

### Vite Build System

**File:** `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
```

**Entry points:** Two files are compiled:
- `resources/css/app.css` -- Tailwind CSS with custom theme tokens, compiled to a single CSS file
- `resources/js/app.js` -- Alpine.js import and initialization, bundled to a single JS file

**Output:** Compiled files go to `public/build/` with content-hashed filenames (e.g., `app-BxG7k9Qz.css`). The hash changes whenever the file content changes, which busts browser caches automatically.

**In Blade templates:** The `@vite()` directive outputs the correct `<link>` and `<script>` tags:
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

**Development mode:** Run `npm run dev` to start the Vite dev server, which provides:
- Hot Module Replacement (HMR) -- CSS changes appear instantly without page reload
- Fast JavaScript rebundling on save

**Production build:** Run `npm run build` to generate optimized, minified, content-hashed assets.

### Google Fonts

Two font families are loaded from Google Fonts via CDN:

| Font | CSS Variable | Class | Usage |
|---|---|---|---|
| Sora | `--font-heading` | `font-heading` | Headings (h1-h6), buttons, navigation |
| DM Sans | `--font-body` | `font-body` | Body text, paragraphs |

Loaded in `layout.blade.php` with `<link rel="preconnect">` for performance:
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:...&family=Sora:...&display=swap" rel="stylesheet">
```

---

## 11. Settings System

### Overview

The settings system is a key-value store that replaces hardcoded text throughout the site. Instead of editing Blade templates to change the company phone number or footer text, administrators edit settings in the admin panel and the changes appear instantly (after cache expiration).

### How It Works (Detailed Flow)

```
Admin saves setting in /admin/manage-settings
     |
     v
ManageSettings::save() calls Setting::set('contact.email', 'new@hilotec.com')
     |
     v
Setting::set() splits 'contact.email' into group='contact', key='email'
     |
     v
Eloquent updateOrCreate(): INSERT or UPDATE the row in the settings table
     |
     v
Cache::forget('settings.all') clears the cached settings
     |
     v
Next page load: Setting::get('contact.email') is called
     |
     v
Cache::remember('settings.all', 3600, ...) -- cache miss, fetches ALL settings
     |
     v
All settings are loaded into a key-value collection and cached for 3600 seconds (60 min)
     |
     v
Returns the value for 'contact.email'
```

### The Setting Model (`app/Models/Setting.php`)

```php
class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    public static function get(string $dotKey, mixed $default = null): mixed
    {
        $settings = Cache::remember('settings.all', 3600, function () {
            return static::all()
                ->mapWithKeys(fn ($s) => ["{$s->group}.{$s->key}" => $s->value]);
        });
        return $settings->get($dotKey, $default);
    }

    public static function set(string $dotKey, ?string $value): void
    {
        [$group, $key] = explode('.', $dotKey, 2);
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
        Cache::forget('settings.all');
    }
}
```

**Key behavior:**
- **All settings are loaded at once** and cached as a flat key-value map. This means the first `setting()` call on a page load triggers one SQL query, and all subsequent `setting()` calls on the same request (and for the next 60 minutes) read from cache.
- **Cache invalidation** happens automatically when any setting is updated via `Setting::set()`.

### The Global Helper Function (`app/helpers.php`)

```php
function setting(string $key, mixed $default = null): mixed
{
    return Setting::get($key, $default);
}
```

This file is autoloaded via `composer.json`:
```json
"autoload": {
    "files": ["app/helpers.php"]
}
```

This means the `setting()` function is available everywhere -- controllers, models, Blade templates, middleware -- without any `use` statement.

### Usage in Blade Templates

```blade
{{-- Display company name --}}
{{ setting('general.company_name') }}

{{-- Display phone number as clickable link --}}
<a href="tel:{{ str_replace(' ', '', setting('contact.phone_support_infra')) }}">
    {{ setting('contact.phone_support_infra') }}
</a>

{{-- Conditional rendering --}}
@if(setting('social.linkedin'))
    <a href="{{ setting('social.linkedin') }}">LinkedIn</a>
@endif
```

### Complete Settings Reference

| Group | Key | Example Value | Used In |
|---|---|---|---|
| **general** | `company_name` | HILOTEC Engineering + Consulting AG | Layout title, footer, header alt text |
| **general** | `company_slogan` | (slogan text) | Homepage |
| **general** | `company_subtitle` | (subtitle text) | Homepage |
| **general** | `founded_year` | 1998 | About page |
| **general** | `about_short` | (short description) | Various sections |
| **contact** | `address_line1` | Bahnhofstrasse 6 | Footer |
| **contact** | `address_zip_city` | 3552 Baren | Footer |
| **contact** | `address_country` | Schweiz | Footer |
| **contact** | `phone_support_infra` | +41 34 408 01 00 | Footer, contact page |
| **contact** | `phone_label_infra` | IT-Infrastruktur | Footer |
| **contact** | `phone_support_software` | +41 34 408 01 10 | Footer, contact page |
| **contact** | `phone_label_software` | Software | Footer |
| **contact** | `email` | info@hilotec.com | Footer, contact page |
| **contact** | `website` | www.hilotec.com | Footer |
| **contact** | `business_hours` | Mo-Fr 07:30-12:00 / 13:00-17:00 | Contact page |
| **footer** | `cta_heading` | (CTA text) | Footer CTA section |
| **footer** | `cta_button_text` | Kontakt aufnehmen | Footer CTA button |
| **footer** | `cta_button_url` | /kontakt | Footer CTA button link |
| **footer** | `copyright_text` | (copyright notice) | Footer bottom bar |
| **footer** | `teamviewer_text` | (TeamViewer description) | Footer Fernwartung column |
| **footer** | `teamviewer_url` | (TeamViewer download link) | Footer Fernwartung column |
| **social** | `linkedin` | https://linkedin.com/company/hilotec | Footer social icons |
| **social** | `github` | https://github.com/hilotec | Footer social icons |

---

## 12. Security Architecture

> **Note:** The SecurityHeaders middleware, ThrottleAdminLogin middleware, SecurityAudit command, and ADMIN_EMAILS access control described in this section are only present on the `master` branch. On `design-v2`, only the Laravel built-in layers (CSRF, Eloquent ORM, Blade escaping) are active. See [09-SECURITY.md](09-SECURITY.md) for full details.

### Security Layers Overview

```
Internet
  |
  v
+---------------------+
|  Web Server          |  (Apache/Nginx)
|  .htaccess rules     |  URL rewriting, directory protection
+---------------------+
  |
  v
+---------------------+
|  SecurityHeaders     |  HTTP response headers:
|  Middleware           |  CSP, HSTS, X-Frame-Options,
|  (master only)       |  Permissions-Policy, etc.
+---------------------+
  |
  v
+---------------------+
|  CSRF Protection     |  Laravel verifies anti-CSRF tokens
|  (all POST requests) |  on all form submissions
+---------------------+
  |
  v
+---------------------+
|  Filament Auth       |  Login required for /admin/*
|  + canAccessPanel()  |  Email must be in ADMIN_EMAILS env (master only)
+---------------------+
  |
  v
+---------------------+
|  ThrottleRequests    |  Rate limiting: 60 req/min for admin (master only)
|  + ThrottleAdminLogin|  + 5 login attempts/min per IP (master only)
+---------------------+
  |
  v
+---------------------+
|  Eloquent ORM        |  Parameterized queries prevent SQL injection
|  (all DB queries)    |  No raw SQL anywhere in codebase
+---------------------+
  |
  v
+---------------------+
|  Blade {{ }}         |  Auto-escapes output to prevent XSS
|  (all templates)     |  (Cross-Site Scripting)
+---------------------+
```

### SecurityHeaders Middleware (`app/Http/Middleware/SecurityHeaders.php`)

> *This middleware only exists on the `master` branch. It is not present on `design-v2`.*

Registered globally in `bootstrap/app.php`:
```php
$middleware->append(\App\Http\Middleware\SecurityHeaders::class);
```

This middleware adds security headers to every HTML response. Here is what each header does:

| Header | Value | What It Prevents |
|---|---|---|
| `X-Frame-Options` | `SAMEORIGIN` | **Clickjacking** -- prevents other sites from embedding this site in an iframe |
| `X-Content-Type-Options` | `nosniff` | **MIME sniffing** -- prevents browsers from reinterpreting file types (e.g., treating a text file as JavaScript) |
| `X-XSS-Protection` | `1; mode=block` | **XSS** -- legacy browsers' built-in XSS filter (modern browsers use CSP instead) |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | **Information leakage** -- only sends the domain (not full URL) to external sites |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains; preload` | **Downgrade attacks** -- forces HTTPS for 1 year, includes subdomains, allows HSTS preload list. Only sent in production or over HTTPS. |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()` | **Feature abuse** -- disables browser APIs the site does not need |
| `Cross-Origin-Opener-Policy` | `same-origin` | **Cross-origin attacks** -- isolates the browsing context |
| `Cross-Origin-Resource-Policy` | `same-origin` | **Resource theft** -- prevents other origins from loading this site's resources |

**Content Security Policy (CSP)** -- applied to public pages only (not `/admin/*` or `/livewire/*` because Filament requires inline styles/scripts):

| Directive | Value | Purpose |
|---|---|---|
| `default-src` | `'self'` | Only load resources from the same origin |
| `script-src` | `'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://www.googletagmanager.com` | Allow scripts from self and Google services |
| `style-src` | `'self' 'unsafe-inline' https://fonts.googleapis.com` | Allow styles from self and Google Fonts |
| `img-src` | `'self' data: https:` | Allow images from self, data URIs, and any HTTPS source |
| `font-src` | `'self' https://fonts.gstatic.com` | Allow fonts from self and Google Fonts CDN |
| `frame-src` | `'self' https://www.google.com` | Allow iframes from self and Google (reCAPTCHA) |
| `connect-src` | `'self' https://www.google-analytics.com https://www.google.com` | Allow AJAX/fetch to self and Google |
| `form-action` | `'self'` | Forms can only submit to the same origin |
| `base-uri` | `'self'` | Prevents base URI injection attacks |
| `object-src` | `'none'` | No plugins (Flash, Java applets) |
| `frame-ancestors` | `'self'` | Only this site can embed this page |
| `upgrade-insecure-requests` | (directive) | Automatically upgrades HTTP to HTTPS |

**Admin-specific headers:**
- `X-Robots-Tag: noindex, nofollow` -- tells search engines not to index admin pages
- `Cache-Control: no-store, no-cache, must-revalidate, private` -- prevents browsers/proxies from caching admin pages

### ThrottleAdminLogin Middleware (`app/Http/Middleware/ThrottleAdminLogin.php`)

> *This middleware only exists on the `master` branch. It is not present on `design-v2`.*

Rate limits admin login POST requests to 5 attempts per minute per IP address. If exceeded, returns a 429 "Too Many Requests" error with the number of seconds until the next attempt is allowed.

```php
$key = 'admin-login:' . $request->ip();
if ($this->limiter->tooManyAttempts($key, 5)) {
    $seconds = $this->limiter->availableIn($key);
    abort(429, "Too many login attempts. Please try again in {$seconds} seconds.");
}
$this->limiter->hit($key, 60);  // 60-second decay window
```

### General Rate Limiting

The Filament admin panel middleware stack includes `ThrottleRequests::class . ':60,1'`, which limits all admin panel requests to 60 per minute per IP.

### CSRF Protection

Laravel automatically generates a unique CSRF token for each user session. Every form must include `@csrf` (which outputs a hidden `<input>` field with the token). When a form is submitted via POST, Laravel validates the token. If it is missing or wrong, the request is rejected with a 419 error.

This prevents **Cross-Site Request Forgery** -- an attack where a malicious website submits a form to your site using the victim's logged-in session.

### SecurityAudit Artisan Command (`app/Console/Commands/SecurityAudit.php`)

> *This command only exists on the `master` branch. It is not present on `design-v2`.*

A custom CLI command that scans the `public/` directory for indicators of compromise.

**Usage:**
```bash
php artisan security:audit              # Scan only (report findings)
php artisan security:audit --fix        # Quarantine suspicious files
php artisan security:audit --notify     # Send email alert
```

**Checks performed:**

| Check | What It Detects |
|---|---|
| Unauthorized root files | Files in `public/` that are not in the allowlist (index.php, .htaccess, robots.txt, favicons) |
| Unauthorized directories | Directories in `public/` not in the allowlist (build, css, js, storage, fonts, images, vendor) |
| Dangerous file types | Files with executable extensions (.php, .sh, .exe, .asp, etc.) inside asset directories |
| PHP in non-PHP files | Embedded `<?php` or `<?=` tags inside .html, .css, .js, .svg files |
| .htaccess integrity | Suspicious .htaccess patterns (external redirects, PHP auto_prepend, handler overrides) and git hash comparison |
| Unauthorized symlinks | Symlinks in `public/` that are not the expected `storage -> ../storage/app/public` |
| File permissions | World-writable `public/` directory or group-writable `index.php` |
| Recently modified files | Files changed in the last 24 hours (excluding `public/build/`) |

**Quarantine:** When `--fix` is used, suspicious files are moved to `storage/quarantine/{timestamp}/` instead of being deleted. This preserves evidence for investigation.

**Scheduled scanning:** Can be scheduled in `routes/console.php`:
```php
Schedule::command('security:audit --fix --notify')->everySixHours();
```

### Admin Access Control (FilamentUser Interface)

> *This access control only exists on the `master` branch. On `design-v2`, the User model does not implement `FilamentUser`, and any authenticated user can access the admin panel.*

The `User` model implements `FilamentUser` with a `canAccessPanel()` method. This is Filament's built-in access control mechanism. Even if a user has valid credentials, they will get a 403 Forbidden error if their email is not in the `ADMIN_EMAILS` environment variable.

**To add a new admin:**
1. Add their email to `ADMIN_EMAILS` in `.env`: `ADMIN_EMAILS=admin@hilotec.com,newadmin@hilotec.com`
2. Create their user account (via seeder, tinker, or the password reset flow)

---

## 13. Key Patterns

### Published Scope Pattern

Most content models have an `is_published` boolean column and a `scopePublished()` method. This pattern ensures unpublished content never appears on the public website, even if a developer forgets to filter it.

```php
// In the model:
public function scopePublished(Builder $query): Builder
{
    return $query->where('is_published', true);
}

// In the controller:
$services = Service::published()->get();  // Only published services
$services = Service::all();               // ALL services (avoid on frontend!)
```

**Which models have it:** Service, Reference, TeamMember, Post, Page, Partner (6 out of 10).

**Special case -- Post:** The Post model's `scopePublished()` checks three conditions:
```php
return $query->where('is_published', true)
    ->whereNotNull('published_at')
    ->where('published_at', '<=', now());
```

### Ordered Scope Pattern

Content with a manual sort order has a `sort_order` integer column and a `scopeOrdered()` method.

```php
public function scopeOrdered(Builder $query): Builder
{
    return $query->orderBy('sort_order');
}

// Usage:
$services = Service::published()->ordered()->get();
```

**Which models have it:** Service, ReferenceCategory, Reference, TeamMember, Partner (5 out of 10).

### Slug-Based Lookup Pattern

Models with public detail pages use a `slug` column (URL-friendly string) for lookups instead of numeric IDs. This produces clean URLs like `/angebot/server-netzwerk` instead of `/angebot/3`.

```php
$service = Service::published()->where('slug', $slug)->firstOrFail();
```

The `firstOrFail()` method automatically returns a 404 if no matching record is found.

### Scope Chaining Pattern

Eloquent scopes can be chained together to build up queries incrementally:

```php
// Chain multiple scopes:
Service::published()->ordered()->get();

// With eager loading:
ReferenceCategory::ordered()
    ->with(['references' => fn ($q) => $q->published()->ordered()])
    ->get();
```

### Thin Controller Pattern

Controllers in this project are intentionally minimal. They contain no business logic -- just data fetching and view rendering. The typical controller method is 2-4 lines:

```php
public function index()
{
    $services = Service::published()->ordered()->get();
    return view('pages.services.index', compact('services'));
}
```

### German Slug Convention

All public URLs use German words to match the site's German content:
- `/angebot` (not `/services`)
- `/referenzen` (not `/references`)
- `/ueber-uns` (not `/about`)
- `/aktuelles` (not `/news`)
- `/kontakt` (not `/contact`)

### Component-Based Template Pattern

Templates are composed of reusable Blade components:

```blade
<x-layout title="Angebot">
    <x-hero heading="Unsere Leistungen" />
    <section class="py-16">
        <x-section-heading title="IT-Dienstleistungen" />
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($services as $service)
                <x-service-card :service="$service" />
            @endforeach
        </div>
    </section>
</x-layout>
```

---

## 14. File Upload & Storage

### How It Works

File uploads are handled by Filament's `FileUpload` form component. When a user uploads a file in the admin panel, here is what happens:

```
Admin uploads image in browser
     |
     v
Filament/Livewire receives the file via temporary upload
     |
     v
File is validated (must be an image, based on ->image() method)
     |
     v
File is saved to storage/app/public/{directory}/
  - team photos -> storage/app/public/team/
  - post images -> storage/app/public/posts/
  - partner logos -> storage/app/public/partners/
     |
     v
The relative path (e.g., "team/photo123.jpg") is stored in the database column
     |
     v
A symlink makes the file publicly accessible:
  public/storage/ -> storage/app/public/
     |
     v
In Blade templates, the file is displayed with:
  <img src="{{ asset('storage/' . $teamMember->photo) }}">
```

### Storage Disk Configuration

The default filesystem configuration (`config/filesystems.php`):

```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'public',
    ],
],
```

Filament uses the `public` disk by default. Files are stored at `storage/app/public/` and are served via the `public/storage` symlink.

### Setting Up the Symlink

The symlink must be created once:
```bash
php artisan storage:link
```

This creates: `public/storage` -> `storage/app/public`

Without this symlink, uploaded files will exist on disk but will not be accessible via URL.

### Upload Directories by Content Type

| Content Type | Filament Config | Storage Path | URL |
|---|---|---|---|
| Team member photos | `FileUpload::make('photo')->image()->directory('team')` | `storage/app/public/team/` | `/storage/team/{filename}` |
| Post featured images | `FileUpload::make('featured_image')->image()->directory('posts')` | `storage/app/public/posts/` | `/storage/posts/{filename}` |
| Partner logos | `FileUpload::make('logo')->image()->directory('partners')` | `storage/app/public/partners/` | `/storage/partners/{filename}` |

### Displaying Uploaded Images in Templates

```blade
{{-- Team member photo --}}
@if($teamMember->photo)
    <img src="{{ asset('storage/' . $teamMember->photo) }}" alt="{{ $teamMember->name }}">
@endif

{{-- Post featured image --}}
@if($post->featured_image)
    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
@endif
```

### Production Considerations

For production deployments:
- Ensure `storage/app/public/` is included in your backup strategy
- The `public/storage` symlink must exist on the production server
- File permissions: the web server user must be able to write to `storage/app/public/`
- For cloud hosting, consider switching to an S3-compatible storage driver (see [Deployment Guide](02-DEPLOYMENT.md))

---

## 15. How to Add New Content Types

This section walks through adding a completely new content type from scratch. As an example, we will add a "Job Posting" content type (Stellenangebote).

### Step 1: Create the Migration

Generate a migration file:
```bash
php artisan make:migration create_job_postings_table
```

This creates a file in `database/migrations/`. Edit it:

```php
Schema::create('job_postings', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->longText('body')->nullable();
    $table->string('location')->nullable();
    $table->string('employment_type')->nullable();    // e.g., "Vollzeit", "Teilzeit"
    $table->integer('sort_order')->default(0);
    $table->boolean('is_published')->default(true);
    $table->timestamps();
});
```

Run the migration:
```bash
php artisan migrate
```

### Step 2: Create the Model

Create `app/Models/JobPosting.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'body', 'location',
        'employment_type', 'sort_order', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
```

### Step 3: Create the Filament Resource

Generate the resource:
```bash
php artisan make:filament-resource JobPosting --generate
```

This creates:
- `app/Filament/Resources/JobPostingResource.php`
- `app/Filament/Resources/JobPostingResource/Pages/ListJobPostings.php`
- `app/Filament/Resources/JobPostingResource/Pages/CreateJobPosting.php`
- `app/Filament/Resources/JobPostingResource/Pages/EditJobPosting.php`

Edit `JobPostingResource.php` to match the project's conventions:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobPostingResource\Pages;
use App\Models\JobPosting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobPostingResource extends Resource
{
    protected static ?string $model = JobPosting::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Inhalte';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Stellenangebote';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')->rows(3),
            Forms\Components\RichEditor::make('body')->columnSpanFull(),
            Forms\Components\TextInput::make('location')->maxLength(255),
            Forms\Components\TextInput::make('employment_type')->maxLength(255),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobPostings::route('/'),
            'create' => Pages\CreateJobPosting::route('/create'),
            'edit' => Pages\EditJobPosting::route('/{record}/edit'),
        ];
    }
}
```

At this point, the admin panel already has a "Stellenangebote" section under "Inhalte" where you can create, edit, and delete job postings.

### Step 4: Create the Controller

Create `app/Http/Controllers/JobPostingController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;

class JobPostingController extends Controller
{
    public function index()
    {
        $jobPostings = JobPosting::published()->ordered()->get();

        return view('pages.jobs.index', compact('jobPostings'));
    }

    public function show(string $slug)
    {
        $jobPosting = JobPosting::published()->where('slug', $slug)->firstOrFail();

        return view('pages.jobs.show', compact('jobPosting'));
    }
}
```

### Step 5: Add Routes

Add to `routes/web.php` **before** the catch-all route:

```php
use App\Http\Controllers\JobPostingController;

// Add these BEFORE the /{slug} catch-all:
Route::get('/stellen', [JobPostingController::class, 'index'])->name('jobs.index');
Route::get('/stellen/{slug}', [JobPostingController::class, 'show'])->name('jobs.show');

// This must remain last:
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
```

### Step 6: Create Blade Templates

Create `resources/views/pages/jobs/index.blade.php`:

```blade
<x-layout title="Stellenangebote">
    <x-hero heading="Stellenangebote" subheading="Werden Sie Teil unseres Teams" />

    <section class="py-16">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($jobPostings as $job)
                    <a href="{{ route('jobs.show', $job->slug) }}"
                       class="block bg-hilotec-surface border border-white/5 rounded-xl p-6
                              hover:border-hilotec-gold/30 transition-all duration-300">
                        <h3 class="text-lg font-heading font-semibold text-white mb-2">
                            {{ $job->title }}
                        </h3>
                        @if($job->location)
                            <p class="text-hilotec-gray-dark text-sm mb-3">{{ $job->location }}</p>
                        @endif
                        <p class="text-hilotec-gray text-sm">{{ Str::limit($job->description, 150) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
```

Create `resources/views/pages/jobs/show.blade.php`:

```blade
<x-layout title="{{ $jobPosting->title }}">
    <x-hero heading="{{ $jobPosting->title }}" subheading="{{ $jobPosting->location }}" />

    <section class="py-16">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto prose prose-invert prose-gold">
                {!! $jobPosting->body !!}
            </div>
        </div>
    </section>
</x-layout>
```

### Step 7: Create a Seeder (Optional)

Create `database/seeders/JobPostingsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use Illuminate\Database\Seeder;

class JobPostingsSeeder extends Seeder
{
    public function run(): void
    {
        JobPosting::create([
            'title' => 'System Engineer',
            'slug' => 'system-engineer',
            'description' => 'Wir suchen einen erfahrenen System Engineer...',
            'body' => '<h2>Ihre Aufgaben</h2><ul><li>...</li></ul>',
            'location' => 'Baren, Schweiz',
            'employment_type' => 'Vollzeit',
            'sort_order' => 1,
            'is_published' => true,
        ]);
    }
}
```

Register it in `database/seeders/DatabaseSeeder.php`:

```php
$this->call([
    // ... existing seeders ...
    JobPostingsSeeder::class,
]);
```

### Step 8: Add Navigation Link (Optional)

To add the new page to the header and footer navigation, edit:

- `resources/views/components/header.blade.php` -- add to the `$navItems` array:
  ```php
  ['label' => 'Stellen', 'route' => 'jobs.index', 'url' => '/stellen'],
  ```

- `resources/views/components/footer.blade.php` -- add a link in the navigation column:
  ```blade
  <a href="/stellen" class="text-hilotec-gray text-sm hover:text-white transition-colors">Stellen</a>
  ```

### Summary Checklist

| Step | Files Created/Modified | Command |
|---|---|---|
| 1. Migration | `database/migrations/xxxx_create_job_postings_table.php` | `php artisan make:migration` |
| 2. Model | `app/Models/JobPosting.php` | Manual or `php artisan make:model` |
| 3. Filament Resource | `app/Filament/Resources/JobPostingResource.php` + Pages/ | `php artisan make:filament-resource` |
| 4. Controller | `app/Http/Controllers/JobPostingController.php` | Manual or `php artisan make:controller` |
| 5. Routes | `routes/web.php` (modified) | Manual edit |
| 6. Views | `resources/views/pages/jobs/index.blade.php`, `show.blade.php` | Manual |
| 7. Seeder | `database/seeders/JobPostingsSeeder.php`, `DatabaseSeeder.php` | Manual |
| 8. Navigation | `resources/views/components/header.blade.php`, `footer.blade.php` | Manual edit |

After completing all steps, run:
```bash
php artisan migrate                    # Apply the new migration
php artisan db:seed --class=JobPostingsSeeder  # Seed sample data (optional)
npm run build                          # Rebuild assets if you added new Tailwind classes
```

---

## Related Documentation

| Document | Contents |
|---|---|
| [00-GETTING-STARTED.md](00-GETTING-STARTED.md) | Quick start, prerequisites, first-time setup |
| [01-DEVELOPMENT-SETUP.md](01-DEVELOPMENT-SETUP.md) | Development environment, branch strategy, troubleshooting |
| [02-DEPLOYMENT.md](02-DEPLOYMENT.md) | Production deployment, server configuration, SSL |
| [03-ADMIN-GUIDE.md](03-ADMIN-GUIDE.md) | How to use the admin panel to manage content |
