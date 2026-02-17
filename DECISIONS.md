# HILOTEC Website — Decision Log

## Tailwind CSS Version
**Decision:** Use Tailwind CSS 4 (shipped with Laravel 11) instead of v3
**Alternatives considered:** Downgrade to Tailwind CSS 3
**Rationale:** Laravel 11's fresh install ships with Tailwind CSS 4 and the `@tailwindcss/vite` plugin. Downgrading would fight the build system. The design tokens are equivalent — just defined in CSS `@theme` block instead of `tailwind.config.js`.

## Database
**Decision:** SQLite for local development
**Alternatives considered:** MySQL, PostgreSQL
**Rationale:** Per project requirements. Zero setup needed, works out of the box.

## Admin Panel
**Decision:** Filament 3 for CMS
**Alternatives considered:** Nova, custom admin, Backpack
**Rationale:** Per project requirements. Free, Laravel-native, rich feature set.

## Font Loading
**Decision:** Google Fonts via `<link>` tags (CDN)
**Alternatives considered:** Self-hosted fonts, @font-face
**Rationale:** Simplest setup for development. Can be switched to self-hosted later for GDPR compliance if needed.

## Contact Form
**Decision:** Log contact form submissions to database (ContactSubmission model)
**Alternatives considered:** Direct email only, third-party form service
**Rationale:** Logging to DB ensures no submissions are lost even without mail config. Mail sending can be added later. Submissions are viewable in the admin panel.

## Settings Architecture
**Decision:** Key-value settings table with group.key dot notation and cache
**Alternatives considered:** Config files, JSON blob, dedicated columns
**Rationale:** Flexible key-value pairs allow easy addition of new settings via admin. Cache layer (60min TTL) prevents repeated DB queries. `setting()` global helper makes usage in Blade templates clean.

## Filament Settings Page
**Decision:** Custom Filament Page with tabbed form instead of a Filament Resource
**Alternatives considered:** Filament Resource for Settings model, spatie/laravel-settings
**Rationale:** Settings are a flat key-value store with grouped tabs, not a CRUD resource. A custom page with tabs (Allgemein, Kontakt, Footer, Social) provides the best UX. The `__` separator (e.g., `contact__email`) maps cleanly to the `group.key` dot notation in the database.

## Reference Websites
**Decision:** Store website as domain string (e.g., "bauhandwerkag.ch") and prepend https:// in templates
**Alternatives considered:** Store full URLs
**Rationale:** Source data uses bare domains. Prepending protocol in templates keeps data clean and consistent.

## Service Detail Pages
**Decision:** Include a sidebar with all services for easy navigation
**Alternatives considered:** Breadcrumbs only, no sidebar
**Rationale:** Sidebar improves navigation between services without returning to the listing page. Current service is highlighted with gold color.

## 404 Page
**Decision:** Use the main layout component for the 404 page
**Alternatives considered:** Standalone HTML page
**Rationale:** Consistent branding and navigation even on error pages. Users can navigate back via header or the CTA button.
