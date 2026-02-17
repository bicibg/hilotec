# Branch Comparison: master vs. design-v2

> **Date:** 2026-02-17
> **Repository:** HILOTEC Corporate Website (Laravel 11 + Filament 3 + Tailwind CSS 4)
> **Audience:** Decision-makers evaluating which branch to deploy or how to merge them.

---

## 1. Branch Overview

### Branch Topology

```
* 7619f26 (master) Add security hardening: middleware, session, admin access, server configs
* cf6e345         Fix mobile UX issues
| * e56098d (design-v2) Fix mobile UX issues and polish animations
| * edb6072              Implement Alpine Precision design refresh (design-v2)
|/
* 80c9ac1 (common ancestor) Update hero bg, remove logo swoosh, fix Entdecken button
* a470a67                    Add comprehensive documentation
* aff2094                    Initial commit: HILOTEC corporate website
```

### master (current production candidate)

- **Purpose:** Security-hardened production branch.
- **Commits since divergence:** 2 (`cf6e345` mobile UX fixes, `7619f26` security hardening).
- **Design:** Full dark theme -- black/dark navy background on every page.
- **Security posture:** Comprehensive. Adds HTTP security headers, admin login throttling, brute-force protection, file integrity auditing, hardened `.htaccess`, session hardening, HTTPS enforcement, Nginx/PHP hardening reference configs, and a deployment script.
- **When to use:** When you need a secure, production-ready deployment immediately and are satisfied with the dark-only visual design.

### design-v2 (design refresh candidate)

- **Purpose:** Visual redesign branch ("Alpine Precision" theme).
- **Commits since divergence:** 2 (`edb6072` design refresh, `e56098d` mobile UX + animation polish).
- **Design:** Hybrid light/dark theme -- light content sections with white cards, dark hero/footer/CTA sections. Scroll-reveal animations, glassmorphism effects, animated counters, gold accent bars.
- **Security posture:** None. All security hardening from master is absent.
- **When to use:** For design review, staging, or as a starting point after merging security hardening from master.

---

## 2. Visual Comparison

| Aspect | master | design-v2 |
|---|---|---|
| **Body background** | `#0a0a0a` (near-black) | `#FAFAFA` (off-white) |
| **Content section backgrounds** | `bg-hilotec-dark` / `bg-hilotec-surface` (dark) | `bg-hilotec-light` / `bg-hilotec-light-alt` (light) |
| **Card style** | Dark surface (`bg-hilotec-surface`) with subtle white borders (`border-white/5`) | White cards (`bg-white`) with soft box shadows (`card-elevated`) |
| **Text colors** | White/gray on dark | Dark text (`#1F2937`) on light, white on dark hero/footer |
| **Hero overlay** | Single color overlay (`bg-hilotec-dark/50`) | Gradient overlay (`from-hilotec-dark/70 via-hilotec-dark/50 to-hilotec-dark/80`) + grid pattern + SVG wave divider |
| **Hero CTA button** | Blue variant | Gold variant with glow effect |
| **Hero badge** | Not present | Gold pill badge above heading (e.g., "IT-Komplettbetreuung fur KMU") |
| **Section headings** | White text, no label | Dark text with optional gold pill label above |
| **Scroll indicator** | Bouncing arrow | Mouse-wheel animation (border pill with bouncing dot) |
| **Prose (rich text)** | `prose-invert` (light text on dark) | `prose-light` (dark text on light, gold bullets/quote borders) |
| **Service cards** | Dark surface, white text, blue "Mehr erfahren" link | White elevated card, gold left accent bar, amber icon circle, hover lift |
| **Post cards** | Dark surface, hover gold title | White elevated card, hover blue title, hover lift |
| **Contact form** | Dark inputs (`bg-hilotec-surface`, `border-white/10`) | Light inputs (`bg-hilotec-light`, `border-hilotec-border`), gold focus ring |
| **Footer CTA** | Yellow card with background image | Dark gradient with glassmorphic card, dot pattern, SVG wave transition |
| **Footer** | Standard dark footer | Gold gradient top border, circular social icons, back-to-top button |
| **Header** | Dark background on scroll (`bg-hilotec-dark/95`) | Glassmorphism on scroll (`glass` class), gold underline on active nav |
| **Active nav indicator** | Gold dot below item | Gold bar (full-width underline) below item |
| **Mobile nav active** | Gold text only | Gold text + gold left border |
| **Button style** | `rounded-lg`, no scale effect | `rounded-xl`, `hover:scale-[1.02]` + `active:scale-[0.98]`, glow effects |
| **Scroll animations** | None | CSS `.reveal` + IntersectionObserver, staggered delays (`.stagger-1` through `.stagger-8`) |
| **Animated counters** | None | Stats band on home page with Alpine.js `animatedCounter` component |
| **Filter pills (references)** | `rounded-lg`, gold active state | `rounded-full`, dark active state |
| **404 page** | Dark background | Light background |

