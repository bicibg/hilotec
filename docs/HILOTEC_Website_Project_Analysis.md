# HILOTEC Website Rebuild — Complete Project Analysis

## 1. Project Overview

**Client:** HILOTEC Engineering + Consulting AG  
**Location:** Untere Hohle Gasse 5, CH-3550 Langnau im Emmental  
**Business:** IT services for SMEs (KMU) since 1995  
**Stack:** Laravel 11 + Filament Admin Panel + Tailwind CSS + Alpine.js  
**Goal:** Pixel-accurate rebuild of the new design (dark theme, modern tech aesthetic) with CMS-managed content via Filament.

---

## 2. Design Analysis (from uploaded mockups)

### 2.1 Color Palette

| Token | Hex | Usage |
|-------|-----|-------|
| `--bg-primary` | `#0a0a0a` / `#0d0f11` | Page background (near-black) |
| `--bg-footer` | `#0a0a0a` | Footer background |
| `--accent-gold` | `#d4a843` / `#c99a2e` | Logo swoosh, active nav indicator, CTA button borders, "Home" highlight |
| `--accent-blue` | `#2563eb` | Alternate CTA variant (blue button from annotated mockup) |
| `--text-primary` | `#ffffff` | Headings, body text |
| `--text-secondary` | `#9ca3af` | Subheadings, muted text |
| `--cta-bg` | `#d4a843` | "Kontakt aufnehmen" button (yellow/gold variant) |
| `--cta-text` | `#000000` | Button text on gold background |
| `--cta-blue-bg` | `#2563eb` | Blue CTA variant |
| `--cta-blue-text` | `#ffffff` | White text on blue CTA |
| `--card-bg` | `#d4a843` | Yellow card (footer CTA section) |
| `--card-text` | `#1a1a2e` | Dark text on yellow card |

### 2.2 Typography

| Element | Font | Weight | Size (approx.) |
|---------|------|--------|-----------------|
| Logo "HILOTEC" | Custom/Sans bold | 700 | ~28px |
| Logo subtitle | Sans | 400 | ~14px |
| Navigation | Sans-serif (likely system or custom) | 400 | ~16px |
| Hero heading | Sans-serif italic bold | 700 italic | ~56-64px |
| Hero subtitle | Sans-serif | 400 | ~18-20px |
| Section headings | Sans-serif bold | 700 | ~36-42px |
| Body text | Sans-serif | 400 | ~16px |
| Footer headings | Sans-serif bold | 600 | ~16px, gold colored |
| Footer links | Sans-serif | 400 | ~14px |

**Note from annotated mockup (Image 3):** Yellow color must match the logo swoosh color exactly. Where contrast in yellow textboxes is too weak, use black text instead.

### 2.3 Layout Structure

**Header:** Fixed/sticky. Logo left-aligned (swoosh extends from off-screen left). Nav right-aligned: Home · Angebot · Referenzen · Über uns · Aktuelles · Kontakt. Active page indicated by gold text + small gold dot below.

**Hero Section (Home):** Full-viewport height. Dark background with tech visualization (circuit board / data streams in blue-white). Left-aligned content: italic bold heading, subtitle, CTA button. "Entdecken" scroll indicator at bottom.

**Hero Section (Über uns):** Full-viewport with landscape photo of Emmental hills/forest at sunset. Centered text: "Ihr Partner aus Langnau im Emmental", subtitle, "Zur Webcam" CTA (gold outline button).

**Footer CTA Block:** Dark background with matrix rain animation (gold/teal vertical lines with dots). Yellow card with dark text: "Sie haben ein Problem oder eine Frage? Wir bieten Ihnen die Lösung, die Sie suchen." Blue CTA button.

**Footer:** 4-column layout on dark background:
1. Logo
2. Fernwartung (TeamViewer link + button)
3. Navigation links
4. Anschrift (address)
5. Kontakt (phone numbers, email)

Copyright bar at bottom with separator line.

### 2.4 Key Design Annotations (from Image 3)

- Yellow color everywhere must match logo swoosh
- Where contrast on yellow backgrounds is weak → use black text
- Logo: the gold swoosh "is pulled from outside the page" (extends off-left)
- Logo text is left-aligned with page content
- CTA Variant: blue textbox with white text as alternative to yellow/gold

