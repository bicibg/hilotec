# Design System

The HILOTEC website uses a consistent design system built with Tailwind CSS 4 custom tokens and reusable Blade components.

## Color Palette

All colors are defined in `resources/css/app.css` via `@theme`.

| Token | Hex | Tailwind Class | Usage |
|-------|-----|---------------|-------|
| `hilotec-dark` | `#0a0a0a` | `bg-hilotec-dark` | Primary page background |
| `hilotec-darker` | `#050505` | `bg-hilotec-darker` | Footer background |
| `hilotec-surface` | `#111318` | `bg-hilotec-surface` | Card backgrounds, alternating sections |
| `hilotec-gold` | `#d4a843` | `text-hilotec-gold` | Accent color, active nav, headings in footer |
| `hilotec-gold-dark` | `#b8922e` | `hover:bg-hilotec-gold-dark` | Gold hover state |
| `hilotec-gold-light` | `#e4be5a` | `hover:text-hilotec-gold-light` | Gold light variant |
| `hilotec-blue` | `#2563eb` | `bg-hilotec-blue` | Primary CTA buttons |
| `hilotec-blue-dark` | `#1d4ed8` | `hover:bg-hilotec-blue-dark` | Blue hover state |
| `hilotec-gray` | `#9ca3af` | `text-hilotec-gray` | Body text on dark backgrounds |
| `hilotec-gray-light` | `#d1d5db` | `text-hilotec-gray-light` | Emphasized secondary text |
| `hilotec-gray-dark` | `#4b5563` | `text-hilotec-gray-dark` | Muted text, labels |

### Design Rules
- **Gold on dark backgrounds**: Use for headings, accents, active states
- **On gold backgrounds**: Always use **black text** (not white — poor contrast)
- **Blue**: Reserved for primary CTA buttons only
- **White text**: Main headings and emphasized content on dark backgrounds

## Typography

| Token | Font Family | Tailwind Class | Usage |
|-------|-------------|---------------|-------|
| `font-heading` | Sora | `font-heading` | All headings (h1–h6), buttons, nav |
| `font-body` | DM Sans | `font-body` | Body text, paragraphs (default via `<body>`) |

Fonts are loaded from Google Fonts CDN via `<link>` tags in the layout component.

### Heading Scale (approximate)

| Element | Classes | Size |
|---------|---------|------|
| Hero heading (full) | `text-4xl md:text-5xl lg:text-6xl font-bold italic` | 36–60px |
| Hero heading (compact) | `text-4xl md:text-5xl lg:text-6xl font-bold` | 36–60px |
| Section heading | `text-3xl md:text-4xl font-bold` | 30–36px |
| Card heading | `text-lg font-semibold` | 18px |
| Footer column heading | `text-sm font-semibold uppercase tracking-wider` | 14px |

## Layout

- **Max content width:** 1280px (`max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8`)
- **Sections:** Alternate between `bg-hilotec-dark` and `bg-hilotec-surface` for visual separation
- **Section padding:** `py-20` (80px top/bottom)
- **Grid:** Responsive using Tailwind grid (`grid-cols-1 md:grid-cols-2 lg:grid-cols-3` etc.)

## Blade Components

All components are in `resources/views/components/`. Each has a comment block documenting its props.

### `<x-layout>`
Main page layout wrapper. Includes `<head>`, header, footer CTA, and footer.

```blade
<x-layout title="Page Title" metaDescription="SEO description">
    {{-- Page content --}}
</x-layout>
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | null | Page title (appended to company name) |
| `metaDescription` | string | null | Meta description for SEO |
| `metaImage` | string | null | OG image path |

### `<x-header>`
Sticky site header with transparent-to-solid scroll transition (Alpine.js). Logo left, navigation right. Mobile hamburger menu at `md` breakpoint.

No props — self-contained component.

### `<x-footer>`
5-column footer grid: Logo, Fernwartung, Navigation, Anschrift, Kontakt. Gold column headings. Social links and copyright bar at bottom.

No props — reads all data from `setting()` helper.

### `<x-footer-cta>`
Gold card CTA section that appears above the footer on every page. Matrix rain background image.

No props — reads heading and button from `setting('footer.*')`.

### `<x-hero>`
Full-width hero section with background image, overlay, and content.

```blade
{{-- Full-height hero (Home, Über uns) --}}
<x-hero
    heading="Sichere IT, die einfach funktioniert."
    subheading="Alles was Ihr KMU braucht."
    image="heroes/home_hero_bg.jpg"
    ctaText="Kontakt aufnehmen"
    ctaUrl="/kontakt"
    :fullHeight="true"
/>

{{-- Compact hero (inner pages) --}}
<x-hero heading="Referenzen" image="heroes/inner_page_hero_bg.jpg" />
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `heading` | string | *required* | Main heading text |
| `subheading` | string | null | Subtitle text |
| `image` | string | `heroes/inner_page_hero_bg.jpg` | Background image path (relative to `/images/`) |
| `ctaText` | string | null | CTA button label |
| `ctaUrl` | string | null | CTA button URL |
| `fullHeight` | bool | false | Full viewport height with scroll indicator |
| `centered` | bool | false | Center-align text |

### `<x-button>`
Reusable button/link component with variant and size support.

```blade
<x-button href="/kontakt" variant="blue" size="lg">Kontakt aufnehmen</x-button>
<x-button variant="gold">Gold Button</x-button>
<x-button variant="outline">Outline Button</x-button>
<x-button type="submit">Form Submit</x-button>
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `href` | string | null | If set, renders `<a>`; otherwise `<button>` |
| `variant` | string | `blue` | `blue`, `gold`, or `outline` |
| `size` | string | `md` | `md` or `lg` |
| `type` | string | `button` | Button type (when not a link) |

### `<x-section-heading>`
Consistent section title with optional subtitle.

```blade
<x-section-heading title="Unsere Leistungen" subtitle="Optional description text" />
```

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `title` | string | *required* | Section title |
| `subtitle` | string | null | Section subtitle |
| `centered` | bool | true | Center alignment |
| `light` | bool | true | Light text on dark background |

### `<x-service-card>`
Card displaying a single service with icon, title, excerpt, and hover link.

```blade
@foreach($services as $service)
    <x-service-card :service="$service" />
@endforeach
```

| Prop | Type | Description |
|------|------|-------------|
| `service` | `App\Models\Service` | Service model instance |

### `<x-reference-item>`
Single reference entry showing company name, address, description, and optional website link.

```blade
@foreach($category->references as $reference)
    <x-reference-item :reference="$reference" />
@endforeach
```

| Prop | Type | Description |
|------|------|-------------|
| `reference` | `App\Models\Reference` | Reference model instance |

### `<x-post-card>`
Blog post preview card with optional featured image, date, title, excerpt.

```blade
@foreach($posts as $post)
    <x-post-card :post="$post" />
@endforeach
```

| Prop | Type | Description |
|------|------|-------------|
| `post` | `App\Models\Post` | Post model instance |

## Adding a New Component

1. Create `resources/views/components/my-component.blade.php`
2. Add a comment block at the top documenting props and usage
3. Use `@props([...])` to define accepted properties
4. Use design tokens (`hilotec-*` colors, `font-heading`/`font-body`) for consistency
5. The component is automatically available as `<x-my-component />`