---

## 3. File-by-file Comparison

### Files Only on master (deleted in design-v2)

| File | Purpose |
|---|---|
| `app/Http/Middleware/SecurityHeaders.php` | HTTP security headers (CSP, HSTS, X-Frame-Options, etc.) |
| `app/Http/Middleware/ThrottleAdminLogin.php` | Rate-limits admin login to 5 POST attempts/min/IP |
| `app/Console/Commands/SecurityAudit.php` | Scans `public/` for unauthorized files, injected PHP, tampered `.htaccess` |
| `deploy.sh` | Hardened deployment script (git clean, permission lockdown, security audit) |
| `php-hardening.ini` | PHP ini overrides: disable dangerous functions, session hardening, error hiding |
| `nginx-security.conf` | Nginx security rules: block PHP in asset dirs, hide server info, rate limiting |
| `.env.production.example` | Production environment variable reference with security settings |

### Files Only on design-v2

| File | Purpose |
|---|---|
| `docs/HILOTEC_Website_Project_Analysis.md` | Project analysis document (579 lines) |

### Files That Differ Between Branches

| File | Nature of Change |
|---|---|
| `bootstrap/app.php` | master appends `SecurityHeaders` middleware; design-v2 has empty middleware closure |
| `config/session.php` | master: `encrypt=true`, `secure=true`, `same_site=strict`; design-v2: Laravel defaults |
| `app/Models/User.php` | master implements `FilamentUser` with `canAccessPanel()` email whitelist; design-v2 does not |
| `app/Providers/AppServiceProvider.php` | master forces HTTPS in production; design-v2 has empty `boot()` |
| `app/Providers/Filament/AdminPanelProvider.php` | master adds `passwordReset()` and `ThrottleRequests::class` middleware; design-v2 does not |
| `routes/console.php` | master schedules `security:audit` every 6 hours; design-v2 has default only |
| `public/.htaccess` | master has 78 lines of security rules; design-v2 has basic Laravel default |
| `package.json` | design-v2 adds `@alpinejs/intersect` dependency |
| `resources/css/app.css` | Major differences (see Section 4) |
| `resources/js/app.js` | design-v2 adds IntersectionObserver, Alpine intersect plugin, animated counter |
| `resources/views/components/button.blade.php` | New `outline-dark` variant, `rounded-xl`, scale/glow effects |
| `resources/views/components/footer-cta.blade.php` | Redesigned: dark gradient + glass card replaces gold card + background image |
| `resources/views/components/footer.blade.php` | Gold gradient border, circular social icons, back-to-top button, color adjustments |
| `resources/views/components/header.blade.php` | Glassmorphism on scroll, gold underline active indicator, mobile active border |
| `resources/views/components/hero.blade.php` | Badge prop, gradient overlay, grid pattern, SVG wave divider, scroll reveal classes |
| `resources/views/components/layout.blade.php` | Removes `fullHero` prop |
| `resources/views/components/post-card.blade.php` | White card, light text, hover lift |
| `resources/views/components/reference-item.blade.php` | Light-background color classes |
| `resources/views/components/section-heading.blade.php` | `light` default flipped to `false`, new `label` prop |
| `resources/views/components/service-card.blade.php` | White card, gold accent bar, amber icon wrapper |
| `resources/views/errors/404.blade.php` | Light background, dark text |
| `resources/views/pages/about.blade.php` | Light backgrounds, `prose-light`, card-elevated team cards |
| `resources/views/pages/contact.blade.php` | Light inputs, white card form, amber icon wrappers |
| `resources/views/pages/generic.blade.php` | Light background, `prose-light` |
| `resources/views/pages/home.blade.php` | Light sections, stats band with animated counters, decorative accents |
| `resources/views/pages/posts/index.blade.php` | Light background |
| `resources/views/pages/posts/show.blade.php` | Light background, `prose-light`, `outline-dark` button |
| `resources/views/pages/references.blade.php` | Light background, rounded-full filter pills, white category cards |
| `resources/views/pages/services/index.blade.php` | Light background |
| `resources/views/pages/services/show.blade.php` | Light background, `prose-light`, white sidebar card |