---

## 3. Site Map & Pages

Based on the new design nav and current website content:

| # | Page | Route | Description |
|---|------|-------|-------------|
| 1 | **Home** | `/` | Hero + services overview + about teaser + CTA |
| 2 | **Angebot** | `/angebot` | Services/offerings (maps to old "Informatik/Datensysteme") |
| 3 | **Referenzen** | `/referenzen` | Client references grouped by industry |
| 4 | **Über uns** | `/ueber-uns` | About page with Emmental hero image |
| 5 | **Aktuelles** | `/aktuelles` | News/blog posts |
| 6 | **Kontakt** | `/kontakt` | Contact form + info + map |
| 7 | **Impressum** | `/impressum` | Legal notice |
| 8 | **Datenschutz** | `/datenschutz` | Privacy policy (DSGVO/Swiss compliance) |

---

## 4. Database Schema & Models

### 4.1 Pages (for generic CMS pages)

```
pages
├── id
├── title
├── slug (unique)
├── hero_heading (nullable)
├── hero_subheading (nullable)
├── hero_image (nullable, file path)
├── hero_cta_text (nullable)
├── hero_cta_url (nullable)
├── body (rich text / HTML)
├── meta_title (nullable)
├── meta_description (nullable)
├── is_published (boolean)
├── sort_order (integer)
├── timestamps
```

### 4.2 Services (Angebot)

```
services
├── id
├── title
├── slug
├── icon (nullable, e.g. SVG icon name)
├── short_description (text)
├── body (rich text)
├── image (nullable)
├── sort_order
├── is_published (boolean)
├── timestamps
```

### 4.3 Reference Categories

```
reference_categories
├── id
├── name (e.g. "Baugewerbe", "Gesundheitswesen")
├── slug
├── sort_order
├── timestamps
```

### 4.4 References

```
references
├── id
├── reference_category_id (FK)
├── company_name
├── address (nullable, text)
├── description (text — what was delivered)
├── website_url (nullable)
├── sort_order
├── is_published (boolean)
├── timestamps
```

### 4.5 Team Members

```
team_members
├── id
├── name
├── role (nullable)
├── photo (nullable)
├── bio (nullable, text)
├── email (nullable)
├── phone (nullable)
├── sort_order
├── is_published (boolean)
├── timestamps
```

### 4.6 Posts (Aktuelles / News)

```
posts
├── id
├── title
├── slug
├── excerpt (nullable)
├── body (rich text)
├── featured_image (nullable)
├── author (nullable)
├── published_at (datetime, nullable)
├── is_published (boolean)
├── timestamps
```

### 4.7 Partners

```
partners
├── id
├── name
├── logo (nullable)
├── website_url (nullable)
├── description (nullable)
├── sort_order
├── is_published (boolean)
├── timestamps
```

### 4.8 Site Settings (key-value for global content)

```
settings
├── id
├── group (e.g. "general", "contact", "social", "footer")
├── key (unique)
├── value (text)
├── type (string: "text", "textarea", "image", "boolean", "rich_text")
├── timestamps
```

**Settings seeder values:**

| Group | Key | Value |
|-------|-----|-------|
| general | company_name | HILOTEC Engineering + Consulting AG |
| general | company_slogan | Sichere IT, die einfach funktioniert. |
| general | company_subtitle | Alles was Ihr KMU im Bereich der Informationstechnologie braucht. |
| contact | address_line1 | Untere Hohle Gasse 5 |
| contact | address_zip_city | CH-3550 Langnau i.E. |
| contact | address_country | Schweiz |
| contact | phone_support_infra | +41 34 408 01 00 |
| contact | phone_support_software | +41 34 408 01 01 |
| contact | email | info@hilotec.com |
| contact | website | https://www.hilotec.com |
| contact | business_hours | Mo-Fr, 08:00-12:00, 13:30-18:00 |
| footer | cta_heading | Sie haben ein Problem oder eine Frage? Wir bieten Ihnen die Lösung, die Sie suchen. |
| footer | cta_button_text | Kontakt aufnehmen |
| footer | cta_button_url | /kontakt |
| footer | copyright | © Copyright 2025, HILOTEC Engineering + Consulting AG |
| footer | teamviewer_text | Klicken Sie hier um den TeamViewer Tool zu starten |
| footer | teamviewer_url | https://get.teamviewer.com/hilotec |
| social | linkedin | https://www.linkedin.com/company/hilotec-engineering-consulting-ag |
| social | github | https://github.com/hilotec |

