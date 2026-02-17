# Design System Reference

This document is the single source of truth for the HILOTEC website's visual design: colors, fonts, spacing, components, and patterns. Everything described here maps directly to code in the `resources/` directory.

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
9. [Icons](#9-icons)
10. [Meta Tags & SEO](#10-meta-tags--seo)
11. [design-v2 Branch Differences](#11-design-v2-branch-differences)
12. [How to Add a New Page](#12-how-to-add-a-new-page)

---

## 1. Design Philosophy

The HILOTEC website uses a **dark corporate theme** built for a Swiss IT infrastructure company. The design communicates professionalism, reliability, and technical competence -- the same qualities clients expect from their IT partner.

### Core Principles

- **Dark backgrounds, light text.** The entire site uses near-black backgrounds (`#0A0A0A`) with white and gray text. This is similar to a terminal or monitoring dashboard aesthetic -- familiar territory for IT professionals.
- **Gold accents for trust.** The gold color (`#D4A843`) is used sparingly for emphasis: active navigation items, section headings in the footer, hover states, and the call-to-action card. Gold conveys quality and established expertise.
- **Blue for actions.** The blue (`#2563EB`) is reserved exclusively for clickable buttons that invite the user to do something (contact, learn more). It is never used for decoration.
- **Minimal animation.** Transitions are subtle (color fades, slight hover effects). Nothing bounces, spins, or distracts. The design respects the user's time.
- **Content from the database.** All visible text (headings, phone numbers, addresses, service descriptions) comes from the database via the Filament admin panel. The Blade templates define structure and styling; content is injected dynamically.

### Visual Identity

- **Company:** HILOTEC Engineering + Consulting AG, based in the Emmental region of Switzerland
- **Language:** German (`<html lang="de">`)
- **Fonts:** Sora (headings) and DM Sans (body) -- both clean, modern sans-serif typefaces
- **Imagery:** Dark hero backgrounds with IT/technology themes, overlaid with semi-transparent black for readability

---

## 2. Color System

All colors are defined as CSS custom properties in a `@theme` block inside `resources/css/app.css`. Tailwind CSS 4 reads these and generates utility classes automatically.

**What is a `@theme` block?** In Tailwind CSS 4, `@theme` replaces the old `tailwind.config.js` color configuration. You define CSS variables like `--color-hilotec-gold: #d4a843;` and Tailwind creates classes like `text-hilotec-gold`, `bg-hilotec-gold`, `border-hilotec-gold`, and so on.

### Color Token Reference

| Token | Hex Value | Tailwind Class Examples | Purpose |
|-------|-----------|------------------------|---------|
| `hilotec-dark` | `#0A0A0A` | `bg-hilotec-dark`, `text-hilotec-dark` | Primary page background. Almost black. |
| `hilotec-darker` | `#050505` | `bg-hilotec-darker` | Footer background. Even darker than the main bg. |
| `hilotec-surface` | `#111318` | `bg-hilotec-surface` | Card backgrounds, alternating sections. A slightly lighter dark. |
| `hilotec-gold` | `#D4A843` | `text-hilotec-gold`, `bg-hilotec-gold` | Primary accent color. Active nav, footer headings, CTA card. |
| `hilotec-gold-dark` | `#B8922E` | `hover:bg-hilotec-gold-dark` | Darker gold for hover states on gold buttons. |
| `hilotec-gold-light` | `#E4BE5A` | `hover:text-hilotec-gold-light` | Lighter gold for hover states on gold text links. |
| `hilotec-blue` | `#2563EB` | `bg-hilotec-blue` | Primary CTA button color. Blue = "click me." |
| `hilotec-blue-dark` | `#1D4ED8` | `hover:bg-hilotec-blue-dark` | Darker blue for button hover states. |
| `hilotec-gray` | `#9CA3AF` | `text-hilotec-gray` | Body text on dark backgrounds. Medium gray. |
| `hilotec-gray-light` | `#D1D5DB` | `text-hilotec-gray-light` | Emphasized secondary text. Brighter than body gray. |
| `hilotec-gray-dark` | `#4B5563` | `text-hilotec-gray-dark` | Muted text: timestamps, labels, copyright text. |

### Color Usage Rules

These rules prevent accessibility and readability problems:

1. **Gold on dark backgrounds:** Use for headings, accents, hover states, and active indicators. Gold text on `hilotec-dark` or `hilotec-surface` has strong contrast.

2. **On gold backgrounds, always use black text.** White text on gold has poor contrast and fails accessibility standards. The footer CTA card (`bg-hilotec-gold`) uses `text-black`.

3. **Blue is for buttons only.** Do not use `bg-hilotec-blue` for section backgrounds or decorative elements. It is reserved for primary call-to-action buttons and "learn more" links.

4. **White text (`text-white`) for main headings** on dark backgrounds. Section titles, hero headings, and card titles use white.

5. **Gray text for body copy.** Paragraphs and descriptions use `text-hilotec-gray`. For secondary/muted information (dates, labels), use `text-hilotec-gray-dark`.

6. **Alternating section backgrounds.** Sections alternate between `bg-hilotec-dark` and `bg-hilotec-surface` to create visual separation without adding borders.

### Where Colors Are Defined

```
File: resources/css/app.css
```

```css
@theme {
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

    /* ... fonts and container defined here too */
}
```

To add a new color, add a line like `--color-hilotec-red: #EF4444;` inside the `@theme` block. Tailwind will automatically generate `text-hilotec-red`, `bg-hilotec-red`, `border-hilotec-red`, etc.

---

## 3. Typography

### Font Families

The site uses two Google Fonts, loaded via `<link>` tags in the layout component (not installed locally).

| Token | Font Name | Tailwind Class | Loaded Weights | Used For |
|-------|-----------|---------------|----------------|----------|
| `font-heading` | [Sora](https://fonts.google.com/specimen/Sora) | `font-heading` | 400, 500, 600, 700, 800 | All headings (h1-h6), buttons, navigation, footer column titles |
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
        @apply font-body bg-hilotec-dark text-white antialiased;
    }
    h1, h2, h3, h4, h5, h6 {
        @apply font-heading;
    }
}
```

This means:
- All text defaults to **DM Sans** (the body font)
- All heading elements (`<h1>` through `<h6>`) automatically use **Sora**
- The page background is `hilotec-dark` and text is white by default
- `antialiased` makes text rendering smoother on screens

### Heading Hierarchy

These are the heading patterns used across the site. "Responsive classes" means the size changes at different screen widths (explained in Section 7).

| Context | Tailwind Classes | Approximate Size | Notes |
|---------|-----------------|-----------------|-------|
| Hero heading (full-height, e.g., Home) | `text-4xl md:text-5xl lg:text-6xl font-bold italic` | 36px -> 48px -> 60px | Italic only on full-height heroes |
| Hero heading (compact, e.g., inner pages) | `text-4xl md:text-5xl lg:text-6xl font-bold` | 36px -> 48px -> 60px | Not italic |
| Section heading | `text-3xl md:text-4xl font-bold` | 30px -> 36px | Via `<x-section-heading>` component |
| Subsection heading (e.g., contact info) | `text-2xl font-bold` | 24px | Manual `<h2>` in page templates |
| Category heading (e.g., references) | `text-xl font-semibold` | 20px | Gold text with bottom border |
| Card heading (services, posts) | `text-lg font-semibold` | 18px | White, turns gold on hover |
| Footer column heading | `text-sm font-semibold uppercase tracking-wider` | 14px | Gold, all caps, spaced-out letters |

### Body Text Patterns

| Context | Classes | Notes |
|---------|---------|-------|
| Standard paragraph | `text-hilotec-gray leading-relaxed` | Default body copy on dark bg |
| Hero subheading | `text-lg md:text-xl text-hilotec-gray-light` | Brighter than body for emphasis |
| Card excerpt | `text-hilotec-gray text-sm leading-relaxed` | Smaller text within cards |
| Timestamp / label | `text-xs text-hilotec-gray-dark font-medium` | Very small, muted |
| Rich content (CMS) | `prose prose-invert prose-lg` | Tailwind Typography plugin for CMS HTML content |

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

### Section Padding

Content sections use consistent vertical padding:

```html
<section class="py-20 bg-hilotec-dark">
    <!-- section content -->
</section>
```

- `py-20` = 80px top + 80px bottom padding
- Exception: The footer uses `pt-16 pb-8` (64px top, 32px bottom)
- Exception: The footer CTA section uses `py-20` (same as content sections)

### Content Width Constraints

Some content is narrower than the full 1280px container to improve readability:

| Context | Classes | Max Width |
|---------|---------|-----------|
| Full container | `max-w-[1280px]` | 1280px |
| Prose / rich text content | `max-w-3xl mx-auto` | 768px |
| Footer CTA card | `max-w-2xl mx-auto` | 672px |
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

<!-- Team members: 1 col -> 2 cols -> 3 cols -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

<!-- Footer columns: 1 col -> 2 cols -> 5 cols -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10">

<!-- Contact page: 1 col -> 2 cols side-by-side -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

<!-- Service detail: main content (3/4) + sidebar (1/4) -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
    <div class="lg:col-span-3"><!-- main --></div>
    <div class="lg:col-span-1"><!-- sidebar --></div>
</div>
```

### Gap Sizes

- `gap-6` (24px) -- tight grids (service cards)
- `gap-8` (32px) -- standard grids (blog posts, team)
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
| `fullHero` | bool | `false` | Currently passed but not used within layout itself (available for future use). |

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
<body>
    <x-header />       <-- sticky navigation
    <main>
        [YOUR CONTENT]  <-- the {{ $slot }}
    </main>
    <x-footer-cta />   <-- gold CTA card
    <x-footer />       <-- site footer
</body>
</html>
```

---

### 5.2 `<x-header>` -- Sticky Navigation

**File:** `resources/views/components/header.blade.php`

Fixed-position header at the top of every page. Transparent when at the top of the page, transitions to a solid dark background with shadow when the user scrolls down. Contains the company logo (left) and navigation links (right). On mobile, navigation collapses into a hamburger menu.

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

**Active state:** The current page's nav item is highlighted in gold with a small gold dot positioned below the text. Active detection uses `request()->is()` to match the URL path.

**Scroll behavior (Alpine.js):** See Section 8 for details.

---

### 5.3 `<x-hero>` -- Hero Section

**File:** `resources/views/components/hero.blade.php`

Full-width banner at the top of every page, below the header. Displays a background image with a dark semi-transparent overlay, a heading, optional subheading, and optional CTA button.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `heading` | string | *required* | Main heading text (rendered as `<h1>`) |
| `subheading` | string | `null` | Subtitle shown below the heading |
| `image` | string | `'heroes/inner_page_hero_bg.jpg'` | Background image path relative to `/images/` |
| `ctaText` | string | `null` | Button label. Both `ctaText` and `ctaUrl` must be set to show the button. |
| `ctaUrl` | string | `null` | Button link URL |
| `fullHeight` | bool | `false` | If `true`, hero takes the full viewport height with a scroll indicator at the bottom |
| `centered` | bool | `false` | If `true`, text is centered; otherwise left-aligned |

**Usage -- full-height hero (Home page, About page):**

```blade
<x-hero
    heading="{{ setting('general.company_slogan') }}"
    subheading="{{ setting('general.company_subtitle') }}"
    image="heroes/home_hero_bg.jpg"
    ctaText="Kontakt aufnehmen"
    ctaUrl="/kontakt"
    :fullHeight="true"
/>
```

This creates a hero that fills the entire screen. A bouncing "Entdecken" (Discover) scroll indicator appears at the bottom. When clicked, it smoothly scrolls to the next section.

**Usage -- compact hero (inner pages):**

```blade
<x-hero
    heading="Referenzen"
    subheading="Kunden aus verschiedenen Branchen vertrauen auf unsere IT-Losungen"
    image="heroes/inner_page_hero_bg.jpg"
/>
```

This creates a shorter hero with top padding (`pt-32`) to clear the fixed header and bottom padding (`pb-20`).

**Slot content:** The hero also accepts a `{{ $slot }}` for custom content below the subheading/CTA.

**Overlay:** The dark overlay (`bg-hilotec-dark/50`) ensures text is readable regardless of the background image. The `/50` means 50% opacity.

---

### 5.4 `<x-button>` -- Button / Link

**File:** `resources/views/components/button.blade.php`

Renders either an `<a>` tag (if `href` is provided) or a `<button>` tag (for form submissions). Consistent styling across the site with variant and size options.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | `null` | If set, renders as `<a>` link. If not set, renders as `<button>`. |
| `variant` | string | `'blue'` | Visual style: `'blue'`, `'gold'`, or `'outline'` |
| `size` | string | `'md'` | Size: `'md'` or `'lg'` |
| `type` | string | `'button'` | HTML button type (only used when `href` is null). Use `'submit'` for forms. |

**Variant details:**

| Variant | Background | Text | Hover | Use Case |
|---------|-----------|------|-------|----------|
| `blue` | `bg-hilotec-blue` | White | Darker blue | Primary CTA ("Kontakt aufnehmen", "Nachricht senden") |
| `gold` | `bg-hilotec-gold` | Black | Darker gold | Secondary prominent action |
| `outline` | Transparent + gold border | Gold | Fills gold, text turns black | Tertiary action ("Alle Leistungen ansehen", "Mehr uber uns") |

**Size details:**

| Size | Padding | Font Size |
|------|---------|-----------|
| `md` | `px-6 py-2.5` (24px x 10px) | `text-sm` (14px) |
| `lg` | `px-8 py-3.5` (32px x 14px) | `text-base` (16px) |

**Usage examples:**

```blade
{{-- Primary CTA link --}}
<x-button href="/kontakt" variant="blue" size="lg">Kontakt aufnehmen</x-button>

{{-- Gold button --}}
<x-button variant="gold">Gold Button</x-button>

{{-- Outline link --}}
<x-button href="/angebot" variant="outline">Alle Leistungen ansehen</x-button>

{{-- Form submit button --}}
<x-button type="submit" variant="blue" size="lg">Nachricht senden</x-button>
```

**Shared base classes (all variants):**
```
inline-flex items-center justify-center
font-heading font-semibold
rounded-lg
transition-all duration-200
focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-hilotec-dark
```

All buttons use the heading font (Sora), are rounded, have a focus ring for keyboard accessibility, and transition smoothly on hover.

---

### 5.5 `<x-section-heading>` -- Section Title

**File:** `resources/views/components/section-heading.blade.php`

Consistent heading block for content sections. Renders an `<h2>` with an optional `<p>` subtitle below it.

**Props:**

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | *required* | Section title text |
| `subtitle` | string | `null` | Optional description below the title |
| `centered` | bool | `true` | Center-align the text |
| `light` | bool | `true` | Use white text (for dark backgrounds). Set to `false` for light backgrounds. |

**Usage:**

```blade
{{-- Standard centered heading on dark background --}}
<x-section-heading
    title="Unsere Leistungen"
    subtitle="Alles was Ihr KMU im Bereich der Informationstechnologie braucht."
/>

{{-- Left-aligned heading, dark text on light background --}}
<x-section-heading
    title="Kontaktinformationen"
    :centered="false"
    :light="false"
/>
```

**Output structure:**

```html
<div class="text-center mb-12">
    <h2 class="text-3xl md:text-4xl font-heading font-bold text-white mb-4">
        Unsere Leistungen
    </h2>
    <p class="text-lg text-hilotec-gray max-w-2xl mx-auto">
        Alles was Ihr KMU im Bereich der Informationstechnologie braucht.
    </p>
</div>
```

---

### 5.6 `<x-service-card>` -- Service Card

**File:** `resources/views/components/service-card.blade.php`

Displays a single IT service as a card with icon, title, excerpt, and a "Mehr erfahren" (Learn more) link. The entire card is clickable -- it links to the service detail page.

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
- Dark surface background (`bg-hilotec-surface`) with a subtle border (`border-white/5`)
- Rounded corners (`rounded-xl`)
- Service icon (SVG from `public/images/icons/`) at the top, 48x48px
- Title in white, turns gold on hover
- Excerpt text in gray, truncated to 150 characters
- "Mehr erfahren" link in blue, turns gold on hover, with a right-arrow icon that slides right on hover

**Hover behavior:** The card border brightens to a faint gold (`hover:border-hilotec-gold/30`), the title and link text change color, and the arrow icon moves right.

---

### 5.7 `<x-post-card>` -- Blog Post Card

**File:** `resources/views/components/post-card.blade.php`

Blog post preview card. Shows an optional featured image, publication date, title, excerpt, and "Weiterlesen" (Read more) link. The entire card is clickable.

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
- Dark surface background, same styling as service cards
- Optional featured image in 16:9 aspect ratio at the top, with a zoom effect on hover (`group-hover:scale-105`)
- Date in small muted gray text
- Title in white, turns gold on hover
- Excerpt in gray, truncated to 160 characters
- "Weiterlesen" link with arrow, same hover behavior as service card

---

### 5.8 `<x-reference-item>` -- Reference / Client Entry

**File:** `resources/views/components/reference-item.blade.php`

A single row in the references list. Shows the client company name, optional address, description, and optional external website link. Not a card -- it is a list item with a bottom border separator.

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
- Bottom border separator (`border-b border-white/5`), last item has no border
- Flex layout: company name + address on the left, description on the right
- If the reference has a website, the company name becomes a link with an external-link icon
- On mobile, the layout stacks vertically

---

### 5.9 `<x-footer-cta>` -- Call-to-Action Section

**File:** `resources/views/components/footer-cta.blade.php`

A gold call-to-action card that appears above the footer on every page. Sits on top of a background image (matrix rain / code pattern).

**Props:** None. Content comes from settings:
- `setting('footer.cta_heading')` -- the CTA text
- `setting('footer.cta_button_text')` -- button label
- `setting('footer.cta_button_url')` -- button link

**Visual structure:**
- Full-width section with a background image and dark overlay
- Centered gold card (`bg-hilotec-gold rounded-lg p-8 md:p-10`) constrained to `max-w-2xl`
- Black text on the gold card (for contrast)
- Blue CTA button with a right-arrow icon

---

### 5.10 `<x-footer>` -- Site Footer

**File:** `resources/views/components/footer.blade.php`

The site footer with company information. Uses the darkest background (`bg-hilotec-darker`).

**Props:** None. All content comes from the `setting()` helper.

**Column layout (5 columns on desktop):**

| Column | Content |
|--------|---------|
| Logo | Company logo linking to home |
| Fernwartung | TeamViewer remote support text and badge link |
| Navigation | Links to all main pages |
| Anschrift | Company address |
| Kontakt | Phone numbers (infrastructure + software support) and email |

**Footer bar:** Below the columns, a thin border separates the copyright text, Impressum/Datenschutz links, and social media icons (LinkedIn, GitHub).

---

## 6. Page Layout Structure

Every page on the site follows the same structural pattern. Understanding this pattern makes it easy to create new pages or modify existing ones.

### The Standard Page Template

```
+------------------------------------------+
|  <x-header />                            |  Fixed position, transparent -> solid on scroll
+------------------------------------------+
|                                          |
|  <x-hero heading="..." />               |  Full-width background image + overlay + text
|                                          |
+------------------------------------------+
|                                          |
|  <section class="py-20 bg-hilotec-dark"> |  Content section 1 (dark background)
|    <div class="max-w-[1280px] mx-auto    |
|         px-4 sm:px-6 lg:px-8">           |
|      <!-- content -->                    |
|    </div>                                |
|  </section>                              |
|                                          |
+------------------------------------------+
|                                          |
|  <section class="py-20 bg-hilotec-       |  Content section 2 (surface background)
|           surface">                      |
|    <div class="max-w-[1280px] mx-auto    |
|         px-4 sm:px-6 lg:px-8">           |
|      <!-- content -->                    |
|    </div>                                |
|  </section>                              |
|                                          |
+------------------------------------------+
|                                          |
|  <x-footer-cta />                        |  Gold CTA card on background image
|                                          |
+------------------------------------------+
|                                          |
|  <x-footer />                            |  Dark footer with contact info
|                                          |
+------------------------------------------+
```

### How It Maps to Code

In a page template file (e.g., `resources/views/pages/home.blade.php`):

```blade
<x-layout title="Page Title" metaDescription="SEO description.">
    {{-- Hero: always first --}}
    <x-hero heading="Page Heading" image="heroes/inner_page_hero_bg.jpg" />

    {{-- Section 1: dark background --}}
    <section class="py-20 bg-hilotec-dark">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading title="Section Title" subtitle="Optional subtitle." />
            {{-- Grid, cards, text, etc. --}}
        </div>
    </section>

    {{-- Section 2: surface background (alternating) --}}
    <section class="py-20 bg-hilotec-surface">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- More content --}}
        </div>
    </section>
</x-layout>
```

The `<x-layout>` component automatically adds the header at the top and footer-cta + footer at the bottom. You only write what goes in the `<main>` area.

### Page Templates in the Codebase

| Page | Template File | Route | Hero Type |
|------|--------------|-------|-----------|
| Home | `resources/views/pages/home.blade.php` | `/` | Full-height, CTA button |
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
| Below `md` (< 768px) | Hamburger menu icon shown. Desktop nav hidden (`hidden md:flex`). Mobile nav panel slides down when toggled. |
| `md` and above (>= 768px) | Full horizontal navigation shown. Hamburger icon hidden (`md:hidden`). |

**Mobile menu details:**
- A hamburger icon (three horizontal lines) toggles the mobile menu open/closed
- When open, the icon changes to an X (close)
- A semi-transparent backdrop appears behind the menu; tapping it closes the menu
- Menu items stack vertically with gold highlight on the active page
- The menu slides in with a fade + translate-up animation

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
| Team member cards | 1 col | 2 cols (`sm`) | 3 cols |
| Footer columns | 1 col | 2 cols (`sm`) | 5 cols |
| Contact page | 1 col (stacked) | 1 col (stacked) | 2 cols side-by-side |
| About teaser (home) | 1 col | 1 col | 2 cols (text + image) |
| Service detail | 1 col | 1 col | 3/4 main + 1/4 sidebar |

### Form Input Responsive Behavior

Form inputs on the contact page are always full-width (`w-full`). The contact form and contact info stack vertically on mobile and sit side-by-side on desktop (`lg:grid-cols-2`).

---

## 8. Alpine.js Interactions

[Alpine.js](https://alpinejs.dev/) is a lightweight JavaScript framework used for small interactive behaviors. It is loaded via Vite from `resources/js/app.js`:

```js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

Alpine works by adding special attributes (starting with `x-`) directly to HTML elements. No separate JavaScript files are needed for most interactions.

### 8.1 Sticky Header with Scroll Detection

**File:** `resources/views/components/header.blade.php`

The header starts transparent and becomes solid dark as you scroll down.

```html
<header
    x-data="{ scrolled: false, mobileOpen: false }"
    x-init="scrolled = window.scrollY > 50;
            window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
    :class="mobileOpen
        ? 'bg-hilotec-dark shadow-lg border-b border-white/5'
        : (scrolled
            ? 'bg-hilotec-dark/95 backdrop-blur-sm shadow-lg'
            : 'bg-transparent')"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
>
```

**How it works, step by step:**

1. `x-data="{ scrolled: false, mobileOpen: false }"` -- declares two state variables. `scrolled` tracks whether the user has scrolled past 50 pixels. `mobileOpen` tracks whether the mobile menu is open.

2. `x-init="..."` -- runs once when the page loads. It immediately checks scroll position (in case the page loads mid-scroll) and sets up a scroll event listener.

3. `:class="..."` -- dynamically applies CSS classes based on the state:
   - **Mobile menu open:** Solid dark background, no blur
   - **Scrolled (menu closed):** 95% opaque dark background with backdrop blur and shadow
   - **At top (menu closed):** Fully transparent -- the hero image shows through

4. `transition-all duration-300` -- the background change animates smoothly over 300 milliseconds.

### 8.2 Mobile Menu Toggle

**File:** `resources/views/components/header.blade.php`

```html
{{-- Toggle button --}}
<button @click="mobileOpen = !mobileOpen" class="md:hidden text-white p-2" aria-label="Menu">
    {{-- Hamburger icon (shown when menu is closed) --}}
    <svg x-show="!mobileOpen" class="w-6 h-6" ...>
        <path d="M4 6h16M4 12h16M4 18h16"/>  {{-- three lines --}}
    </svg>
    {{-- Close icon (shown when menu is open) --}}
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
    class="md:hidden fixed inset-0 bg-black/30 -z-10"
></div>
```

This creates a semi-transparent overlay behind the menu. Clicking it sets `mobileOpen = false`, which closes the menu.

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

The `x-transition` attributes define the enter/leave animations: the menu fades in while sliding down from above (200ms), and fades out while sliding up (150ms).

### 8.3 Reference Category Filters

**File:** `resources/views/pages/references.blade.php`

The references page has filter buttons that show/hide reference categories without reloading the page.

```html
<section x-data="{ activeCategory: 'all' }">
    {{-- Filter buttons --}}
    <button
        @click="activeCategory = 'all'"
        :class="activeCategory === 'all'
            ? 'bg-hilotec-gold text-black'
            : 'bg-hilotec-surface text-hilotec-gray hover:text-white'"
        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
    >
        Alle
    </button>

    @foreach($categories as $category)
        <button
            @click="activeCategory = '{{ $category->slug }}'"
            :class="activeCategory === '{{ $category->slug }}'
                ? 'bg-hilotec-gold text-black'
                : 'bg-hilotec-surface text-hilotec-gray hover:text-white'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
        >
            {{ $category->name }}
            <span class="ml-1 opacity-60">({{ $category->references->count() }})</span>
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
            {{-- Category heading and reference items --}}
        </div>
    @endforeach
</section>
```

**How it works:**

1. `x-data="{ activeCategory: 'all' }"` -- starts with all categories visible
2. Each filter button sets `activeCategory` to its category slug on click
3. `:class` on each button changes its appearance based on whether it is the active filter (gold background = active, dark surface = inactive)
4. `x-show` on each category group shows/hides it based on the active filter
5. `x-transition` adds a fade-in animation when a category becomes visible

### 8.4 Hero Scroll Indicator

The full-height hero has a bouncing "Entdecken" button at the bottom:

```html
<button
    onclick="this.closest('section').nextElementSibling?.scrollIntoView({ behavior: 'smooth' })"
    class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce cursor-pointer"
>
```

This is plain JavaScript (not Alpine): when clicked, it finds the next sibling element after the hero `<section>` and smoothly scrolls to it.

---

## 9. Icons

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

Service icons are referenced by filename in the database (`$service->icon` stores just the filename, e.g., `server.svg`). The component builds the full path:

```blade
<img
    src="{{ asset('images/icons/' . $service->icon) }}"
    alt=""
    class="w-12 h-12"
>
```

### How to Add a New Icon

1. Create an SVG file in `public/images/icons/` (e.g., `monitoring.svg`)
2. Follow the conventions above: 64x64 viewBox, gold stroke (`#d4a843`), no fill, stroke-width 2
3. In the Filament admin panel, set the service's `icon` field to the filename: `monitoring.svg`

### Inline SVG Icons

Inline SVG icons (not from files) are used throughout the site for UI elements: navigation arrows, external link indicators, the hamburger menu, social media logos, and contact page icons. These are embedded directly in the Blade templates as `<svg>` elements.

Common inline icons and where they appear:

| Icon | Location | Purpose |
|------|----------|---------|
| Right arrow (`M17 8l4 4m0 0l-4 4m4-4H3`) | Buttons, "Mehr erfahren" links | Indicates navigation/action |
| Down arrow (`M19 14l-7 7m0 0l-7-7m7 7V3`) | Hero scroll indicator | Indicates "scroll down" |
| External link (`M10 6H6a2...`) | Reference company names | Indicates link opens externally |
| Hamburger (`M4 6h16M4 12h16M4 18h16`) | Mobile menu button (closed) | Menu toggle |
| Close X (`M6 18L18 6M6 6l12 12`) | Mobile menu button (open) | Menu toggle |
| Location pin | Contact page | Address |
| Phone | Contact page | Phone numbers |
| Envelope | Contact page | Email |
| Clock | Contact page | Business hours |
| LinkedIn logo | Footer | Social link |
| GitHub logo | Footer | Social link |

---

## 10. Meta Tags & SEO

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

## 11. design-v2 Branch Differences

The `design-v2` branch introduces a **light/dark hybrid theme** -- content sections use white backgrounds while the hero, footer, and CTA sections remain dark. This section documents all differences from the `master` branch.

**Branch:** `design-v2` (available locally and on remote)

### 11.1 New Color Tokens

The `design-v2` branch adds several new color tokens to `resources/css/app.css` and adjusts existing dark values:

| Token | Hex | Purpose |
|-------|-----|---------|
| `hilotec-dark` | `#0C1222` | Changed from `#0A0A0A` -- slightly blue-tinted navy instead of pure black |
| `hilotec-darker` | `#080E1A` | Changed from `#050505` -- matches the new navy palette |
| `hilotec-surface` | `#111827` | Changed from `#111318` -- standard Tailwind gray-900 |
| `hilotec-light` | `#FAFAFA` | **New.** Light content section backgrounds (replaces `hilotec-dark` in main body) |
| `hilotec-light-alt` | `#F3F4F6` | **New.** Slightly darker light background for alternating sections |
| `hilotec-white` | `#FFFFFF` | **New.** Pure white for card backgrounds |
| `hilotec-blue-light` | `#3B82F6` | **New.** Lighter blue variant |
| `hilotec-text` | `#1F2937` | **New.** Primary text on light backgrounds (dark gray, almost black) |
| `hilotec-text-light` | `#6B7280` | **New.** Secondary text on light backgrounds |
| `hilotec-text-muted` | `#9CA3AF` | **New.** Muted text on light backgrounds |
| `hilotec-border` | `#E5E7EB` | **New.** Borders on light backgrounds |
| `hilotec-border-dark` | `#1E293B` | **New.** Borders on dark backgrounds |

### 11.2 Body Defaults Change

The base body style changes from dark to light:

```css
/* master */
body { @apply font-body bg-hilotec-dark text-white antialiased; }

/* design-v2 */
body { @apply font-body bg-hilotec-light text-hilotec-text antialiased; }
```

This means content sections default to a light background with dark text, unless explicitly overridden.

### 11.3 New Utility Classes

The `design-v2` branch adds several CSS utility classes in `@layer utilities`:

**Glassmorphism (frosted glass effect):**

```css
.glass {
    background: rgba(12, 18, 34, 0.85);
    backdrop-filter: blur(12px);
}
.glass-light {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
}
```

Used in the header (replaces the `bg-hilotec-dark/95 backdrop-blur-sm` on scroll) and the footer CTA card.

**Scroll reveal animations:**

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

Elements with the `reveal` class start invisible and 16px below their final position. When they scroll into view, JavaScript adds the `revealed` class, triggering a smooth fade-in and slide-up animation.

**Stagger delays for sequenced animations:**

```css
.stagger-1 { transition-delay: 0.1s; }
.stagger-2 { transition-delay: 0.2s; }
/* ... up to .stagger-8 (0.8s) */
```

When combined with `reveal`, stagger classes make elements animate in sequence (e.g., heading first, then subheading, then button).

**Gold accent bar:**

```css
.gold-bar-left::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, #d4a843, #e4be5a);
}
```

A thin gold vertical bar on the left edge of service cards.

**Glow effects for buttons:**

```css
.glow-blue { box-shadow: 0 4px 24px rgba(37, 99, 235, 0.3); }
.glow-gold { box-shadow: 0 4px 24px rgba(212, 168, 67, 0.25); }
```

Adds a colored shadow behind buttons for a "glow" effect.

**Card elevation:**

```css
.card-elevated {
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
}
.card-elevated:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08), 0 8px 32px rgba(0,0,0,0.06);
}
```

Replaces the dark-surface + border card style with subtle shadows on white cards.

### 11.4 JavaScript Changes

The `design-v2` branch extends `resources/js/app.js` with:

**IntersectionObserver for scroll reveal:**

```js
document.addEventListener('DOMContentLoaded', () => {
    const revealElements = document.querySelectorAll('.reveal');
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

This watches all `.reveal` elements and adds `.revealed` when they become 10% visible. The `-40px` bottom margin triggers the animation slightly before the element fully enters the viewport. Each element is unobserved after revealing (one-time animation).

**Alpine.js Intersect plugin:** Added for Alpine-level intersection observers.

**Animated counter component:** A reusable Alpine component for animating numbers from 0 to a target value with ease-out cubic easing.

### 11.5 Component Changes Summary

| Component | master | design-v2 |
|-----------|--------|-----------|
| **Header** | `bg-hilotec-dark/95` on scroll | `glass` class (glassmorphism). Active nav indicator changes from dot to full-width gold underline. Mobile active items get a gold left border. |
| **Hero** | Flat dark overlay | Gradient overlay (`from-hilotec-dark/70 via-.../50 to-.../80`). Optional grid pattern overlay on full-height. New `badge` prop for gold pill label. SVG wave divider at bottom. Scroll indicator redesigned as mouse-wheel shape. `reveal`+`stagger-*` on content. |
| **Button** | `rounded-lg`, no glow | `rounded-xl`, `glow-blue`/`glow-gold` shadows, `hover:scale-[1.02]` press effect. New `outline-dark` variant (dark border on light bg). |
| **Section Heading** | `light` defaults to `true` | `light` defaults to `false` (dark text on light bg). New `label` prop for gold pill label above title. `reveal` class added. |
| **Service Card** | Dark surface bg, white/gold text | White bg, `card-elevated` shadow, `gold-bar-left` accent, amber icon container, `reveal` class. Text uses `hilotec-text` tokens. |
| **Post Card** | Dark surface bg | White bg, `card-elevated` shadow, `reveal` class. Hover changes title to blue instead of gold. |
| **Reference Item** | `border-white/5` divider | `border-hilotec-border` divider. Text uses `hilotec-text` tokens. Hover uses blue instead of gold. |
| **Footer CTA** | Gold card on background image | Glassmorphic card (`glass` class) on dark gradient. SVG wave divider at top. Dot pattern overlay. CTA button changes from blue to gold. |
| **Footer** | Standard `pt-16` | Gold gradient top border (`h-px bg-gradient-to-r from-transparent via-hilotec-gold to-transparent`). Social icons in circular borders. Back-to-top button added. Text colors use standard Tailwind grays. |

### 11.6 Prose for Light Backgrounds

A new `prose-light` component class provides custom typography colors for CMS content on light backgrounds:

```css
.prose-light {
    --tw-prose-body: #374151;
    --tw-prose-headings: #111827;
    --tw-prose-links: #2563eb;
    --tw-prose-bullets: #d4a843;    /* gold bullets */
    --tw-prose-quote-borders: #d4a843;  /* gold quote borders */
    /* ... */
}
```

---

## 12. How to Add a New Page

This section walks through creating a new page from scratch, using existing components. As an example, we will create a "Partner" page at `/partner`.

### Step 1: Create the Route

Open `routes/web.php` and add a route. Place it **above** the catch-all route at the bottom.

```php
// In routes/web.php, before the catch-all:
Route::get('/partner', [PartnerController::class, 'index'])->name('partner');

// This must remain last:
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');
```

### Step 2: Create the Controller

Create `app/Http/Controllers/PartnerController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Partner;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::published()->orderBy('sort_order')->get();

        return view('pages.partner', compact('partners'));
    }
}
```

### Step 3: Create the Blade Template

Create `resources/views/pages/partner.blade.php`:

```blade
<x-layout title="Partner" metaDescription="Unsere Technologiepartner und Kooperationen.">
    {{-- Hero --}}
    <x-hero
        heading="Unsere Partner"
        subheading="Technologiepartner, auf die wir setzen"
        image="heroes/inner_page_hero_bg.jpg"
    />

    {{-- Partner List Section --}}
    <section class="py-20 bg-hilotec-dark">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading
                title="Technologiepartner"
                subtitle="Wir arbeiten mit fuhrenden Herstellern zusammen."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($partners as $partner)
                    <div class="bg-hilotec-surface border border-white/5 rounded-xl p-6">
                        <h3 class="text-lg font-heading font-semibold text-white mb-3">
                            {{ $partner->name }}
                        </h3>
                        <p class="text-hilotec-gray text-sm leading-relaxed">
                            {{ $partner->description }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Optional second section with alternating background --}}
    <section class="py-20 bg-hilotec-surface">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Additional content --}}
        </div>
    </section>