---

## 4. CSS/Theme Differences

### Color Token Comparison

| Token | master | design-v2 | Notes |
|---|---|---|---|
| `--color-hilotec-dark` | `#0a0a0a` | `#0C1222` | Shifted from pure black to navy-black |
| `--color-hilotec-darker` | `#050505` | `#080E1A` | Same shift |
| `--color-hilotec-surface` | `#111318` | `#111827` | Slightly warmer |
| `--color-hilotec-light` | -- | `#FAFAFA` | **New** -- primary light background |
| `--color-hilotec-light-alt` | -- | `#F3F4F6` | **New** -- alternate light background |
| `--color-hilotec-white` | -- | `#FFFFFF` | **New** -- pure white |
| `--color-hilotec-blue-light` | -- | `#3b82f6` | **New** -- lighter blue |
| `--color-hilotec-text` | -- | `#1F2937` | **New** -- primary dark text |
| `--color-hilotec-text-light` | -- | `#6B7280` | **New** -- secondary text |
| `--color-hilotec-text-muted` | -- | `#9CA3AF` | **New** -- muted text |
| `--color-hilotec-border` | -- | `#E5E7EB` | **New** -- light border |
| `--color-hilotec-border-dark` | -- | `#1E293B` | **New** -- dark border |
| Gold tokens | Identical | Identical | No change |

### Body Default

```css
/* master */
body { @apply font-body bg-hilotec-dark text-white antialiased; }

/* design-v2 */
body { @apply font-body bg-hilotec-light text-hilotec-text antialiased; }
```

### New CSS Utility Classes (design-v2 only)

| Class | Description |
|---|---|
| `.glass` | Glassmorphism: `rgba(12,18,34,0.85)` background + 12px blur |
| `.glass-light` | Light glassmorphism: `rgba(255,255,255,0.8)` background + 12px blur |
| `.reveal` | Scroll-reveal animation: starts `opacity:0; translateY(16px)`, transitions to visible |
| `.reveal.revealed` | Active state: `opacity:1; translateY(0)` with 0.7s ease-out |
| `.stagger-1` to `.stagger-8` | Transition delays from 0.1s to 0.8s for sequential reveal |
| `.gold-bar-left` | 4px gold gradient bar on the left edge via `::before` pseudo-element |
| `.glow-blue` | Blue box-shadow glow (24px spread), intensifies on hover |
| `.glow-gold` | Gold box-shadow glow (24px spread), intensifies on hover |
| `.card-elevated` | Soft box shadow, intensifies on hover |

### Prose Configuration (design-v2 only)

```css
.prose-light {
    --tw-prose-body: #374151;
    --tw-prose-headings: #111827;
    --tw-prose-links: #2563eb;
    --tw-prose-bold: #111827;
    --tw-prose-bullets: #d4a843;      /* Gold bullets */
    --tw-prose-quote-borders: #d4a843; /* Gold quote borders */
    --tw-prose-pre-bg: #0C1222;        /* Dark code blocks on light pages */
}
```

### JavaScript Additions (design-v2 only)