---

## 5. Seeder Content Plan

All content should be inserted via `DatabaseSeeder` calling individual seeders:

### 5.1 SettingsSeeder
All key-value pairs from table above.

### 5.2 ServicesSeeder
Based on current website's service offerings:

| # | Title | Description |
|---|-------|-------------|
| 1 | IT-Infrastruktur & Netzwerke | Server, Workstations, Netzwerke — massgeschneidert für Ihr KMU |
| 2 | IT-Sicherheit | Watchguard Gold Partner: Firewall, APT-Blocker, VPN, Ransomware-Schutz |
| 3 | Cloud & Hosting | Hosting auf eigenen Servern in der Schweiz, DNS, E-Mail, Web |
| 4 | Backup-Lösungen | Veeam-basierte Sicherungen mit RTPO unter 15 Minuten |
| 5 | Software & Branchenlösungen | Sage50, M-SOFT PASST.prime, Chronikos, Elexis — Beratung, Einführung, Support |
| 6 | VoIP-Telefonie | Asterisk-basierte VoIP-Anlagen mit CTI, SIP-Telefonen, DECT |
| 7 | Virtualisierung | VMWare vSphere, KVM — Serverkonsolidierung und Kostenoptimierung |
| 8 | Beratung & Projektierung | IT-Unternehmensberatung, Anforderungsanalyse, Projektrealisierung |

### 5.3 ReferenceCategoriesSeeder + ReferencesSeeder
Categories from current site: Baugewerbe, Dienstleistungsgewerbe, Detailhandel, Elektrotechnik, Erziehungswesen, Gastronomie, Geologie/Umwelt, Gesundheitswesen, Industrie/Metallverarbeitung, Kunst/Literatur, Öffentliches Wesen, Tourismus, Solarenergie, Tiere, Weitere.

References seeded from the live site data (50+ entries with company name, address, description).

### 5.4 PagesSeeder
Static pages: Impressum, Datenschutz, Über uns body content.

### 5.5 PostsSeeder
1-2 sample posts for the Aktuelles section.

---

## 6. Technical Architecture

### 6.1 Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 |
| Admin CMS | Filament 3 |
| Frontend CSS | Tailwind CSS 3 |
| JS Interactions | Alpine.js |
| Templating | Blade |
| Database | MySQL 8 / MariaDB |
| File Storage | Laravel filesystem (local or S3) |
| Rich Text | Filament's built-in TipTap editor |
| Deployment | Any PHP 8.2+ server |

### 6.2 Directory Structure (key files)

```
app/
├── Models/
│   ├── Page.php
│   ├── Service.php
│   ├── Reference.php
│   ├── ReferenceCategory.php
│   ├── TeamMember.php
│   ├── Post.php
│   ├── Partner.php
│   └── Setting.php
├── Filament/
│   └── Resources/
│       ├── PageResource.php
│       ├── ServiceResource.php
│       ├── ReferenceResource.php
│       ├── ReferenceCategoryResource.php
│       ├── TeamMemberResource.php
│       ├── PostResource.php
│       ├── PartnerResource.php
│       └── SettingResource.php (or a custom Settings page)
├── Http/
│   └── Controllers/
│       ├── HomeController.php
│       ├── ServiceController.php
│       ├── ReferenceController.php
│       ├── AboutController.php
│       ├── PostController.php
│       └── ContactController.php
├── View/
│   └── Components/
│       ├── Layout.php
│       ├── Header.php
│       ├── Footer.php
│       ├── HeroSection.php
│       └── ServiceCard.php

resources/views/
├── components/
│   ├── layout.blade.php
│   ├── header.blade.php
│   ├── footer.blade.php
│   ├── hero-section.blade.php
│   ├── footer-cta.blade.php
│   └── service-card.blade.php
├── pages/
│   ├── home.blade.php
│   ├── angebot.blade.php
│   ├── referenzen.blade.php
│   ├── ueber-uns.blade.php
│   ├── aktuelles/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── kontakt.blade.php
│   └── page.blade.php (generic for Impressum, Datenschutz)

database/
├── migrations/
│   ├── create_pages_table.php
│   ├── create_services_table.php
│   ├── create_reference_categories_table.php
│   ├── create_references_table.php
│   ├── create_team_members_table.php
│   ├── create_posts_table.php
│   ├── create_partners_table.php
│   └── create_settings_table.php
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── SettingsSeeder.php
│   ├── ServicesSeeder.php
│   ├── ReferenceCategoriesSeeder.php
│   ├── ReferencesSeeder.php
│   ├── PagesSeeder.php
│   └── PostsSeeder.php
```

