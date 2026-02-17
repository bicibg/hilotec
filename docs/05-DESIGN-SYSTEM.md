# Design System Reference — Alpine Precision (design-v2)

This document is the single source of truth for the HILOTEC website's visual design on the `design-v2` branch: colors, fonts, spacing, components, and patterns. Everything described here maps directly to code in the `resources/` directory.

**Branch:** `design-v2` — the "Alpine Precision" theme.

If you are familiar with configuring servers and networks but not CSS, think of this document like a network diagram for the frontend -- it tells you what goes where and why.

---

## Table of Contents

1. [Design Philosophy](#1-design-philosophy)
2. [Color System](#2-color-system)
3. [Typography](#3-typography)
4. [Spacing & Layout](#4-spacing--layout)
5. [Blade Components Reference](#5-blade-components-reference)
6. [Page Layout Structure](#6-page-layout-structure)
7. [Responsive Design](#7-responsive-design)
8. [Alpine.js Interactions](#8-alpinejs-interactions)
9. [CSS Utilities](#9-css-utilities)
10. [Icons](#10-icons)
11. [Meta Tags & SEO](#11-meta-tags--seo)
12. [Differences from master Branch](#12-differences-from-master-branch)

---

## 1. Design Philosophy

The `design-v2` branch introduces the **"Alpine Precision"** theme -- a hybrid light/dark design that uses light backgrounds for content sections and dark backgrounds for the hero, footer, stats band, and CTA. This approach balances readability (light content areas with dark text) with tech credibility (dark hero and footer areas with white text).

### Core Principles

- **Light content, dark framing.** Content sections use near-white backgrounds (`#FAFAFA`, `#F3F4F6`) with dark text (`#1F2937`). The hero, stats band, footer CTA, and footer use deep navy backgrounds (`#0C1222`, `#080E1A`). This creates a clear visual hierarchy: dark sections frame the page, light sections present the content.
- **Smooth transitions between zones.** SVG wave dividers connect the dark hero to the light content and the light content to the dark footer CTA. These waves prevent hard edges between the two color zones.
- **Gold accents for trust.** The gold color (`#D4A843`) remains the primary accent: active navigation, pill labels, card left-edge bars, scroll indicator, footer headings, and CTA buttons. Gold conveys quality and established expertise.
- **Blue for actions and interactivity.** The blue (`#2563EB`) is used for primary CTA buttons, "Mehr erfahren" / "Weiterlesen" links, and hover states on titles and company names. Blue with a glow effect signals clickability.
- **Scroll reveal animations.** Elements fade in and slide up as they enter the viewport, using CSS transitions triggered by an IntersectionObserver. Stagger delays create sequenced animation within groups (heading, then subheading, then button).
- **Glassmorphism on dark surfaces.** The header (on scroll) and footer CTA card use a frosted glass effect (semi-transparent dark background + blur), giving depth without full opacity.
- **Content from the database.** All visible text (headings, phone numbers, addresses, service descriptions) comes from the database via the Filament admin panel. The Blade templates define structure and styling; content is injected dynamically.

### Visual Identity

- **Company:** HILOTEC Engineering + Consulting AG, based in the Emmental region of Switzerland
- **Language:** German (`<html lang="de">`)
- **Fonts:** Sora (headings) and DM Sans (body) -- both clean, modern sans-serif typefaces
- **Imagery:** Dark hero backgrounds with IT/technology themes, overlaid with gradient dark overlay for readability. SVG wave dividers at section boundaries.

---

## 2. Color System

All colors are defined as CSS custom properties in a `@theme` block inside `resources/css/app.css`. Tailwind CSS 4 reads these and generates utility classes automatically.

**What is a `@theme` block?** In Tailwind CSS 4, `@theme` replaces the old `tailwind.config.js` color configuration. You define CSS variables like `--color-hilotec-gold: #d4a843;` and Tailwind creates classes like `text-hilotec-gold`, `bg-hilotec-gold`, `border-hilotec-gold`, and so on.

### Color Token Reference

| Token | Hex Value | Tailwind Class Examples | Purpose |
|-------|-----------|------------------------|---------|
| **Dark tones (hero, footer, CTA)** ||||
| `hilotec-dark` | `#0C1222` | `bg-hilotec-dark`, `from-hilotec-dark` | Hero overlay, stats band, dark sections. Blue-tinted navy. |
| `hilotec-darker` | `#080E1A` | `bg-hilotec-darker` | Footer background. Deepest dark. |
| `hilotec-surface` | `#111827` | `bg-hilotec-surface`, `via-hilotec-surface` | Footer CTA gradient midpoint. Tailwind gray-900. |
| **Light tones (content sections)** ||||
| `hilotec-light` | `#FAFAFA` | `bg-hilotec-light`, `fill-hilotec-light` | Primary content section background. Also used as SVG wave fill. |
| `hilotec-light-alt` | `#F3F4F6` | `bg-hilotec-light-alt` | Alternating content sections, image placeholders. Tailwind gray-100. |
| `hilotec-white` | `#FFFFFF` | `bg-hilotec-white` | Card backgrounds (service cards, post cards, reference containers). |
| **Gold accent** ||||
| `hilotec-gold` | `#D4A843` | `text-hilotec-gold`, `bg-hilotec-gold` | Primary accent. Active nav, footer headings, pill labels, CTA buttons. |
| `hilotec-gold-dark` | `#B8922E` | `hover:bg-hilotec-gold-dark`, `text-hilotec-gold-dark` | Hover on gold buttons. Pill label text on light backgrounds. |
| `hilotec-gold-light` | `#E4BE5A` | `hover:text-hilotec-gold-light` | Hover on gold text links. Gold-bar-left gradient endpoint. |
| **Blue CTA** ||||
| `hilotec-blue` | `#2563EB` | `bg-hilotec-blue`, `text-hilotec-blue` | Primary CTA button, "Mehr erfahren" links, prose link color. |
| `hilotec-blue-dark` | `#1D4ED8` | `hover:bg-hilotec-blue-dark` | Blue button hover state. |
| `hilotec-blue-light` | `#3B82F6` | `text-hilotec-blue-light` | Lighter blue variant. |
| **Text colors** ||||
| `hilotec-text` | `#1F2937` | `text-hilotec-text` | Primary text on light backgrounds. Headings, card titles. Almost black. |
| `hilotec-text-light` | `#6B7280` | `text-hilotec-text-light` | Secondary text: card excerpts, descriptions. Medium gray. |
| `hilotec-text-muted` | `#9CA3AF` | `text-hilotec-text-muted` | Muted text: timestamps, addresses. Light gray. |
| **Legacy gray aliases** ||||
| `hilotec-gray` | `#9CA3AF` | `text-hilotec-gray` | Legacy alias, same value as `hilotec-text-muted`. Used in some dark-section contexts. |
| `hilotec-gray-light` | `#D1D5DB` | `text-hilotec-gray-light` | Emphasized secondary text on dark backgrounds. |
| `hilotec-gray-dark` | `#4B5563` | `text-hilotec-gray-dark` | Muted text on dark backgrounds. |
| **Borders** ||||
| `hilotec-border` | `#E5E7EB` | `border-hilotec-border` | Borders and dividers on light backgrounds (reference items, filter buttons). |
| `hilotec-border-dark` | `#1E293B` | `border-hilotec-border-dark` | Borders on dark backgrounds. |

### Color Usage Rules

These rules prevent accessibility and readability problems:

1. **Dark text on light backgrounds.** Content sections use `bg-hilotec-light` or `bg-hilotec-light-alt` with `text-hilotec-text` for headings and `text-hilotec-text-light` for body copy. Never use white text on light backgrounds.

2. **White text on dark backgrounds.** Hero, stats band, and footer sections use white text. Subheadings in dark sections use `text-gray-300` or `text-gray-400`.

3. **Gold for accents, not for body text.** Gold is used for pill labels, active nav indicators, footer column headings, card left-edge bars, and CTA buttons. It is not used for body paragraphs.

4. **On gold backgrounds, always use black text.** The gold CTA button (`bg-hilotec-gold`) uses `text-black` for contrast. White text on gold has poor contrast and fails accessibility standards.

5. **Blue for interactive elements.** `text-hilotec-blue` is used for "Mehr erfahren" and "Weiterlesen" links. `bg-hilotec-blue` is for primary CTA buttons. Post card titles hover to blue. Reference company names hover to blue.

6. **Alternating light section backgrounds.** Content sections alternate between `bg-hilotec-light` and `bg-hilotec-light-alt` to create visual separation without adding borders. Dark sections (stats band) break up the light sections.

### Where Colors Are Defined

```
File: resources/css/app.css
```

```css
@theme {
    /* Dark tones — hero, footer, CTA sections */
    --color-hilotec-dark: #0C1222;
    --color-hilotec-darker: #080E1A;
    --color-hilotec-surface: #111827;

    /* Light tones — content sections */
    --color-hilotec-light: #FAFAFA;
    --color-hilotec-light-alt: #F3F4F6;
    --color-hilotec-white: #FFFFFF;

    /* Gold accent */
    --color-hilotec-gold: #d4a843;
    --color-hilotec-gold-dark: #b8922e;
    --color-hilotec-gold-light: #e4be5a;

    /* Blue CTA */
    --color-hilotec-blue: #2563eb;
    --color-hilotec-blue-dark: #1d4ed8;
    --color-hilotec-blue-light: #3b82f6;

    /* Text colors */
    --color-hilotec-text: #1F2937;
    --color-hilotec-text-light: #6B7280;
    --color-hilotec-text-muted: #9CA3AF;

    /* Legacy gray aliases */
    --color-hilotec-gray: #9ca3af;
    --color-hilotec-gray-light: #d1d5db;
    --color-hilotec-gray-dark: #4b5563;

    /* Border */
    --color-hilotec-border: #E5E7EB;
    --color-hilotec-border-dark: #1E293B;

    /* Font families */
    --font-heading: 'Sora', ui-sans-serif, system-ui, sans-serif;
    --font-body: 'DM Sans', ui-sans-serif, system-ui, sans-serif;

    /* Max widths */
    --container-content: 1280px;
}
```

To add a new color, add a line like `--color-hilotec-red: #EF4444;` inside the `@theme` block. Tailwind will automatically generate `text-hilotec-red`, `bg-hilotec-red`, `border-hilotec-red`, etc.

---

## 3. Typography

### Font Families

The site uses two Google Fonts, loaded via `<link>` tags in the layout component (not installed locally).

| Token | Font Name | Tailwind Class | Loaded Weights | Used For |
|-------|-----------|---------------|----------------|----------|
| `font-heading` | [Sora](https://fonts.google.com/specimen/Sora) | `font-heading` | 400, 500, 600, 700, 800 | All headings (h1-h6), buttons, navigation, footer column titles, pill labels, stat numbers |
| `font-body` | [DM Sans](https://fonts.google.com/specimen/DM+Sans) | `font-body` | 300, 400, 500, 600, 700 (+ italic) | Body text, paragraphs, form labels. Applied to `<body>` by default. |

**How font loading works:** The layout component (`resources/views/components/layout.blade.php`) includes two `<link rel="preconnect">` tags to Google's font servers, followed by a single `<link>` that loads both font families. The `preconnect` tags tell the browser to start the connection early, which speeds up font loading.

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;...&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

### Base Styles

In `resources/css/app.css`, the `@layer base` block sets defaults so you do not have to add font classes to every element:

```css
@layer base {
    body {
        @apply font-body bg-hilotec-light text-hilotec-text antialiased;
    }
    h1, h2, h3, h4, h5, h6 {
        @apply font-heading;
    }
}
```

This means:
- All text defaults to **DM Sans** (the body font) with dark text (`#1F2937`)
- All heading elements (`<h1>` through `<h6>`) automatically use **Sora**
- The page background is `hilotec-light` (`#FAFAFA`) -- a near-white background
- `antialiased` makes text rendering smoother on screens

### Heading Hierarchy

These are the heading patterns used across the site. "Responsive classes" means the size changes at different screen widths (explained in Section 7).

| Context | Tailwind Classes | Approximate Size | Notes |
|---------|-----------------|-----------------|-------|
| Hero heading (full-height, e.g., Home) | `text-4xl md:text-5xl lg:text-6xl font-bold italic` | 36px -> 48px -> 60px | Italic only on full-height heroes. White text. |
| Hero heading (compact, e.g., inner pages) | `text-4xl md:text-5xl lg:text-6xl font-bold` | 36px -> 48px -> 60px | Not italic. White text. |
| Section heading (light bg) | `text-3xl md:text-4xl font-bold text-hilotec-text` | 30px -> 36px | Via `<x-section-heading>` with default `light=false` |
| Section heading (dark bg) | `text-3xl md:text-4xl font-bold text-white` | 30px -> 36px | Via `<x-section-heading :light="true">` |
| Card heading (service) | `text-lg font-semibold text-hilotec-text` | 18px | Turns gold-dark on hover |
| Card heading (post) | `text-lg font-semibold text-hilotec-text` | 18px | Turns blue on hover |
| Stat number (dark bg) | `text-4xl md:text-5xl font-bold text-white` | 36px -> 48px | Suffix in gold |
| Footer column heading | `text-sm font-semibold uppercase tracking-wider text-hilotec-gold` | 14px | Gold, all caps, spaced-out letters |

### Body Text Patterns

| Context | Classes | Notes |
|---------|---------|-------|
| Standard paragraph (light bg) | `text-hilotec-text-light leading-relaxed` | Default body copy on light sections |
| Hero subheading (dark bg) | `text-lg md:text-xl text-gray-300` | Lighter gray for readability on dark overlay |
| Card excerpt (light bg) | `text-hilotec-text-light text-sm leading-relaxed` | Service and post card descriptions |
| Timestamp / label | `text-xs text-hilotec-text-muted font-medium` | Post dates, reference addresses |
| Footer body text | `text-gray-400 text-sm` | Footer paragraphs and nav links |
| Rich content (CMS, light bg) | `prose prose-light prose-lg` | Tailwind Typography plugin with light-bg overrides |

---

## 4. Spacing & Layout

### Container

All page content is horizontally constrained to a maximum width of **1280px** and centered with auto margins. Horizontal padding is added so content does not touch screen edges on small screens.

The container pattern used throughout every section:

```html
<div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
    <!-- content -->
</div>
```

Breaking this down:
- `max-w-[1280px]` -- content never exceeds 1280 pixels wide (custom value in `@theme` as `--container-content`)
- `mx-auto` -- centers the container horizontally
- `px-4` -- 16px horizontal padding (mobile)
- `sm:px-6` -- 24px horizontal padding (screens >= 640px)
- `lg:px-8` -- 32px horizontal padding (screens >= 1024px)

**Note:** The hero component uses slightly wider padding (`px-6 sm:px-8 lg:px-10`) to give the hero text a bit more breathing room.

### Section Padding

Content sections use consistent vertical padding:

```html
<section class="py-20 bg-hilotec-light">
    <!-- section content -->
</section>
```

- `py-20` = 80px top + 80px bottom padding (standard content sections)
- `py-16` = 64px top + 64px bottom (stats band)
- `py-24` = 96px top + 96px bottom (footer CTA)
- Exception: The footer uses `pt-1 pb-8` (the gold gradient border adds visual top spacing)

### Section Background Alternation

Sections alternate between light and dark backgrounds to create visual rhythm. SVG wave dividers smooth the transitions between dark and light zones.

```
Dark:  Hero (bg-hilotec-dark overlay on image)
       ~~~~ SVG wave (fill-hilotec-light) ~~~~
Light: Services section (bg-hilotec-light)
Dark:  Stats band (bg-hilotec-dark)
Light: About teaser (bg-hilotec-light-alt)
       ~~~~ SVG wave (fill-hilotec-light, rotated) ~~~~
Dark:  Footer CTA (bg-gradient from-dark via-surface to-dark)
Dark:  Footer (bg-hilotec-darker)
```

The SVG wave dividers are `<svg>` elements positioned absolutely at the bottom of the hero and top of the footer CTA. They create a smooth curved transition rather than a hard edge between light and dark sections.

### Content Width Constraints

Some content is narrower than the full 1280px container to improve readability:

| Context | Classes | Max Width |
|---------|---------|-----------|
| Full container | `max-w-[1280px]` | 1280px |
| Prose / rich text content | `max-w-3xl mx-auto` | 768px |
| Footer CTA glassmorphic card | `max-w-2xl mx-auto` | 672px |
| Section heading subtitle | `max-w-2xl` (+ `mx-auto` if centered) | 672px |
| Hero text block (left-aligned) | `max-w-2xl` | 672px |
| Hero text block (centered) | `max-w-3xl mx-auto` | 768px |

### Grid Patterns

Tailwind's grid system is used for multi-column layouts. The pattern is always mobile-first: one column on phones, expanding to more columns on larger screens.

```html
<!-- Services on home page: 1 col -> 2 cols -> 4 cols -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

<!-- Services index page: 1 col -> 2 cols -> 3 cols -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Blog posts: 1 col -> 2 cols -> 3 cols -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

<!-- Stats band: 2 cols -> 4 cols -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">

<!-- Team members: 1 col -> 2 cols -> 3 cols -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

<!-- Footer columns: 1 col -> 2 cols -> 5 cols -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10">

<!-- Contact page: 1 col -> 2 cols side-by-side -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

<!-- About teaser: 1 col -> 2 cols (text + image) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

<!-- Service detail: main content (3/4) + sidebar (1/4) -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
    <div class="lg:col-span-3"><!-- main --></div>
    <div class="lg:col-span-1"><!-- sidebar --></div>
</div>
```

### Gap Sizes

- `gap-6` (24px) -- tight grids (service cards)
- `gap-8` (32px) -- standard grids (blog posts, team, stats)
- `gap-10` (40px) -- wide grids (footer columns)
- `gap-12` (48px) -- two-column layouts (contact, about teaser)

---

## 5. Blade Components Reference

Blade components are reusable HTML templates. They live in `resources/views/components/` and are used in page templates with the `<x-component-name>` syntax. Think of them like functions: you pass in parameters (called "props") and get back rendered HTML.

### 5.1 `<x-layout>` -- Page Wrapper

**File:** `resources/views/components/layout.blade.php`

The root component that wraps every page. It outputs the entire HTML document: `<!DOCTYPE html>`, `<head>` (with meta tags, fonts, CSS/JS), `<body>` (with header, your page content, footer CTA, and footer).

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | `null` | Page title. Appended after " -- " and the company name. If null, shows only the company name. |
| `metaDescription` | string | `null` | HTML meta description for search engines. |
| `metaImage` | string | `null` | Path to Open Graph image. Defaults to `images/meta/og_image.jpg`. |

**Note:** The `fullHero` prop from `master` has been removed. The layout body has `min-h-screen flex flex-col` on `<body>` and `flex-1` on `<main>` to ensure the footer sticks to the bottom of the viewport even on short pages.

**Usage:**

```blade
{{-- Minimal: just a title --}}
<x-layout title="Kontakt" metaDescription="Kontaktieren Sie uns.">
    {{-- Page-specific content goes here --}}
</x-layout>

{{-- Home page: no title (shows company name only) --}}
<x-layout>
    {{-- Home page content --}}
</x-layout>
```

**What it outputs (simplified):**

```
<!DOCTYPE html>
<html lang="de">
<head>
    [meta tags, fonts, Vite CSS/JS]
</head>
<body class="min-h-screen flex flex-col">
    <x-header />       <-- sticky navigation
    <main class="flex-1">
        [YOUR CONTENT]  <-- the {{ $slot }}
    </main>
    <x-footer-cta />   <-- glassmorphic CTA card on dark gradient
    <x-footer />       <-- navy-black footer
</body>
</html>
```

---

### 5.2 `<x-header>` -- Sticky Navigation with Glassmorphism

**File:** `resources/views/components/header.blade.php`

Fixed-position header at the top of every page. Transparent when at the top of the page, transitions to a **glassmorphic** dark background (`.glass` class) with shadow when the user scrolls down. Contains the company logo (left) and navigation links (right). On mobile, navigation collapses into a hamburger menu.

**Props:** None. Self-contained. Navigation items are hardcoded in the component.

**Navigation items (hardcoded):**

| Label | URL | Route Name |
|-------|-----|------------|
| Home | `/` | `home` |
| Angebot | `/angebot` | `services.index` |
| Referenzen | `/referenzen` | `references.index` |
| Uber uns | `/ueber-uns` | `about` |
| Aktuelles | `/aktuelles` | `posts.index` |
| Kontakt | `/kontakt` | `contact` |

**Active state (desktop):** The current page's nav item gets `text-hilotec-gold` and a full-width gold underline bar below the text (`h-0.5 bg-hilotec-gold rounded-full` spanning from `left-0` to `right-0`). Inactive items use `text-white hover:text-hilotec-gold`.

**Active state (mobile):** The current page's nav item gets `text-hilotec-gold border-l-2 border-hilotec-gold` -- a gold left border accent.

**Scroll behavior:** When `scrollY > 50`, the header transitions from `bg-transparent` to the `.glass` utility class (dark glassmorphism: `rgba(12, 18, 34, 0.85)` + `blur(12px)`) plus `shadow-lg border-b border-white/5`. When the mobile menu is open, it uses a solid `bg-hilotec-dark` instead of glass.

**Height:** 80px (`h-20`).

---

### 5.3 `<x-hero>` -- Hero Section with Wave Divider

**File:** `resources/views/components/hero.blade.php`

Full-width banner at the top of every page, below the header. Displays a background image with a **gradient dark overlay**, heading, optional badge pill, optional subheading, optional CTA button, and an **SVG wave divider** at the bottom that transitions into the light content section.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `heading` | string | *required* | Main heading text (rendered as `<h1>`) |
| `subheading` | string | `null` | Subtitle shown below the heading |
| `image` | string | `'heroes/inner_page_hero_bg.jpg'` | Background image path relative to `/images/` |
| `ctaText` | string | `null` | Button label. Both `ctaText` and `ctaUrl` must be set to show the button. |
| `ctaUrl` | string | `null` | Button link URL |
| `fullHeight` | bool | `false` | If `true`, hero takes the full viewport height (`min-h-screen`) with a scroll indicator and grid pattern overlay |
| `centered` | bool | `false` | If `true`, text is centered with `max-w-3xl mx-auto`; otherwise left-aligned with `max-w-2xl` |
| `badge` | string | `null` | Text for a gold pill badge displayed above the heading |

**Overlay:** A gradient overlay (`bg-gradient-to-b from-hilotec-dark/70 via-hilotec-dark/50 to-hilotec-dark/80`) covers the background image. On full-height heroes, an additional subtle grid pattern overlay is added at 3% opacity with 60px grid lines.

**Badge:** When provided, a gold pill label appears above the heading: `bg-hilotec-gold/15 text-hilotec-gold border border-hilotec-gold/30`, uppercase, `text-xs font-semibold tracking-wider`. The badge has `reveal stagger-1`.

**Scroll reveal:** The heading, subheading, and CTA are wrapped with `reveal` and sequential `stagger-*` classes: badge is `stagger-1`, heading is `stagger-2`, subheading is `stagger-3`, CTA button is `stagger-4`.

**CTA button:** Uses `variant="gold"` with `size="lg"` and includes a right-arrow SVG icon.

**Scroll indicator (full-height only):** A mouse-wheel shaped indicator at the bottom: a rounded pill outline (`w-6 h-10 border-2 rounded-full`) with a bouncing dot inside (`w-1.5 h-1.5 bg-current rounded-full animate-bounce`). The word "Entdecken" appears above it. Clicking it smoothly scrolls to the next section using `scrollIntoView({ behavior: 'smooth' })`.

**SVG wave divider:** At the bottom of every hero (not just full-height), an SVG wave shape transitions from the dark hero to the light content section below. The wave uses `fill-hilotec-light` and is `h-12 md:h-16`.

**Usage -- full-height hero with badge (Home page):**

```blade
<x-hero
    heading="{{ setting('general.company_slogan') }}"
    subheading="{{ setting('general.company_subtitle') }}"
    image="heroes/home_hero_bg.jpg"
    ctaText="Kontakt aufnehmen"
    ctaUrl="/kontakt"
    badge="IT-Komplettbetreuung fur KMU"
    :fullHeight="true"
/>
```

**Usage -- compact hero (inner pages):**

```blade
<x-hero
    heading="Referenzen"
    subheading="Kunden aus verschiedenen Branchen vertrauen auf unsere IT-Losungen"
    image="heroes/inner_page_hero_bg.jpg"
/>
```

This creates a shorter hero with `pt-32` (to clear the fixed header) and `pb-20`.

**Slot content:** The hero also accepts a `{{ $slot }}` for custom content below the subheading/CTA.

---

### 5.4 `<x-button>` -- Button / Link with Glow Effects

**File:** `resources/views/components/button.blade.php`

Renders either an `<a>` tag (if `href` is provided) or a `<button>` tag (for form submissions). Features rounded-xl corners, glow shadows on blue/gold variants, and a micro-interaction scale effect on hover/active.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | `null` | If set, renders as `<a>` link. If not set, renders as `<button>`. |
| `variant` | string | `'blue'` | Visual style: `'blue'`, `'gold'`, `'outline'`, or `'outline-dark'` |
| `size` | string | `'md'` | Size: `'md'` or `'lg'` |
| `type` | string | `'button'` | HTML button type (only used when `href` is null). Use `'submit'` for forms. |

**Variant details:**

| Variant | Background | Text | Hover | Extras | Use Case |
|---------|-----------|------|-------|--------|----------|
| `blue` | `bg-hilotec-blue` | White | `bg-hilotec-blue-dark` | `glow-blue` shadow | Primary CTA ("Kontakt aufnehmen", "Nachricht senden") |
| `gold` | `bg-hilotec-gold` | Black | `bg-hilotec-gold-dark` | `glow-gold` shadow | Hero CTA, footer CTA |
| `outline` | Transparent | Gold | Fills gold, text turns black | Gold border | Tertiary action on dark backgrounds |
| `outline-dark` | Transparent | `hilotec-dark` | Fills dark, text turns white | Dark border | Tertiary action on light backgrounds ("Alle Leistungen ansehen", "Mehr uber uns") |

**Size details:**

| Size | Padding | Font Size |
|------|---------|-----------|
| `md` | `px-6 py-2.5` (24px x 10px) | `text-sm` (14px) |
| `lg` | `px-8 py-3.5` (32px x 14px) | `text-base` (16px) |

**Shared base classes (all variants):**
```
inline-flex items-center justify-center
font-heading font-semibold
rounded-xl
transition-all duration-300
focus:outline-none focus:ring-2 focus:ring-offset-2
hover:scale-[1.02] active:scale-[0.98]
```

All buttons use the heading font (Sora), are rounded with `rounded-xl`, have a focus ring for keyboard accessibility, and include a micro-interaction: `hover:scale-[1.02]` (slight grow on hover) and `active:scale-[0.98]` (slight shrink on press).

**Usage examples:**

```blade
{{-- Primary CTA link --}}
<x-button href="/kontakt" variant="blue" size="lg">Kontakt aufnehmen</x-button>

{{-- Gold CTA (hero, footer CTA) --}}
<x-button href="/kontakt" variant="gold" size="lg">Kontakt aufnehmen</x-button>

{{-- Outline-dark on light backgrounds --}}
<x-button href="/angebot" variant="outline-dark">Alle Leistungen ansehen</x-button>

{{-- Outline on dark backgrounds --}}
<x-button href="/angebot" variant="outline">Alle Leistungen ansehen</x-button>

{{-- Form submit button --}}
<x-button type="submit" variant="blue" size="lg">Nachricht senden</x-button>
```

---

### 5.5 `<x-section-heading>` -- Section Title with Pill Label

**File:** `resources/views/components/section-heading.blade.php`

Consistent heading block for content sections. Renders an optional gold pill label, an `<h2>`, and an optional `<p>` subtitle. The entire block has the `reveal` class for scroll-triggered animation.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | *required* | Section title text |
| `subtitle` | string | `null` | Optional description below the title |
| `centered` | bool | `true` | Center-align the text |
| `light` | bool | `false` | Use white text (for dark backgrounds). Default `false` (dark text for light backgrounds). |
| `label` | string | `null` | Gold pill label text displayed above the title |

**Note on `light` default:** In `design-v2`, `light` defaults to `false` (dark text on light background). This is the opposite of `master`, where `light` defaulted to `true` (white text on dark background). Most content sections are now light, so the default matches the common case.

**Label pill styling:**
- On light backgrounds (`light=false`): `bg-hilotec-gold/10 text-hilotec-gold-dark`
- On dark backgrounds (`light=true`): `bg-hilotec-gold/15 text-hilotec-gold`
- Both: `rounded-full text-xs font-semibold font-heading tracking-wider uppercase mb-4`

**Usage:**

```blade
{{-- Heading on light background with label pill --}}
<x-section-heading
    title="Unsere Leistungen"
    subtitle="Alles was Ihr KMU im Bereich der Informationstechnologie braucht."
    label="Angebot"
/>

{{-- Heading on dark background --}}
<x-section-heading
    title="Kennzahlen"
    :light="true"
    :centered="true"
/>

{{-- Left-aligned heading, no label --}}
<x-section-heading
    title="Kontaktinformationen"
    :centered="false"
/>
```

**Output structure (light background, centered, with label):**

```html
<div class="reveal text-center mb-12">
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold font-heading tracking-wider uppercase mb-4 bg-hilotec-gold/10 text-hilotec-gold-dark">
        Angebot
    </span>
    <h2 class="text-3xl md:text-4xl font-heading font-bold text-hilotec-text mb-4">
        Unsere Leistungen
    </h2>
    <p class="text-lg text-hilotec-text-light max-w-2xl mx-auto">
        Alles was Ihr KMU im Bereich der Informationstechnologie braucht.
    </p>
</div>
```

---

### 5.6 `<x-service-card>` -- Service Card (White, Elevated)

**File:** `resources/views/components/service-card.blade.php`

Displays a single IT service as a white elevated card with a gold left accent bar, amber icon container, title, excerpt, and a "Mehr erfahren" (Learn more) link in blue. The entire card is clickable -- it links to the service detail page.

**Props:**

| Prop | Type | Description |
|------|------|-------------|
| `service` | `App\Models\Service` | A Service model instance with `slug`, `icon`, `title`, `excerpt` fields |

**Usage:**

```blade
@foreach($services as $service)
    <x-service-card :service="$service" />
@endforeach
```

**Visual structure:**
- White background (`bg-white`) with `card-elevated` shadow utility and `gold-bar-left` accent
- `rounded-xl`, `pl-5 pr-6 py-6` padding (extra left padding for the gold bar)
- Hover: `-translate-y-1` lift effect, enhanced shadow via `card-elevated:hover`
- Service icon inside an amber container (`w-12 h-12 rounded-lg bg-amber-50`, hover `bg-amber-100`), icon is `w-7 h-7`
- Title: `text-lg font-heading font-semibold text-hilotec-text`, turns `text-hilotec-gold-dark` on group hover
- Excerpt: `text-hilotec-text-light text-sm leading-relaxed`, truncated to 150 characters
- "Mehr erfahren" link: `text-hilotec-blue`, turns `text-hilotec-blue-dark` on hover, with a right-arrow icon that slides right via `group-hover:translate-x-1`
- The entire card has `reveal` class for scroll animation

---

### 5.7 `<x-post-card>` -- Blog Post Card (White, Elevated)

**File:** `resources/views/components/post-card.blade.php`

Blog post preview card on a white background with elevation shadow. Shows an optional featured image, publication date, title, excerpt, and "Weiterlesen" (Read more) link. The entire card is clickable.

**Props:**

| Prop | Type | Description |
|------|------|-------------|
| `post` | `App\Models\Post` | A Post model instance with `slug`, `featured_image`, `published_at`, `title`, `excerpt` fields |

**Usage:**

```blade
@foreach($posts as $post)
    <x-post-card :post="$post" />
@endforeach
```

**Visual structure:**
- White background (`bg-white`), `card-elevated` shadow, `rounded-xl`, `overflow-hidden`
- Hover: `-translate-y-1` lift effect, enhanced shadow
- Optional featured image in `aspect-video` container with `bg-hilotec-light-alt` placeholder, zoom on hover (`group-hover:scale-105`, `duration-500`)
- Date: `text-xs text-hilotec-text-muted font-medium`
- Title: `text-lg font-heading font-semibold text-hilotec-text`, turns `text-hilotec-blue` on group hover (not gold -- different from master)
- Excerpt: `text-hilotec-text-light text-sm leading-relaxed`, truncated to 160 characters
- "Weiterlesen" link: `text-hilotec-blue`, turns `text-hilotec-blue-dark` on hover, with arrow icon
- The entire card has `reveal` class for scroll animation

---

### 5.8 `<x-reference-item>` -- Reference / Client Entry

**File:** `resources/views/components/reference-item.blade.php`

A single row in the references list. Shows the client company name, optional address, description, and optional external website link. Styled as a list item with a light border separator.

**Props:**

| Prop | Type | Description |
|------|------|-------------|
| `reference` | `App\Models\Reference` | A Reference model instance with `company_name`, `website`, `address`, `description` fields |

**Usage:**

```blade
@foreach($category->references as $reference)
    <x-reference-item :reference="$reference" />
@endforeach
```

**Visual structure:**
- Bottom border separator: `border-b border-hilotec-border`, last item has no border (`last:border-b-0`)
- Padding: `py-4`
- Flex layout: `flex-col sm:flex-row sm:items-start sm:justify-between gap-2`
- Company name: `text-hilotec-text font-medium`. If the reference has a website, the name becomes a link that hovers to `text-hilotec-blue` (not gold -- different from master)
- Address: `text-hilotec-text-muted text-sm`
- Description: `text-hilotec-text-light text-sm sm:text-right sm:max-w-md`
- External link icon (3x3, 40% opacity) shown when website is present

---

### 5.9 `<x-footer-cta>` -- Call-to-Action Section (Glassmorphic)

**File:** `resources/views/components/footer-cta.blade.php`

A glassmorphic call-to-action card on a dark gradient background that appears above the footer on every page. Features a dot pattern overlay and an SVG wave divider at the top for smooth transition from light content.

**Props:** None. Content comes from settings:
- `setting('footer.cta_heading')` -- the CTA text
- `setting('footer.cta_button_text')` -- button label
- `setting('footer.cta_button_url')` -- button link

**Visual structure:**
- Full-width section: `py-24 bg-gradient-to-br from-hilotec-dark via-hilotec-surface to-hilotec-dark overflow-hidden`
- Dot pattern overlay at 4% opacity: radial gradient dots, 24px grid
- SVG wave at top (rotated 180deg, `fill-hilotec-light`) for transition from light content above: `h-12 md:h-16`
- Glassmorphic card: `.glass` utility + `rounded-2xl border border-white/10 p-8 md:p-10 text-center`, constrained to `max-w-2xl mx-auto`
- Text: `text-lg md:text-xl font-heading font-semibold text-white leading-relaxed`
- CTA button: `variant="gold" size="lg"` with a right-arrow SVG icon
- The card has `reveal` class for scroll animation

---

### 5.10 `<x-footer>` -- Site Footer

**File:** `resources/views/components/footer.blade.php`

The site footer with company information. Uses the darkest background (`bg-hilotec-darker`) with a gold gradient top border.

**Props:** None. All content comes from the `setting()` helper.

**Gold gradient border:** A 1px-tall element at the very top: `h-px bg-gradient-to-r from-transparent via-hilotec-gold to-transparent mb-16`. This creates a subtle gold line that fades in from the edges.

**Column layout (5 columns on desktop):**

| Column | Content |
|--------|---------|
| Logo | Company logo (`h-14`) linking to home |
| Fernwartung | Heading in gold, description in `text-gray-400`, TeamViewer badge link |
| Navigation | Heading in gold, links in `text-gray-400 hover:text-white` |
| Anschrift | Heading in gold, address in `text-gray-400` |
| Kontakt | Heading in gold, phone labels in `text-gray-500`, phone numbers in `text-gray-300 hover:text-hilotec-gold`, email in `text-hilotec-gold hover:text-hilotec-gold-light` |

**Copyright bar:** Below the columns, `border-t border-white/10 pt-6`:
- Copyright text in `text-gray-500 text-xs`
- Impressum and Datenschutz links in `text-gray-500 text-xs hover:text-white`
- Social icons (LinkedIn, GitHub) in circular bordered buttons: `w-8 h-8 rounded-full border border-white/10 text-gray-400 hover:text-hilotec-gold hover:border-hilotec-gold/30`
- **Back-to-top button:** Same circular style, with an up-arrow icon. Scrolls to top on click via `window.scrollTo({ top: 0, behavior: 'smooth' })`. Labeled "Nach oben" for accessibility.

---

## 6. Page Layout Structure

Every page on the site follows a light/dark alternation pattern with smooth wave transitions between zones.

### The Standard Page Template

```
+------------------------------------------+
|  <x-header />                            |  Fixed, transparent -> glass on scroll
+------------------------------------------+
|                                          |
|  <x-hero heading="..." />               |  Dark: bg image + gradient overlay
|  ~~~~~~~~ SVG wave (fill-light) ~~~~~~~~ |  Curved transition to light
|                                          |
+------------------------------------------+
|                                          |
|  <section class="py-20 bg-hilotec-       |  Light content section 1
|           light">                        |
|    <div class="max-w-[1280px] mx-auto    |
|         px-4 sm:px-6 lg:px-8">           |
|      <!-- content -->                    |
|    </div>                                |
|  </section>                              |
|                                          |
+------------------------------------------+
|                                          |
|  <section class="py-20 bg-hilotec-       |  Light content section 2 (alternating)
|           light-alt">                    |
|    <div class="max-w-[1280px] mx-auto    |
|         px-4 sm:px-6 lg:px-8">           |
|      <!-- content -->                    |
|    </div>                                |
|  </section>                              |
|                                          |
+------------------------------------------+
|  ~~~~~~~~ SVG wave (fill-light) ~~~~~~~~ |  Curved transition to dark
|                                          |
|  <x-footer-cta />                        |  Dark: glassmorphic CTA card
|                                          |
+------------------------------------------+
|  --- gold gradient border ---            |
|                                          |
|  <x-footer />                            |  Dark: navy-black footer
|                                          |
+------------------------------------------+
```

### How It Maps to Code

In a page template file (e.g., `resources/views/pages/home.blade.php`):

```blade
<x-layout title="Page Title" metaDescription="SEO description.">
    {{-- Hero: always first, dark with wave divider at bottom --}}
    <x-hero heading="Page Heading" image="heroes/inner_page_hero_bg.jpg" />

    {{-- Section 1: light background --}}
    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading title="Section Title" subtitle="Optional subtitle." label="Label" />
            {{-- Grid, cards, text, etc. --}}
        </div>
    </section>

    {{-- Section 2: alternating light background --}}
    <section class="py-20 bg-hilotec-light-alt">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- More content --}}
        </div>
    </section>
</x-layout>
```

The `<x-layout>` component automatically adds the header at the top and footer-cta + footer at the bottom. You only write what goes in the `<main>` area.

### Home Page Structure

The home page has a unique structure with a dark stats band breaking up the light sections:

```blade
<x-layout>
    {{-- 1. Full-height hero with badge and CTA (dark) --}}
    <x-hero heading="..." badge="IT-Komplettbetreuung fur KMU" :fullHeight="true" />

    {{-- 2. Services section (light) --}}
    <section class="py-20 bg-hilotec-light">
        <x-section-heading title="Unsere Leistungen" label="Angebot" />
        <!-- 4-column service card grid -->
        <!-- outline-dark button: "Alle Leistungen ansehen" -->
    </section>

    {{-- 3. Stats band (dark) with animated counters --}}
    <section class="py-16 bg-hilotec-dark">
        <!-- 4-column stats grid with animatedCounter Alpine component -->
        <!-- Stats: Jahre Erfahrung, 55+ Kunden, 8 IT-Dienstleistungen, 24/7 Monitoring -->
    </section>

    {{-- 4. About teaser (light-alt) --}}
    <section class="py-20 bg-hilotec-light-alt">
        <!-- 2-column layout: text + image with decorative gold border accent -->
    </section>
</x-layout>
```

### Page Templates in the Codebase

| Page | Template File | Route | Hero Type |
|------|--------------|-------|-----------|
| Home | `resources/views/pages/home.blade.php` | `/` | Full-height, badge, CTA button |
| Angebot (Services) | `resources/views/pages/services/index.blade.php` | `/angebot` | Compact |
| Service Detail | `resources/views/pages/services/show.blade.php` | `/angebot/{slug}` | Compact |
| Referenzen | `resources/views/pages/references.blade.php` | `/referenzen` | Compact |
| Uber uns | `resources/views/pages/about.blade.php` | `/ueber-uns` | Full-height, centered |
| Aktuelles (Blog) | `resources/views/pages/posts/index.blade.php` | `/aktuelles` | Compact |
| Blog Post Detail | `resources/views/pages/posts/show.blade.php` | `/aktuelles/{slug}` | Compact |
| Kontakt | `resources/views/pages/contact.blade.php` | `/kontakt` | Compact |
| Generic (Impressum, etc.) | `resources/views/pages/generic.blade.php` | `/{slug}` | Compact |

---

## 7. Responsive Design

The site uses a **mobile-first** approach. This means styles are written for small screens by default, and larger-screen styles are added with breakpoint prefixes (`sm:`, `md:`, `lg:`, `xl:`).

### Tailwind Breakpoints

Think of these as "from this width and up." They are minimum-width breakpoints:

| Prefix | Minimum Width | Typical Devices |
|--------|--------------|-----------------|
| (none) | 0px | Phones (portrait) |
| `sm:` | 640px | Phones (landscape), small tablets |
| `md:` | 768px | Tablets, small laptops |
| `lg:` | 1024px | Laptops, desktops |
| `xl:` | 1280px | Large desktops |

**Example:** `text-4xl md:text-5xl lg:text-6xl` means:
- Phone: 36px (`text-4xl`)
- Tablet and up: 48px (`md:text-5xl`)
- Desktop and up: 60px (`lg:text-6xl`)

### Header Responsive Behavior

The header is the primary responsive element on the site.

| Screen Size | Behavior |
|-------------|----------|
| Below `md` (< 768px) | Hamburger menu icon shown. Desktop nav hidden (`hidden md:flex`). Mobile nav panel slides down when toggled. Active items have a gold left border. |
| `md` and above (>= 768px) | Full horizontal navigation shown. Hamburger icon hidden (`md:hidden`). Active items have a gold underline bar. |

**Mobile menu details:**
- A hamburger icon (three horizontal lines) toggles the mobile menu open/closed
- When open, the icon changes to an X (close)
- A semi-transparent backdrop (`bg-black/30`) appears behind the menu with a fade transition; tapping it closes the menu
- Menu items stack vertically with gold left border on the active page
- The menu slides in with a fade + translate-up animation (200ms ease-out)

### SVG Wave Responsive Behavior

The hero wave divider and footer CTA wave divider are responsive:
- Mobile: `h-12` (48px tall)
- Tablet and up (`md:`): `h-16` (64px tall)

### Grid Responsive Patterns

All grids start as a single column on mobile and expand:

```
Phone (< 768px):   [  Card  ]    <- 1 column, full width
                    [  Card  ]
                    [  Card  ]

Tablet (>= 768px): [ Card ][ Card ]    <- 2 columns
                    [ Card ][ Card ]

Desktop (>= 1024px): [Card][Card][Card]    <- 3 columns
                      [Card][Card][Card]
```

### Responsive Patterns Used

| Pattern | Mobile | Tablet (`md`) | Desktop (`lg`) |
|---------|--------|---------------|----------------|
| Service cards (home) | 1 col | 2 cols | 4 cols |
| Service cards (index) | 1 col | 2 cols | 3 cols |
| Blog post cards | 1 col | 2 cols | 3 cols |
| Stats band | 2 cols | 4 cols | 4 cols |
| Team member cards | 1 col | 2 cols (`sm`) | 3 cols |
| Footer columns | 1 col | 2 cols (`sm`) | 5 cols |
| Contact page | 1 col (stacked) | 1 col (stacked) | 2 cols side-by-side |
| About teaser (home) | 1 col | 1 col | 2 cols (text + image) |
| Service detail | 1 col | 1 col | 3/4 main + 1/4 sidebar |
| Reference items | Stacked vertically | Side-by-side (`sm:flex-row`) | Side-by-side |

### Form Input Responsive Behavior

Form inputs on the contact page are always full-width (`w-full`). The contact form and contact info stack vertically on mobile and sit side-by-side on desktop (`lg:grid-cols-2`).

---

## 8. Alpine.js Interactions

[Alpine.js](https://alpinejs.dev/) is a lightweight JavaScript framework used for interactive behaviors. It is loaded via Vite from `resources/js/app.js` along with the `@alpinejs/intersect` plugin:

```js
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);
window.Alpine = Alpine;

// ... IntersectionObserver and animatedCounter setup ...

Alpine.start();
```

**Dependency:** `@alpinejs/intersect: ^3.15.8` is listed in `package.json` under `dependencies`.

Alpine works by adding special attributes (starting with `x-`) directly to HTML elements. No separate JavaScript files are needed for most interactions.

### 8.1 Scroll Reveal Observer

**File:** `resources/js/app.js`

A vanilla JavaScript IntersectionObserver that triggers CSS-based reveal animations on elements with the `.reveal` class.

```js
document.addEventListener('DOMContentLoaded', () => {
    const revealElements = document.querySelectorAll('.reveal');

    if (revealElements.length === 0) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
    );

    revealElements.forEach((el) => observer.observe(el));
});
```

**How it works:**

1. On DOM ready, all elements with `.reveal` class are found
2. An IntersectionObserver watches them with `threshold: 0.1` (triggers when 10% visible) and `rootMargin: '0px 0px -40px 0px'` (triggers 40px before the element fully enters the viewport)
3. When an element becomes visible, `.revealed` is added, which triggers the CSS transition (opacity 0->1, translateY 16px->0, 0.7s ease-out)
4. Each element is unobserved after revealing (one-time animation)
5. Combined with `.stagger-1` through `.stagger-8` classes, elements in a group animate in sequence

### 8.2 Animated Counter

**File:** `resources/js/app.js`

A reusable Alpine.js data component that animates a number from 0 to a target value with ease-out cubic easing:

```js
Alpine.data('animatedCounter', (targetValue, duration = 2000) => ({
    current: 0,
    target: targetValue,
    started: false,

    startCounting() {
        if (this.started) return;
        this.started = true;

        const startTime = performance.now();
        const step = (now) => {
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            this.current = Math.round(eased * this.target);
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    },
}));
```

**Used on the home page stats band:**

```blade
<div x-data="animatedCounter({{ $stat['value'] }})" x-intersect.once="startCounting()">
    <span x-text="current">0</span><span class="text-hilotec-gold">{{ $stat['suffix'] }}</span>
</div>
```

The `x-intersect.once` directive (from the `@alpinejs/intersect` plugin) triggers `startCounting()` when the element scrolls into view. The counter runs once, animating from 0 to the target over 2 seconds.

**Stats displayed:**
- `Jahre Erfahrung` (calculated from founding year) with `+` suffix
- `Zufriedene Kunden` (55) with `+` suffix
- `IT-Dienstleistungen` (8) with no suffix
- `Monitoring` (24) with `/7` suffix

### 8.3 Sticky Header with Scroll Detection

**File:** `resources/views/components/header.blade.php`

The header starts transparent and becomes a glassmorphic dark panel as you scroll down.

```html
<header
    x-data="{ scrolled: false, mobileOpen: false }"
    x-init="scrolled = window.scrollY > 50;
            window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
    :class="mobileOpen
        ? 'bg-hilotec-dark shadow-lg border-b border-white/5'
        : (scrolled
            ? 'glass shadow-lg border-b border-white/5'
            : 'bg-transparent')"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
>
```

**How it works, step by step:**

1. `x-data="{ scrolled: false, mobileOpen: false }"` -- declares two state variables
2. `x-init="..."` -- immediately checks scroll position and sets up a scroll event listener (threshold: 50px)
3. `:class="..."` -- dynamically applies CSS classes:
   - **Mobile menu open:** Solid `bg-hilotec-dark` background (no glass blur, to avoid rendering issues)
   - **Scrolled (menu closed):** `.glass` utility (dark glassmorphism) + `shadow-lg` + subtle border
   - **At top (menu closed):** `bg-transparent` -- the hero image shows through
4. `transition-all duration-300` -- the background change animates smoothly over 300 milliseconds

### 8.4 Mobile Menu Toggle

**File:** `resources/views/components/header.blade.php`

```html
{{-- Toggle button --}}
<button @click="mobileOpen = !mobileOpen" class="md:hidden text-white p-2" aria-label="Menu">
    <svg x-show="!mobileOpen" class="w-6 h-6" ...>
        <path d="M4 6h16M4 12h16M4 18h16"/>  {{-- three lines --}}
    </svg>
    <svg x-show="mobileOpen" x-cloak class="w-6 h-6" ...>
        <path d="M6 18L18 6M6 6l12 12"/>  {{-- X shape --}}
    </svg>
</button>
```

- `@click="mobileOpen = !mobileOpen"` -- toggles the state variable on each click
- `x-show="!mobileOpen"` -- shows the hamburger when closed
- `x-show="mobileOpen"` -- shows the X when open
- `x-cloak` -- hides the element until Alpine initializes (prevents a flash of the X icon on page load)

**Mobile menu backdrop (closes menu on outside tap):**

```html
<div
    x-show="mobileOpen"
    @click="mobileOpen = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="md:hidden fixed inset-0 bg-black/30 -z-10"
></div>
```

This creates a semi-transparent overlay behind the menu with a fade transition. Clicking it sets `mobileOpen = false`, which closes the menu.

**Mobile menu panel with animation:**

```html
<nav
    x-show="mobileOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="md:hidden pb-4 border-t border-white/10"
>
```

The menu fades in while sliding down from above (200ms), and fades out while sliding up (150ms).

### 8.5 Reference Category Filters

**File:** `resources/views/pages/references.blade.php`

The references page has filter buttons that show/hide reference categories without reloading the page. In `design-v2`, the filter buttons use pill-shaped styling on light backgrounds.

```html
<section class="py-20 bg-hilotec-light" x-data="{ activeCategory: 'all' }">
    {{-- Filter buttons --}}
    <button
        @click="activeCategory = 'all'"
        :class="activeCategory === 'all'
            ? 'bg-hilotec-dark text-white shadow-md'
            : 'bg-white text-hilotec-text-light hover:text-hilotec-text hover:shadow-md'"
        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border border-hilotec-border"
    >
        Alle
    </button>

    @foreach($categories as $category)
        <button
            @click="activeCategory = '{{ $category->slug }}'"
            :class="activeCategory === '{{ $category->slug }}'
                ? 'bg-hilotec-dark text-white shadow-md'
                : 'bg-white text-hilotec-text-light hover:text-hilotec-text hover:shadow-md'"
            class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border border-hilotec-border"
        >
            {{ $category->name }}
            <span class="ml-1 opacity-50">({{ $category->references->count() }})</span>
        </button>
    @endforeach

    {{-- Filtered content --}}
    @foreach($categories as $category)
        <div
            x-show="activeCategory === 'all' || activeCategory === '{{ $category->slug }}'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
        >
            <div class="bg-white rounded-xl p-6 card-elevated">
                <h3 class="text-xl font-heading font-semibold text-hilotec-text mb-4 pb-2 border-b border-hilotec-border flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-hilotec-gold rounded-full"></span>
                    {{ $category->name }}
                </h3>
                {{-- Reference items --}}
            </div>
        </div>
    @endforeach
</section>
```

**How it works:**

1. `x-data="{ activeCategory: 'all' }"` -- starts with all categories visible
2. Each filter button sets `activeCategory` to its category slug on click
3. Active button: `bg-hilotec-dark text-white shadow-md` (dark fill). Inactive: `bg-white text-hilotec-text-light` (white fill with light text)
4. All filter buttons are `rounded-full` with `border border-hilotec-border`
5. `x-show` on each category group shows/hides it based on the active filter
6. `x-transition` adds a fade-in animation when a category becomes visible
7. Each category is displayed inside a white `card-elevated` container with a gold dot accent beside the category name

---

## 9. CSS Utilities

All custom utility classes are defined in `resources/css/app.css` within `@layer utilities` and `@layer components`. These extend Tailwind's default utilities with design-system-specific effects.

### 9.1 Glassmorphism

```css
.glass {
    background: rgba(12, 18, 34, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
.glass-light {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}
```

**`.glass`** -- Dark glassmorphism. Used on the header when scrolled (transparent dark navy + blur) and the footer CTA card. The color `rgba(12, 18, 34, 0.85)` matches `hilotec-dark` at 85% opacity.

**`.glass-light`** -- Light glassmorphism. Available for use on light-background contexts. White at 80% opacity + blur.

Both include the `-webkit-backdrop-filter` prefix for Safari compatibility.

### 9.2 Scroll Reveal

```css
.reveal {
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.7s ease-out, transform 0.7s ease-out;
}
.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}
```

Elements with `.reveal` start invisible and 16px below their final position. When the IntersectionObserver (see Section 8.1) detects them in the viewport, it adds `.revealed`, triggering a 0.7-second fade-in and slide-up.

**Used on:** Section headings, service cards, post cards, hero badge/heading/subheading/CTA, footer CTA card, about teaser elements, stats band items.

### 9.3 Stagger Delays

```css
.stagger-1 { transition-delay: 0.1s; }
.stagger-2 { transition-delay: 0.2s; }
.stagger-3 { transition-delay: 0.3s; }
.stagger-4 { transition-delay: 0.4s; }
.stagger-5 { transition-delay: 0.5s; }
.stagger-6 { transition-delay: 0.6s; }
.stagger-7 { transition-delay: 0.7s; }
.stagger-8 { transition-delay: 0.8s; }
```

When combined with `.reveal`, stagger classes make elements animate in sequence. For example, in the hero:
- Badge: `reveal stagger-1` (0.1s delay)
- Heading: `reveal stagger-2` (0.2s delay)
- Subheading: `reveal stagger-3` (0.3s delay)
- CTA button: `reveal stagger-4` (0.4s delay)

Each element starts its animation 0.1s after the previous one, creating a cascading reveal effect.

### 9.4 Gold Accent Bar

```css
.gold-bar-left {
    position: relative;
}
.gold-bar-left::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #d4a843, #e4be5a);
    border-radius: 2px;
}
```

A thin (4px) vertical gold gradient bar on the left edge of an element. The gradient goes from `hilotec-gold` (#d4a843) at the top to `hilotec-gold-light` (#e4be5a) at the bottom. Rounded with 2px border-radius.

**Used on:** Service cards (`<x-service-card>`).

### 9.5 Glow Effects

```css
.glow-blue {
    box-shadow: 0 4px 24px rgba(37, 99, 235, 0.3);
}
.glow-blue:hover {
    box-shadow: 0 8px 32px rgba(37, 99, 235, 0.45);
}
.glow-gold {
    box-shadow: 0 4px 24px rgba(212, 168, 67, 0.25);
}
.glow-gold:hover {
    box-shadow: 0 8px 32px rgba(212, 168, 67, 0.4);
}
```

Colored box-shadow glow effects for buttons. The glow intensifies on hover (larger blur radius, higher opacity). The shadow colors match the button variants:
- `glow-blue` uses `rgba(37, 99, 235, ...)` -- the `hilotec-blue` color
- `glow-gold` uses `rgba(212, 168, 67, ...)` -- the `hilotec-gold` color

**Used on:** Blue variant buttons (`.glow-blue`), gold variant buttons (`.glow-gold`).

### 9.6 Card Elevation

```css
.card-elevated {
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
}
.card-elevated:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08), 0 8px 32px rgba(0,0,0,0.06);
}
```

Subtle shadow for white cards that deepens on hover. Replaces the dark-surface + border card style from `master` with a more traditional elevated card pattern suitable for light backgrounds.

**Used on:** Service cards, post cards, reference category containers, about teaser image.

### 9.7 Prose Light

Defined in `@layer components`:

```css
.prose-light {
    --tw-prose-body: #374151;
    --tw-prose-headings: #111827;
    --tw-prose-links: #2563eb;
    --tw-prose-bold: #111827;
    --tw-prose-counters: #6B7280;
    --tw-prose-bullets: #d4a843;
    --tw-prose-hr: #E5E7EB;
    --tw-prose-quotes: #111827;
    --tw-prose-quote-borders: #d4a843;
    --tw-prose-code: #111827;
    --tw-prose-pre-code: #E5E7EB;
    --tw-prose-pre-bg: #0C1222;
}
```

Custom overrides for Tailwind Typography plugin when rendering CMS content on light backgrounds. Key decisions:
- Body text: `#374151` (gray-700) for readability on white/light
- Links: `#2563eb` (hilotec-blue) -- blue for interactivity
- Bullets and quote borders: `#d4a843` (hilotec-gold) -- gold accent for visual interest
- Code blocks: Dark background (`#0C1222` / hilotec-dark) with light text (`#E5E7EB`)

**Usage:** `<div class="prose prose-light prose-lg">` replaces `<div class="prose prose-invert prose-lg">` from `master`.

---

## 10. Icons

### SVG Icon System

Service icons are stored as individual SVG files in `public/images/icons/`. Each icon is a 64x64 viewBox SVG with gold strokes (`stroke="#d4a843"`) on a transparent background.

**Available icons:**

| File | Service |
|------|---------|
| `server.svg` | Server / Infrastructure |
| `security.svg` | IT Security |
| `cloud.svg` | Cloud Services |
| `backup.svg` | Backup / Data Protection |
| `software.svg` | Software Development |
| `phone.svg` | VoIP / Telephony |
| `virtualization.svg` | Virtualization |
| `consulting.svg` | IT Consulting |

**Icon directory:** `public/images/icons/`

### Icon Style Conventions

All service icons follow a consistent style:

```xml
<svg xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 64 64"
     fill="none"
     stroke="#d4a843"
     stroke-width="2"
     stroke-linecap="round"
     stroke-linejoin="round">
    <!-- icon paths -->
</svg>
```

- **ViewBox:** `0 0 64 64` (64x64 units)
- **Stroke color:** `#d4a843` (hilotec-gold)
- **Stroke width:** 2
- **Fill:** None (outline style)
- **Line caps and joins:** Rounded for a softer look

### How Icons Are Used

In `design-v2`, service icons are displayed inside an amber container on service cards:

```blade
<div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
    <img
        src="{{ asset('images/icons/' . $service->icon) }}"
        alt=""
        class="w-7 h-7"
    >
</div>
```

The icon (`w-7 h-7` / 28x28px) sits inside a rounded amber-tinted container (`w-12 h-12` / 48x48px) that brightens on hover.

### How to Add a New Icon

1. Create an SVG file in `public/images/icons/` (e.g., `monitoring.svg`)
2. Follow the conventions above: 64x64 viewBox, gold stroke (`#d4a843`), no fill, stroke-width 2
3. In the Filament admin panel, set the service's `icon` field to the filename: `monitoring.svg`

### Inline SVG Icons

Inline SVG icons (not from files) are used throughout the site for UI elements: navigation arrows, external link indicators, the hamburger menu, social media logos, scroll indicator, and back-to-top button. These are embedded directly in the Blade templates as `<svg>` elements.

Common inline icons and where they appear:

| Icon | Location | Purpose |
|------|----------|---------|
| Right arrow (`M17 8l4 4m0 0l-4 4m4-4H3`) | Buttons, "Mehr erfahren" / "Weiterlesen" links | Indicates navigation/action |
| Up arrow (`M5 10l7-7m0 0l7 7m-7-7v18`) | Footer back-to-top button | Scroll to top |
| External link (`M10 6H6a2...`) | Reference company names | Indicates link opens externally |
| Hamburger (`M4 6h16M4 12h16M4 18h16`) | Mobile menu button (closed) | Menu toggle |
| Close X (`M6 18L18 6M6 6l12 12`) | Mobile menu button (open) | Menu toggle |
| Mouse scroll (pill + bouncing dot) | Hero scroll indicator | Indicates "scroll down" |
| Location pin | Contact page | Address |
| Phone | Contact page | Phone numbers |
| Envelope | Contact page | Email |
| Clock | Contact page | Business hours |
| LinkedIn logo | Footer | Social link |
| GitHub logo | Footer | Social link |

---

## 11. Meta Tags & SEO

### Title Pattern

Defined in `resources/views/components/layout.blade.php`:

```blade
<title>{{ $title ? $title . ' — ' . setting('general.company_name') : setting('general.company_name') }}</title>
```

- **Home page:** "HILOTEC Engineering + Consulting AG" (just the company name)
- **Inner pages:** "Page Title -- HILOTEC Engineering + Consulting AG"

### Meta Description

```blade
@if($metaDescription)
    <meta name="description" content="{{ $metaDescription }}">
@endif
```

Each page passes a `metaDescription` prop to `<x-layout>`. If omitted, no meta description tag is rendered.

### Open Graph Tags

Open Graph (OG) tags control how links to the site appear when shared on social media (LinkedIn, WhatsApp, etc.) or in chat tools.

```blade
<meta property="og:title" content="{{ $title ?? setting('general.company_name') }}">
@if($metaDescription)
    <meta property="og:description" content="{{ $metaDescription }}">
@endif
<meta property="og:image" content="{{ asset($metaImage ?? 'images/meta/og_image.jpg') }}">
<meta property="og:type" content="website">
```

- `og:title` -- same as the page title
- `og:description` -- same as the meta description
- `og:image` -- defaults to `public/images/meta/og_image.jpg` unless overridden
- `og:type` -- always "website"

### Favicons

```blade
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/meta/favicon-32x32.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/meta/apple-touch-icon.png') }}">
```

Favicon files are stored in:
- `public/favicon.ico` -- main favicon
- `public/images/meta/favicon-32x32.png` -- 32x32 PNG
- `public/images/meta/apple-touch-icon.png` -- 180x180 for Apple devices

### CSRF Token

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

Required by Laravel for POST form submissions (the contact form). The token is automatically included in forms that use `@csrf`.

### Viewport

```blade
<meta name="viewport" content="width=device-width, initial-scale=1">
```

Standard responsive viewport tag. Tells mobile browsers to render at device width instead of simulating a desktop-width screen.

---

## 12. Differences from master Branch

The `design-v2` branch is a visual redesign of the `master` branch. Both share the same Laravel backend, models, controllers, routes, and Filament admin panel. The differences are purely in the frontend presentation layer.

### Summary of Key Differences

| Aspect | `master` branch | `design-v2` branch |
|--------|-----------------|---------------------|
| **Theme** | Dark-only (all sections dark) | Light/dark hybrid ("Alpine Precision") |
| **Body default** | `bg-hilotec-dark text-white` | `bg-hilotec-light text-hilotec-text` |
| **Dark token** | `#0A0A0A` (pure black) | `#0C1222` (blue-tinted navy) |
| **Content sections** | `bg-hilotec-dark` / `bg-hilotec-surface` | `bg-hilotec-light` / `bg-hilotec-light-alt` |
| **Cards** | Dark surface bg, white text | White bg, card-elevated shadow, dark text |
| **Section transitions** | Hard edges | SVG wave dividers |
| **Header on scroll** | `bg-hilotec-dark/95 backdrop-blur-sm` | `.glass` (glassmorphism) |
| **Active nav indicator** | Gold dot below text | Full-width gold underline bar |
| **Hero overlay** | Flat `bg-hilotec-dark/50` | Gradient `from-dark/70 via-dark/50 to-dark/80` |
| **Hero extras** | None | Badge pill, grid pattern, wave divider |
| **Button shape** | `rounded-lg` | `rounded-xl` with glow shadows and scale effects |
| **Button variants** | blue, gold, outline | blue, gold, outline, **outline-dark** |
| **Section heading `light` default** | `true` (white text) | `false` (dark text) |
| **Section heading extras** | None | `label` prop (gold pill) |
| **Scroll animations** | None | `reveal` + `stagger-*` on most elements |
| **Post card title hover** | Gold | Blue |
| **Reference company hover** | Gold | Blue |
| **Footer CTA** | Gold card on background image | Glassmorphic card on dark gradient |
| **Footer extras** | None | Gold gradient top border, back-to-top button |
| **Prose class** | `prose-invert` | `prose-light` |
| **Stats band** | Not present | Animated counters on dark bg |
| **Alpine.js intersect** | Not used | `@alpinejs/intersect` plugin |
| **Security hardening** | Full (SecurityHeaders, ThrottleAdminLogin, SecurityAudit, hardened .htaccess) | **Not yet implemented** |

### Merging Guidance

When merging `design-v2` into `master` or vice versa, the security hardening from `master` (middleware, session config, .htaccess) should be applied on top of the `design-v2` visual changes. See `docs/10-BRANCH-COMPARISON.md` for a detailed diff and merge guide.