- **`@alpinejs/intersect` plugin** -- new npm dependency, registered in `app.js`.
- **IntersectionObserver scroll reveal** -- observes all `.reveal` elements, adds `.revealed` class at 10% visibility with a -40px bottom margin.
- **`animatedCounter` Alpine component** -- ease-out cubic counter animation used in the homepage stats band (duration default 2000ms).

---

## 5. Security Differences (Critical)

This section is the most important for a deployment decision. The master branch contains comprehensive security hardening that is completely absent from design-v2.

### Security Features Present ONLY on master

| Feature | File(s) | Impact |
|---|---|---|
| **HTTP Security Headers** | `app/Http/Middleware/SecurityHeaders.php`, `bootstrap/app.php` | Adds X-Frame-Options, X-Content-Type-Options, HSTS, CSP, Permissions-Policy, Referrer-Policy, Cross-Origin-Opener-Policy to all responses. CSP excludes admin/Livewire routes for Filament compatibility. |
| **Admin Login Throttling** | `app/Http/Middleware/ThrottleAdminLogin.php` | Limits admin login POST requests to 5 attempts per minute per IP. Prevents brute-force attacks. |
| **Filament Admin Rate Limiting** | `app/Providers/Filament/AdminPanelProvider.php` | Adds `ThrottleRequests::class . ':60,1'` middleware to all Filament admin panel routes. |
| **Admin Access Whitelist** | `app/Models/User.php` | Implements `FilamentUser` interface with `canAccessPanel()` that checks `ADMIN_EMAILS` env variable. Only whitelisted emails can log into the admin panel. |
| **Password Reset in Admin** | `app/Providers/Filament/AdminPanelProvider.php` | Enables Filament's `->passwordReset()` feature. |
| **Session Hardening** | `config/session.php` | `encrypt=true` (encrypts session data), `secure=true` (cookies sent only over HTTPS), `same_site=strict` (prevents CSRF via cross-site requests). |
| **HTTPS Enforcement** | `app/Providers/AppServiceProvider.php` | `URL::forceScheme('https')` in production ensures all generated URLs use HTTPS. |
| **Hardened .htaccess** | `public/.htaccess` | Forces HTTPS, blocks hidden files, blocks PHP execution in asset directories, blocks dangerous file extensions, blocks sensitive file types, blocks exploit URL patterns, blocks CMS vulnerability scanners, disables directory browsing, hides server version. |
| **Security Audit Command** | `app/Console/Commands/SecurityAudit.php`, `routes/console.php` | Artisan command (`php artisan security:audit`) that scans public directory for unauthorized files, injected PHP, tampered `.htaccess`, suspicious symlinks, world-writable permissions, recently modified files. Supports `--fix` (quarantine) and `--notify` (email alert). Scheduled every 6 hours. |
| **Deployment Script** | `deploy.sh` | Hardened deployment: cleans unauthorized files from public/, removes injected PHP from asset directories, removes injected `.htaccess` files, runs security audit, locks file permissions (644 files, 755 dirs, 640 `.env`). |
| **PHP Hardening Config** | `php-hardening.ini` | Reference config: disables dangerous functions (`exec`, `shell_exec`, `system`, etc.), hides PHP version, disables error display, enforces session security at PHP level, sets resource limits. |
| **Nginx Security Config** | `nginx-security.conf` | Reference config: blocks hidden files, blocks PHP execution in asset dirs, blocks dangerous file types, blocks CMS scanners, blocks exploit patterns in query strings, blocks vulnerability scanner user agents, security headers backup, request limits. |
| **Production .env Reference** | `.env.production.example` | Documents minimum security-related environment variables for production. |

### Risk Assessment

**Deploying design-v2 without merging security from master would expose the site to:**

1. **Clickjacking** -- no X-Frame-Options header.
2. **XSS via MIME sniffing** -- no X-Content-Type-Options header.
3. **Protocol downgrade attacks** -- no HSTS header, no HTTPS enforcement.
4. **Admin brute-force attacks** -- no login throttling, no rate limiting.
5. **Unauthorized admin access** -- any user with valid credentials can access the admin panel (no email whitelist).
6. **PHP shell injection** -- no `.htaccess` rules blocking PHP execution in `images/`, `css/`, `js/` directories.
7. **Information disclosure** -- directory browsing enabled, server version visible, sensitive files accessible.
8. **Session hijacking** -- cookies not restricted to HTTPS, session data not encrypted, SameSite set to `lax` instead of `strict`.
9. **No file integrity monitoring** -- no scheduled security audits.