</x-layout>
```

### Step 4: Add Navigation (Optional)

If the page should appear in the main navigation, edit the `$navItems` array in `resources/views/components/header.blade.php`:

```php
$navItems = [
    ['label' => 'Home', 'route' => 'home', 'url' => '/'],
    ['label' => 'Angebot', 'route' => 'services.index', 'url' => '/angebot'],
    ['label' => 'Partner', 'route' => 'partner', 'url' => '/partner'],  // new
    // ... rest of items
];
```

Also add the link to the footer navigation in `resources/views/components/footer.blade.php`.

### Step 5: Build and Test

```bash
npm run build        # Recompile CSS/JS (picks up new Tailwind classes)
php artisan serve    # Start the dev server
```

Visit `http://localhost:8000/partner` to see the new page.

### Checklist for New Pages

- [ ] Route added in `routes/web.php` (above the catch-all)
- [ ] Controller created in `app/Http/Controllers/`
- [ ] Blade template created in `resources/views/pages/`
- [ ] `<x-layout>` wraps the page with `title` and `metaDescription`
- [ ] `<x-hero>` is the first element inside the layout
- [ ] Content sections use `py-20` padding and the standard container
- [ ] Sections alternate between `bg-hilotec-dark` and `bg-hilotec-surface`
- [ ] Color tokens from the design system are used (not raw hex values)
- [ ] Headings use `font-heading`, body text uses `font-body` (automatic via base styles)
- [ ] Grids are responsive (`grid-cols-1 md:grid-cols-2 lg:grid-cols-3`)
- [ ] `npm run build` executed to compile new Tailwind classes
- [ ] Navigation updated in header and footer if the page should appear there