### 6.3 Routes

```php
// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/angebot', [ServiceController::class, 'index'])->name('services');
Route::get('/angebot/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/referenzen', [ReferenceController::class, 'index'])->name('references');
Route::get('/ueber-uns', [AboutController::class, 'index'])->name('about');
Route::get('/aktuelles', [PostController::class, 'index'])->name('posts');
Route::get('/aktuelles/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/kontakt', [ContactController::class, 'index'])->name('contact');
Route::post('/kontakt', [ContactController::class, 'send'])->name('contact.send');
Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
```

---

## 7. Frontend Implementation Details

### 7.1 Header Component

- Sticky/fixed position at top
- Logo with gold swoosh extending off-screen left (use `position: absolute` or negative margin for swoosh)
- Logo image from `/uploads/logo.png`
- Navigation items right-aligned, white text, gold on active with dot indicator
- Mobile: hamburger menu with slide-in panel
- Transparent on hero, gains solid dark background on scroll (Alpine.js `x-data`)

### 7.2 Home Page Hero

- Full viewport height (`min-h-screen`)
- Background: dark with the tech circuit/data stream visualization (use the uploaded hero background image)
- Content left-aligned, max ~50% width:
  - Heading: `"Sichere IT, die einfach funktioniert."` — bold italic, large
  - Subtitle: `"Alles was Ihr KMU im Bereich der Informationstechnologie braucht."`
  - CTA: Blue button `"Kontakt aufnehmen →"` (per annotated mockup preference)
- Bottom: `"⌐ Entdecken"` scroll indicator in gold

### 7.3 Über uns Hero

- Full viewport with background image (Emmental landscape at sunset)
- Centered text, white with text shadow
- Heading: `"Ihr Partner aus Langnau im Emmental"`
- Subtitle: `"Langfristiger Nutzen dank einer langfristigen Partnerschaft mit HILOTEC"`
- CTA: Gold outline button `"Zur Webcam →"`

### 7.4 Footer CTA Section

- Dark background with animated "matrix rain" effect (CSS/JS animation):
  - Vertical teal/gold lines with glowing dots falling down
  - Particle/dot animation
- Yellow card (left-aligned, rounded corners):
  - Dark text heading
  - Blue CTA button
- Implementation: Canvas animation or CSS keyframe animation for the rain effect

### 7.5 Footer