---

## 6. Shared Backend

The following backend files are **identical** between both branches (confirmed via `git diff`):

| Category | Files |
|---|---|
| **Controllers** | All files in `app/Http/Controllers/` |
| **Models** (except User) | `app/Models/Post.php`, `Service.php`, `Reference.php`, `ReferenceCategory.php`, `Page.php`, `TeamMember.php`, `Setting.php`, `SettingGroup.php` |
| **Routes** | `routes/web.php` |
| **Migrations** | All files in `database/migrations/` |
| **Seeders** | All files in `database/seeders/` |
| **Filament Resources** | All files in `app/Filament/Resources/` and `app/Filament/Pages/` |
| **Config** (except session) | All config files other than `config/session.php` |

The only backend differences are security-related: `User.php` (FilamentUser interface), `AppServiceProvider.php` (HTTPS enforcement), `AdminPanelProvider.php` (password reset + throttling), `bootstrap/app.php` (SecurityHeaders middleware), `config/session.php` (hardening), and `routes/console.php` (security audit schedule).

---

## 7. Merging Strategy

There are two viable approaches:

### Option A: Merge security hardening INTO design-v2

**Direction:** Cherry-pick or merge master's security changes into design-v2.

- Pros: design-v2 becomes the single production-ready branch with both the new design and security.
- Cons: Requires careful conflict resolution in `bootstrap/app.php`, `config/session.php`, `app/Models/User.php`, `app/Providers/`, `routes/console.php`, and `public/.htaccess`.
- Risk: Low. Security files are additive -- they do not modify design-related code.

### Option B: Merge design changes INTO master

**Direction:** Merge design-v2's visual changes into master.

- Pros: Master already has security; you only add design files on top.
- Cons: More files to merge (all CSS, JS, and 20+ Blade templates). The merge will have conflicts wherever master's `cf6e345` (mobile UX fixes) touched the same Blade templates as design-v2's `edb6072`.
- Risk: Medium. More conflict surface area. Requires careful visual testing of every page after merge.

### Option C: Rebase design-v2 on top of master

**Direction:** Replay design-v2's 2 commits on top of master's HEAD.

- Pros: Clean linear history. design-v2's commits are applied after security hardening.
- Cons: Same conflict resolution as Option B, but rewrites design-v2's commit history.
- Risk: Medium. Same conflict surface as Option B.

---

## 8. Recommended Approach

**Recommendation: Option A -- Merge master's security hardening into design-v2.**

Rationale:

1. **Minimal conflict surface.** Master's security commit (`7619f26`) adds entirely new files (middleware, commands, configs) and makes small, isolated changes to backend files. These do not touch any CSS, JS, or Blade templates -- so there are zero conflicts with design-v2's visual changes.

2. **The one conflict file is trivial.** The `cf6e345` (mobile UX fixes) commit on master touches Blade templates that design-v2 also modified. However, when merging master INTO design-v2, design-v2's versions of these templates are the ones you want to keep (they already include their own mobile UX fixes in `e56098d`).

3. **design-v2 becomes the complete production candidate.** After merging, design-v2 has both the new design and all security hardening. You can then promote it to master or deploy it directly.

4. **Security files are purely additive.** The middleware, commands, and config files from master are new files that design-v2 simply does not have. Merging them in requires no modification to the design code.

---

## 9. Step-by-step Merge Guide

### Prerequisites

```bash
cd /home/bugra/Projects/hilotec
git status              # Ensure clean working tree
git stash               # Stash any uncommitted changes if needed
```

### Step 1: Create a merge branch (safety net)

```bash
git checkout design-v2
git checkout -b design-v2-with-security
```

### Step 2: Merge master into the new branch

```bash
git merge master
```

### Step 3: Resolve expected conflicts

The merge will likely produce conflicts in files modified by both branches. For each conflicted file, the resolution strategy is:

| File | Resolution |
|---|---|
| `bootstrap/app.php` | **Keep master's SecurityHeaders middleware registration.** Accept the line: `$middleware->append(\App\Http\Middleware\SecurityHeaders::class);` |
| `config/session.php` | **Keep master's hardened values.** Accept: `encrypt => true`, `secure => true`, `same_site => strict`. |
| `app/Models/User.php` | **Keep master's version.** Accept the `FilamentUser` interface implementation and `canAccessPanel()` method. |
| `app/Providers/AppServiceProvider.php` | **Keep master's version.** Accept the `URL::forceScheme('https')` in production. |
| `app/Providers/Filament/AdminPanelProvider.php` | **Keep master's version.** Accept `->passwordReset()` and `ThrottleRequests` middleware. |
| `routes/console.php` | **Keep master's version.** Accept the `Schedule::command('security:audit')` line. |
| `public/.htaccess` | **Keep master's version entirely.** The security rules are critical. |
| Blade template files | **Keep design-v2's versions.** These contain the new visual design. Master's `cf6e345` mobile UX fixes are superseded by design-v2's `e56098d`. |

```bash
# After resolving each conflict:
git add <resolved-file>

# When all conflicts are resolved:
git commit
```

### Step 4: Verify security files are present

```bash
# All of these should exist:
ls app/Http/Middleware/SecurityHeaders.php
ls app/Http/Middleware/ThrottleAdminLogin.php
ls app/Console/Commands/SecurityAudit.php
ls deploy.sh
ls php-hardening.ini
ls nginx-security.conf
ls .env.production.example

# Verify SecurityHeaders is registered:
grep -n "SecurityHeaders" bootstrap/app.php

# Verify session hardening:
grep -n "encrypt" config/session.php
grep -n "secure" config/session.php
grep -n "same_site" config/session.php

# Verify FilamentUser is implemented:
grep -n "FilamentUser" app/Models/User.php
grep -n "canAccessPanel" app/Models/User.php

# Verify admin throttling:
grep -n "ThrottleRequests" app/Providers/Filament/AdminPanelProvider.php

# Verify HTTPS enforcement:
grep -n "forceScheme" app/Providers/AppServiceProvider.php

# Verify security audit schedule:
grep -n "security:audit" routes/console.php

# Verify .htaccess has security rules (should be ~90+ lines):
wc -l public/.htaccess
```

### Step 5: Verify design files are intact

```bash
# Verify new CSS tokens exist:
grep "hilotec-light" resources/css/app.css
grep "hilotec-text" resources/css/app.css
grep "card-elevated" resources/css/app.css
grep "\.reveal" resources/css/app.css

# Verify JS animations exist:
grep "IntersectionObserver" resources/js/app.js
grep "animatedCounter" resources/js/app.js

# Verify Alpine intersect plugin:
grep "intersect" package.json

# Verify light-background sections:
grep "bg-hilotec-light" resources/views/pages/home.blade.php
```

### Step 6: Build and test

```bash
npm install              # Install @alpinejs/intersect
npm run build            # Rebuild frontend assets
php artisan serve        # Start dev server

# Open http://localhost:8000 and visually verify:
# - Light content sections with white cards
# - Scroll animations trigger on scroll
# - Homepage stats band animates counters
# - Hero has gradient overlay, badge, wave divider
# - Footer has gold gradient border, back-to-top button
```

### Step 7: Test security

```bash
# Test security headers (run dev server first):
curl -sI http://localhost:8000 | grep -iE "(x-frame|x-content|referrer|permissions)"

# Test security audit:
php artisan security:audit

# Test admin access:
php artisan tinker
# > App\Models\User::first()->canAccessPanel(null); // Should check ADMIN_EMAILS
```

### Step 8: Promote to master (when satisfied)

```bash
git checkout master
git merge design-v2-with-security

# Or, if you prefer to replace master entirely:
git checkout master
git reset --hard design-v2-with-security
```