- Dark background (#0a0a0a)
- 5-column grid (responsive → stacked on mobile):
  1. Logo
  2. Fernwartung: text + TeamViewer logo/button
  3. Navigation: stacked links
  4. Anschrift: company address
  5. Kontakt: phone numbers, email
- Gold section headings with underline accent
- Divider line above copyright
- Copyright text right-aligned

### 7.6 Animations & Interactions

- Header: transparent → solid on scroll
- Hero: subtle parallax or fade-in on load
- Matrix rain: continuous CSS/Canvas animation in footer CTA
- Service cards: hover scale/glow effect
- Scroll: fade-in-up for sections (Intersection Observer via Alpine.js)
- Navigation: smooth scroll for anchor links

---

## 8. Filament Admin Panel

### 8.1 Resources to Create

| Resource | Features |
|----------|----------|
| **SettingResource** | Custom Filament page with grouped form fields for all site settings |
| **ServiceResource** | CRUD with rich text editor, image upload, drag-and-drop reorder |
| **ReferenceCategoryResource** | Simple CRUD with sort order |
| **ReferenceResource** | CRUD with category relation, filterable list |
| **TeamMemberResource** | CRUD with photo upload, sortable |
| **PostResource** | CRUD with rich text, featured image, publish date scheduling |
| **PageResource** | CRUD for static pages (Impressum, Datenschutz) with rich text |
| **PartnerResource** | CRUD with logo upload |

### 8.2 Custom Settings Page

Instead of a generic resource, create a Filament Settings page at `/admin/settings` with tabs:
- **Allgemein** (General): Company name, slogan, subtitle
- **Kontakt** (Contact): Address, phones, email, business hours
- **Footer**: CTA text, button text/URL, copyright, TeamViewer
- **Social**: LinkedIn, GitHub, etc.

---

## 9. Implementation Roadmap

### Phase 1: Project Setup (Day 1)
- `laravel new hilotec`
- Install Filament 3, Tailwind CSS, Alpine.js
- Configure database, `.env`
- Create all migrations
- Create all models with relationships and casts

### Phase 2: Seeders & Admin (Days 2-3)
- Write all seeders with real content from live website
- Create all Filament resources
- Create custom Settings admin page
- Test CRUD for all entities

### Phase 3: Frontend Layout & Components (Days 3-5)
- Tailwind config with custom colors, fonts
- Layout component (header, footer, footer-cta)
- Header with scroll behavior (Alpine.js)
- Footer with all columns
- Footer CTA with matrix rain animation
- Responsive breakpoints

### Phase 4: Page Implementation (Days 5-8)
- Home page: hero + services grid + about teaser + CTA
- Angebot: service listing + detail pages
- Referenzen: category-grouped listing with filtering
- Über uns: hero + company info + team
- Aktuelles: post listing + detail
- Kontakt: form + info + map embed
- Impressum / Datenschutz: generic page template

### Phase 5: Polish & Deploy (Days 8-10)
- Animations and transitions
- SEO meta tags (dynamic from models)
- Performance optimization (image caching, eager loading)
- Contact form with email sending (Mailable)
- Cookie consent banner (Swiss law compliance)
- Testing across browsers
- Deployment setup

---

## 10. Key Files to Provide to Claude Code

When starting the project with Claude Code, provide:
1. All uploaded design mockups (already available)
2. The `logo.png` file
3. The hero background image (extract from mockups or provide original)
4. The Emmental landscape photo (for Über uns hero)
5. TeamViewer logo/badge

---

## 11. Tailwind Configuration Excerpt

```javascript
// tailwind.config.js
module.exports = {
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      colors: {
        'hilotec': {
          'dark': '#0a0a0a',
          'darker': '#050505',
          'gold': '#d4a843',
          'gold-dark': '#c99a2e',
          'blue': '#2563eb',
          'blue-dark': '#1d4ed8',
          'gray': '#9ca3af',
          'card': '#d4a843',
        }
      },
      fontFamily: {
        'sans': ['"Your-Chosen-Sans"', 'system-ui', 'sans-serif'],
        'heading': ['"Your-Chosen-Display"', 'system-ui', 'sans-serif'],
      }
    }
  }
}
```

---

## 12. Summary of Editable Content via Admin

| Content | Managed By |
|---------|-----------|
| Company name, slogan, subtitle | Settings |
| Contact details (address, phones, email) | Settings |
| Footer CTA text and button | Settings |
| Copyright text | Settings |
| TeamViewer link | Settings |
| Hero section text per page | Pages model |
| Services (title, description, icon, body) | Services resource |
| References (company, address, description, category) | References resource |
| News/blog posts | Posts resource |
| Team members | TeamMembers resource |
| Partners | Partners resource |
| Static pages (Impressum, Datenschutz) | Pages resource |
| Navigation items | Derived from routes (hardcoded in header, reflecting published pages) |

Everything the website owner sees on the frontend is manageable through the Filament admin panel at `/admin`.