---

## 10. Post-merge Verification Checklist

### Security Verification

- [ ] `SecurityHeaders` middleware is registered in `bootstrap/app.php`
- [ ] `curl -sI <url>` returns `X-Frame-Options: SAMEORIGIN`
- [ ] `curl -sI <url>` returns `X-Content-Type-Options: nosniff`
- [ ] `curl -sI <url>` returns `Content-Security-Policy` (on non-admin pages)
- [ ] `curl -sI <url>` returns `Permissions-Policy`
- [ ] `ThrottleAdminLogin` middleware file exists
- [ ] `ThrottleRequests` middleware is listed in `AdminPanelProvider.php`
- [ ] `User` model implements `FilamentUser` and has `canAccessPanel()`
- [ ] `ADMIN_EMAILS` is set in `.env` and only listed emails can access `/admin`
- [ ] `config/session.php` has `encrypt => true`, `secure => true`, `same_site => strict`
- [ ] `AppServiceProvider` forces HTTPS in production
- [ ] `public/.htaccess` contains security rules (HTTPS redirect, block hidden files, block PHP in assets, block exploit patterns, block scanners)
- [ ] `php artisan security:audit` runs without errors
- [ ] `routes/console.php` schedules security audit every 6 hours
- [ ] `deploy.sh` exists and is executable
- [ ] `php-hardening.ini` exists for server deployment reference
- [ ] `nginx-security.conf` exists for server deployment reference
- [ ] `.env.production.example` exists
- [ ] `AdminPanelProvider` includes `->passwordReset()`

### Design Verification

- [ ] Homepage: light content sections, dark hero with badge and wave divider
- [ ] Homepage: stats band renders with animated counters
- [ ] Homepage: scroll-reveal animations trigger on scroll
- [ ] Services page: white elevated cards with gold accent bars
- [ ] Service detail: light background, `prose-light` rendering, white sidebar card
- [ ] References page: light background, rounded filter pills, white category cards
- [ ] About page: light background, white team member cards with gold ring accent
- [ ] Contact page: white form card with light inputs and gold focus rings
- [ ] Posts index: white post cards with hover lift effect
- [ ] Post detail: light background, `prose-light` rendering
- [ ] Generic pages (Impressum, Datenschutz): light background, `prose-light`
- [ ] 404 page: light background, dark text
- [ ] Header: glassmorphism on scroll, gold underline on active nav item
- [ ] Mobile header: gold left border on active item
- [ ] Footer CTA: dark gradient with glass card, SVG wave transition at top
- [ ] Footer: gold gradient top border, circular social icons, back-to-top button
- [ ] Buttons: rounded-xl, scale animation on hover/active, glow effects
- [ ] Hero scroll indicator: mouse-wheel animation (not bouncing arrow)
- [ ] All pages load without console errors
- [ ] `npm run build` completes without errors

### Backend Verification

- [ ] `php artisan migrate` runs with no pending migrations
- [ ] Admin panel (`/admin`) is accessible with correct credentials
- [ ] All Filament resources load (Services, Posts, References, Pages, Team Members, Settings)
- [ ] Contact form submission works
- [ ] All public routes resolve correctly (`/`, `/angebot`, `/referenzen`, `/ueber-uns`, `/aktuelles`, `/kontakt`)

---

## Summary

| Dimension | master | design-v2 | Merged (recommended) |
|---|---|---|---|
| Design | Dark-only | Light/dark hybrid | Light/dark hybrid |
| Animations | None | Scroll reveal, counters | Scroll reveal, counters |
| Security headers | Yes | No | Yes |
| Admin throttling | Yes | No | Yes |
| Admin email whitelist | Yes | No | Yes |
| Session hardening | Yes | No | Yes |
| .htaccess hardening | Yes | No | Yes |
| HTTPS enforcement | Yes | No | Yes |
| Security audit | Yes | No | Yes |
| Server configs | Yes | No | Yes |
| Deploy script | Yes | No | Yes |
| **Production-ready** | **Yes** | **No** | **Yes** |
