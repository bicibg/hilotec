# SEO Documentation

This guide covers Search Engine Optimization for the HILOTEC corporate website. It is written for sysadmins and IT generalists -- you do not need prior SEO experience to follow along.

**What is SEO?** Search Engine Optimization is the practice of making your website easy for Google (and other search engines) to find, understand, and rank. Better SEO means more potential customers discover HILOTEC when searching for IT services in the Emmental region.

---

## Table of Contents

1. [Current SEO State](#1-current-seo-state)
2. [Meta Tags](#2-meta-tags)
3. [Open Graph Tags](#3-open-graph-tags)
4. [URL Structure](#4-url-structure)
5. [Content Strategy](#5-content-strategy)
6. [sitemap.xml](#6-sitemapxml)
7. [robots.txt](#7-robotstxt)
8. [Structured Data / JSON-LD](#8-structured-data--json-ld)
9. [Image SEO](#9-image-seo)
10. [Performance & Core Web Vitals](#10-performance--core-web-vitals)
11. [GDPR & Analytics](#11-gdpr--analytics)
12. [SEO Checklist](#12-seo-checklist)
13. [Monitoring & Tools](#13-monitoring--tools)

---

## 1. Current SEO State

### What is already implemented

The site has a solid foundation for SEO:

| Feature | Status | Details |
|---------|--------|---------|
| Server-side rendering | Done | Laravel + Blade templates -- Google sees the full HTML immediately, no JavaScript rendering required |
| `<html lang="de">` | Done | Tells search engines the page language is German |
| Dynamic `<title>` tags | Done | Each page sets its own title via the layout component |
| Meta description | Done | Passed per-page through `metaDescription` prop |
| Open Graph tags | Done | `og:title`, `og:description`, `og:image`, `og:type` are present |
| SEO-friendly URLs | Done | German slugs (`/angebot`, `/referenzen`, `/ueber-uns`) |
| Favicon set | Done | ICO, PNG 32x32, Apple Touch Icon 180x180 |
| Security headers | Done | `Referrer-Policy: strict-origin-when-cross-origin` preserves analytics data |
| Admin panel hidden | Done | `X-Robots-Tag: noindex, nofollow` on all `/admin/*` routes |
| Cache-busted assets | Done | Vite generates content-hashed filenames (`app-Bx4f2k.css`) |
| HTTPS enforcement | Done | HSTS header with `includeSubDomains; preload` in production |

### What is missing

| Feature | Priority | Section |
|---------|----------|---------|
| Canonical URL tags | High | [Meta Tags](#2-meta-tags) |
| `og:url` and `og:locale` | High | [Open Graph Tags](#3-open-graph-tags) |
| Twitter Card meta tags | Medium | [Open Graph Tags](#3-open-graph-tags) |
| `sitemap.xml` | High | [sitemap.xml](#6-sitemapxml) |
| Improved `robots.txt` | Medium | [robots.txt](#7-robotstxt) |
| Structured data (JSON-LD) | High | [Structured Data](#8-structured-data--json-ld) |
| Image lazy loading | Medium | [Image SEO](#9-image-seo) |
| Image alt text audit | Medium | [Image SEO](#9-image-seo) |
| Self-hosted Google Fonts | High | [GDPR & Analytics](#11-gdpr--analytics) |
| Cookie-free analytics | Medium | [GDPR & Analytics](#11-gdpr--analytics) |
| Meta description on homepage | High | [Meta Tags](#2-meta-tags) |

---

## 2. Meta Tags

### How meta tags work in this project

The layout component (`resources/views/components/layout.blade.php`) accepts three SEO-related props:

```blade
@props(['title' => null, 'metaDescription' => null, 'metaImage' => null, 'fullHero' => false])
```

These generate the `<head>` section:

```html
<title>{{ $title ? $title . ' — ' . setting('general.company_name') : setting('general.company_name') }}</title>

@if($metaDescription)
    <meta name="description" content="{{ $metaDescription }}">
@endif
```

### How each page sets its meta tags

Pages pass values through the `<x-layout>` component:

```blade
{{-- Static meta (e.g., services/index.blade.php) --}}
<x-layout title="Angebot" metaDescription="IT-Dienstleistungen für KMU: Infrastruktur, Sicherheit, ...">

{{-- Dynamic meta from database (e.g., generic.blade.php) --}}
<x-layout
    title="{{ $page->meta_title ?? $page->title }}"
    metaDescription="{{ $page->meta_description }}"
>

{{-- Dynamic meta from model (e.g., posts/show.blade.php) --}}
<x-layout title="{{ $post->title }}" metaDescription="{{ $post->excerpt }}">
```

### Current gaps and fixes

**Problem 1: Homepage has no meta description.** The home page (`resources/views/pages/home.blade.php`) uses `<x-layout>` without any props:

```blade
{{-- Current: no title, no description --}}
<x-layout>
```

**Fix:** Add title and description:

```blade
<x-layout
    title="IT-Dienstleistungen für KMU"
    metaDescription="HILOTEC Engineering + Consulting AG — Ihr IT-Partner im Emmental. Infrastruktur, Cloud, Sicherheit, Software und VoIP für KMU."
>
```

Alternatively, store the homepage meta description in the settings panel so it can be edited without code changes.

**Problem 2: No canonical URL tag.** A canonical tag tells Google which URL is the "official" version of a page. Without it, Google might index both `https://hilotec.com/angebot` and `https://www.hilotec.com/angebot` as separate pages (duplicate content).

**Fix:** Add this to `resources/views/components/layout.blade.php` inside `<head>`:

```blade
<link rel="canonical" href="{{ url()->current() }}">
```

This uses Laravel's `url()->current()` helper, which returns the clean URL without query parameters.

**Problem 3: Posts have no dedicated `meta_title` or `meta_description` fields.** The Post model uses `title` for `<title>` and `excerpt` for the meta description. This works but limits control -- sometimes you want a different title for Google than what shows on the page.

**Fix (optional):** Add `meta_title` and `meta_description` columns to the `posts` table, similar to the `pages` table:

```bash
php artisan make:migration add_meta_fields_to_posts_table
```

```php
// Migration
Schema::table('posts', function (Blueprint $table) {
    $table->string('meta_title')->nullable()->after('title');
    $table->text('meta_description')->nullable()->after('excerpt');
});
```

Then update the Blade template:

```blade
<x-layout
    title="{{ $post->meta_title ?? $post->title }}"
    metaDescription="{{ $post->meta_description ?? $post->excerpt }}"
>
```

### Meta tag best practices

- **Title**: Keep under 60 characters. Format: `Page Title — HILOTEC Engineering + Consulting AG`
- **Description**: Keep between 120-160 characters. Include a call to action or value proposition.
- **One H1 per page**: Each page already has a single `<h1>` in the hero component. Do not add additional `<h1>` tags.

---

## 3. Open Graph Tags

### What are Open Graph tags?

When someone shares a link to your website on LinkedIn, WhatsApp, or Teams, these platforms look for Open Graph (OG) tags to generate a preview card with a title, description, and image. Without them, the preview looks broken or empty.

### Current implementation

In `resources/views/components/layout.blade.php`:

```html
<meta property="og:title" content="{{ $title ?? setting('general.company_name') }}">
@if($metaDescription)
    <meta property="og:description" content="{{ $metaDescription }}">
@endif
<meta property="og:image" content="{{ asset($metaImage ?? 'images/meta/og_image.jpg') }}">
<meta property="og:type" content="website">
```

### Missing tags

Add these to the `<head>` in `layout.blade.php`:

```blade
{{-- Open Graph: URL and locale --}}
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="de_CH">
<meta property="og:site_name" content="{{ setting('general.company_name') }}">

{{-- Twitter Card tags (used by X/Twitter, Slack, Discord, and others) --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title ?? setting('general.company_name') }}">
@if($metaDescription)
    <meta name="twitter:description" content="{{ $metaDescription }}">
@endif
<meta name="twitter:image" content="{{ asset($metaImage ?? 'images/meta/og_image.jpg') }}">
```

**Why `de_CH`?** HILOTEC is a Swiss company. The locale `de_CH` tells platforms this is Swiss German content. This is more accurate than just `de` or `de_DE`.

### OG image guidelines

- **Recommended size**: 1200 x 630 pixels
- **Format**: JPG or PNG, under 1 MB
- **Content**: Company logo, tagline, brand colors
- The default image is `public/images/meta/og_image.jpg` -- make sure it exists and looks good when shared
- For blog posts, consider using `featured_image` as the OG image:

```blade
{{-- In posts/show.blade.php --}}
<x-layout
    title="{{ $post->title }}"
    metaDescription="{{ $post->excerpt }}"
    metaImage="{{ $post->featured_image ? 'storage/' . $post->featured_image : null }}"
>
```

### Testing Open Graph tags

Use these free tools to preview how your links appear when shared:

- [Facebook Sharing Debugger](https://developers.facebook.com/tools/debug/) -- also forces Facebook to re-scrape a URL
- [LinkedIn Post Inspector](https://www.linkedin.com/post-inspector/)
- [Twitter Card Validator](https://cards-dev.twitter.com/validator)

---

## 4. URL Structure

### Current route map

The site uses clean, descriptive German slugs. This is ideal for a Swiss German audience -- Google favors URLs that match the language of the content.

| URL | Route Name | Controller | Description |
|-----|-----------|------------|-------------|
| `/` | `home` | `HomeController@index` | Homepage |
| `/angebot` | `services.index` | `ServiceController@index` | All services |
| `/angebot/{slug}` | `services.show` | `ServiceController@show` | Single service |
| `/referenzen` | `references.index` | `ReferenceController@index` | References |
| `/ueber-uns` | `about` | `AboutController@index` | About page |
| `/aktuelles` | `posts.index` | `PostController@index` | Blog/news list |
| `/aktuelles/{slug}` | `posts.show` | `PostController@show` | Single post |
| `/kontakt` | `contact` | `ContactController@index` | Contact page |
| `/{slug}` | `pages.show` | `PageController@show` | Generic pages (Impressum, Datenschutz) |

### Why this is good for SEO

1. **No ID numbers in URLs**: `/angebot/cloud-loesungen` is better than `/services/42`
2. **Human-readable**: both users and Google can tell what the page is about from the URL alone
3. **Flat hierarchy**: no deeply nested URLs like `/de/services/category/it/cloud-loesungen`
4. **Consistent slug pattern**: all slugs use lowercase with hyphens

### URL best practices

- **Never change a URL without a redirect.** If you rename a service slug from `cloud-loesungen` to `cloud-services`, add a 301 redirect in `routes/web.php`:

```php
Route::redirect('/angebot/cloud-loesungen', '/angebot/cloud-services', 301);
```

- **Keep slugs short and descriptive**: `netzwerk-infrastruktur` is better than `netzwerk-und-infrastruktur-dienstleistungen-fuer-kmu`
- **Use only lowercase letters, numbers, and hyphens** in slugs -- no umlauts in URLs (`ueber-uns` not `über-uns`)

---

## 5. Content Strategy

### Using the admin panel for SEO-optimized content

All site content is managed through the Filament admin panel at `/admin`. This means SEO improvements can be made without touching code.

### Pages (Impressum, Datenschutz, etc.)

The `Page` model includes dedicated SEO fields:

- **meta_title**: Appears in the browser tab and Google search results. If left empty, falls back to the page `title`.
- **meta_description**: The 1-2 sentence summary shown under the title in Google results.

When editing a page in the admin panel, always fill in both fields.

### Services

The `Service` model has `title`, `slug`, and `excerpt`. The excerpt doubles as the meta description on service detail pages. Write excerpts that:

- Contain the primary keyword (e.g., "VoIP-Telefonie", "Cloud-Lösungen")
- Stay between 120-160 characters
- Describe what HILOTEC offers, not just what the service is

**Good excerpt**: "Professionelle VoIP-Telefonie für KMU: Wir planen, installieren und betreuen Ihre IP-Telefonanlage im Emmental und Umgebung."

**Weak excerpt**: "VoIP-Telefonie"

### Blog posts (Aktuelles)

Each blog post should:

1. Have a descriptive `title` that includes the primary keyword
2. Have an `excerpt` (120-160 characters) that summarizes the article and entices clicks
3. Have a `featured_image` with meaningful alt text
4. Use proper heading hierarchy in the body (`<h2>`, `<h3>` -- never `<h1>`, which is reserved for the page title)

### Content tips for IT companies

- Write about topics your customers search for: "Server-Migration Windows Server 2025", "Microsoft 365 Backup-Lösung", "IT-Sicherheit KMU Schweiz"
- Use the "Aktuelles" section for technical articles -- this builds authority and generates long-tail search traffic
- Aim for at least 300 words per service page and 500+ words per blog post
- Include location terms naturally: "Emmental", "Bern", "Schweiz", "KMU"

---

## 6. sitemap.xml

### What is a sitemap?

A sitemap is an XML file that lists all the public URLs on your website. You submit it to Google Search Console so Google knows exactly which pages to crawl. Without one, Google has to discover pages by following links -- which can miss pages or take longer.

### Current state

There is no `sitemap.xml` file. This should be added.

### Option A: Use spatie/laravel-sitemap (recommended)

This package automatically crawls your site and generates a sitemap.

```bash
composer require spatie/laravel-sitemap
```

**Generate on demand:**

```bash
php artisan sitemap:generate
```

**Or create an Artisan command for a dynamic sitemap** (`app/Console/Commands/GenerateSitemap.php`):

```php
<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate the sitemap.xml file';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        // Static pages
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency('weekly'));
        $sitemap->add(Url::create('/angebot')->setPriority(0.9)->setChangeFrequency('weekly'));
        $sitemap->add(Url::create('/referenzen')->setPriority(0.7)->setChangeFrequency('monthly'));
        $sitemap->add(Url::create('/ueber-uns')->setPriority(0.6)->setChangeFrequency('monthly'));
        $sitemap->add(Url::create('/aktuelles')->setPriority(0.8)->setChangeFrequency('weekly'));
        $sitemap->add(Url::create('/kontakt')->setPriority(0.7)->setChangeFrequency('monthly'));

        // Services
        Service::published()->ordered()->each(function (Service $service) use ($sitemap) {
            $sitemap->add(
                Url::create("/angebot/{$service->slug}")
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
                    ->setLastModificationDate($service->updated_at)
            );
        });

        // Blog posts
        Post::published()->latest()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(
                Url::create("/aktuelles/{$post->slug}")
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
                    ->setLastModificationDate($post->updated_at)
            );
        });

        // Generic pages (Impressum, Datenschutz, etc.)
        Page::published()->each(function (Page $page) use ($sitemap) {
            // Skip pages that have their own dedicated routes
            if (in_array($page->slug, ['ueber-uns'])) {
                return;
            }
            $sitemap->add(
                Url::create("/{$page->slug}")
                    ->setPriority(0.4)
                    ->setChangeFrequency('monthly')
                    ->setLastModificationDate($page->updated_at)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $this->info('Sitemap generated at public/sitemap.xml');
    }
}
```

**Schedule it to run daily** in `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('sitemap:generate')->daily();
```

### Option B: Dynamic sitemap via route

If you prefer not to generate a static file, serve the sitemap dynamically:

```php
// routes/web.php
Route::get('/sitemap.xml', function () {
    $services = \App\Models\Service::published()->ordered()->get();
    $posts = \App\Models\Post::published()->latest()->get();
    $pages = \App\Models\Page::published()->get();

    return response()
        ->view('sitemap', compact('services', 'posts', 'pages'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
```

Create `resources/views/sitemap.blade.php`:

```xml
{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <priority>1.0</priority>
        <changefreq>weekly</changefreq>
    </url>
    <url>
        <loc>{{ url('/angebot') }}</loc>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ url('/referenzen') }}</loc>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ url('/ueber-uns') }}</loc>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{{ url('/aktuelles') }}</loc>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ url('/kontakt') }}</loc>
        <priority>0.7</priority>
    </url>
    @foreach($services as $service)
    <url>
        <loc>{{ url('/angebot/' . $service->slug) }}</loc>
        <lastmod>{{ $service->updated_at->toW3cString() }}</lastmod>
        <priority>0.8</priority>
    </url>
    @endforeach
    @foreach($posts as $post)
    <url>
        <loc>{{ url('/aktuelles/' . $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toW3cString() }}</lastmod>
        <priority>0.7</priority>
    </url>
    @endforeach
    @foreach($pages as $page)
    @if($page->slug !== 'ueber-uns')
    <url>
        <loc>{{ url('/' . $page->slug) }}</loc>
        <lastmod>{{ $page->updated_at->toW3cString() }}</lastmod>
        <priority>0.4</priority>
    </url>
    @endif
    @endforeach
</urlset>
```

**Important**: If you use Option B, make sure the dynamic route is registered *before* the catch-all `/{slug}` route in `routes/web.php`, or add it to `robots.txt` pointing to the correct URL.

### After creating the sitemap

1. Submit it to Google Search Console (see [Section 13](#13-monitoring--tools))
2. Reference it in `robots.txt` (see [Section 7](#7-robotstxt))

---

## 7. robots.txt

### What is robots.txt?

The `robots.txt` file at your site root tells search engine crawlers which parts of your site they are allowed (or not allowed) to crawl. It is not a security mechanism -- it is a politeness protocol.

### Current state

The file `public/robots.txt` contains:

```
User-agent: *
Disallow:
```

This allows all crawlers to access everything. While not harmful, it can be improved.

### Recommended robots.txt

Replace the contents of `public/robots.txt` with:

```
# HILOTEC Engineering + Consulting AG
# https://hilotec.com

User-agent: *
Allow: /

# Block admin panel (also blocked via X-Robots-Tag header)
Disallow: /admin
Disallow: /livewire

# Block Laravel internal routes
Disallow: /sanctum
Disallow: /storage/
Disallow: /_debugbar

# Sitemap location
Sitemap: https://hilotec.com/sitemap.xml
```

### What each rule does

- `Disallow: /admin` -- prevents crawlers from wasting time on the admin panel (already blocked by `X-Robots-Tag: noindex` in the SecurityHeaders middleware, but belt-and-suspenders is good practice)
- `Disallow: /livewire` -- Filament uses Livewire internally; these routes are not public content
- `Disallow: /storage/` -- prevents indexing of uploaded files directly (they should be accessed through proper page context)
- `Disallow: /_debugbar` -- blocks the Laravel debug bar if it is ever accidentally enabled in production
- `Sitemap:` -- tells crawlers where to find the sitemap, which speeds up discovery of new pages

---

## 8. Structured Data / JSON-LD

### What is structured data?

Structured data is a standardized format (JSON-LD) that you embed in your HTML to help Google understand your content. For a company like HILOTEC, the most important type is **LocalBusiness** -- it tells Google your company name, address, phone number, and business hours. This information can appear directly in search results as a "Knowledge Panel" or rich snippet.

### Current state

There is no structured data on the site.

### Adding LocalBusiness schema

Add this to `resources/views/components/layout.blade.php`, inside `<head>`, right before the closing `</head>` tag:

```blade
{{-- Structured Data: LocalBusiness --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "{{ setting('general.company_name') }}",
    "description": "{{ $metaDescription ?? 'IT-Dienstleistungen für KMU im Emmental' }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/branding/logo.png') }}",
    "image": "{{ asset('images/meta/og_image.jpg') }}",
    "telephone": "{{ setting('contact.phone_support_infra') }}",
    "email": "{{ setting('contact.email') }}",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ setting('contact.address_line1') }}",
        "addressLocality": "Burgdorf",
        "postalCode": "3400",
        "addressRegion": "BE",
        "addressCountry": "CH"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 47.0511,
        "longitude": 7.6261
    },
    "openingHours": "{{ setting('contact.business_hours') }}",
    "sameAs": [
        @if(setting('social.linkedin'))"{{ setting('social.linkedin') }}"@endif
        @if(setting('social.linkedin') && setting('social.github')),@endif
        @if(setting('social.github'))"{{ setting('social.github') }}"@endif
    ],
    "priceRange": "$$"
}
</script>
```

> **Note**: Replace `"latitude"`, `"longitude"`, `"addressLocality"`, and `"postalCode"` with the actual values for HILOTEC's office address. The values above are placeholders for Burgdorf, BE.

### Adding Article schema for blog posts

For blog posts, add article-specific structured data. In `resources/views/pages/posts/show.blade.php`, add a `@push('head')` block (requires adding `@stack('head')` to the layout -- see below):

First, add this to `layout.blade.php` inside `<head>`:

```blade
@stack('head')
```

Then in `resources/views/pages/posts/show.blade.php`:

```blade
@push('head')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "{{ $post->title }}",
    "description": "{{ $post->excerpt }}",
    "datePublished": "{{ $post->published_at?->toIso8601String() }}",
    "dateModified": "{{ $post->updated_at->toIso8601String() }}",
    "author": {
        "@type": "Organization",
        "name": "{{ setting('general.company_name') }}"
    },
    "publisher": {
        "@type": "Organization",
        "name": "{{ setting('general.company_name') }}",
        "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/branding/logo.png') }}"
        }
    }
    @if($post->featured_image)
    ,"image": "{{ asset('storage/' . $post->featured_image) }}"
    @endif
}
</script>
@endpush
```

### Adding Service schema

For service detail pages, in `resources/views/pages/services/show.blade.php`:

```blade
@push('head')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "{{ $service->title }}",
    "description": "{{ $service->excerpt }}",
    "provider": {
        "@type": "LocalBusiness",
        "name": "{{ setting('general.company_name') }}"
    },
    "url": "{{ url()->current() }}"
}
</script>
@endpush
```

### Validating structured data

After adding JSON-LD, test every page type with:

- [Google Rich Results Test](https://search.google.com/test/rich-results) -- shows if Google can read your structured data
- [Schema.org Validator](https://validator.schema.org/) -- checks syntax

---

## 9. Image SEO

### Current state

Images across the site are standard `<img>` tags with no lazy loading:

```html
{{-- Hero: background image via CSS --}}
<section style="background-image: url('{{ asset('images/' . $image) }}')">

{{-- Post card: standard img --}}
<img src="{{ asset('storage/' . $post->featured_image) }}"
     alt="{{ $post->title }}"
     class="w-full h-full object-cover">

{{-- Service icon: empty alt --}}
<img src="{{ asset('images/icons/' . $service->icon) }}" alt="" class="w-12 h-12">
```

### Alt text

The `alt` attribute describes an image for screen readers and search engines. Some images on the site already have alt text, but there are inconsistencies:

- Post featured images use the post title as alt text -- good
- Service icons use `alt=""` (empty) -- acceptable for decorative icons, but not ideal if the icon conveys meaning
- The header and footer logos use the company name -- good
- The hero background images have no alt text (they use CSS `background-image`) -- this is not accessible

**Recommendation**: For meaningful images, always provide descriptive alt text. For purely decorative images (dividers, background patterns), use `alt=""` to signal they should be skipped by screen readers.

### Lazy loading

Currently no images use lazy loading. This means the browser downloads all images immediately, even those far below the fold. Adding `loading="lazy"` tells the browser to only load images when they are about to scroll into view.

**Add `loading="lazy"` to all images below the fold.** Do NOT add it to the hero image or logo (these are above the fold and should load immediately).

For the post card component (`resources/views/components/post-card.blade.php`):

```blade
<img
    src="{{ asset('storage/' . $post->featured_image) }}"
    alt="{{ $post->title }}"
    loading="lazy"
    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
>
```

For team member photos (`resources/views/pages/about.blade.php`):

```blade
<img src="{{ asset('storage/' . $member->photo) }}"
     alt="{{ $member->name }}"
     loading="lazy"
     class="w-full h-full object-cover">
```

### Image format optimization

- **Use WebP** format where possible -- it is typically 25-35% smaller than JPEG at the same quality
- If you add image upload via the admin panel, consider using [spatie/laravel-medialibrary](https://github.com/spatie/laravel-medialibrary) which can auto-generate WebP variants
- For static images in `public/images/`, convert them manually:

```bash
# Install cwebp (on Ubuntu/Debian)
sudo apt install webp

# Convert a JPEG to WebP
cwebp -q 80 public/images/heroes/home_hero_bg.jpg -o public/images/heroes/home_hero_bg.webp
```

Then use the `<picture>` element to serve WebP with JPEG fallback:

```html
<picture>
    <source srcset="{{ asset('images/heroes/home_hero_bg.webp') }}" type="image/webp">
    <img src="{{ asset('images/heroes/home_hero_bg.jpg') }}" alt="..." loading="lazy">
</picture>
```

### Image sizing

Always specify `width` and `height` attributes (or use CSS `aspect-ratio`) to prevent layout shifts when images load. The post-card component already uses `aspect-video` on its container, which is good.

---

## 10. Performance & Core Web Vitals

### What are Core Web Vitals?

Google uses three performance metrics as ranking signals:

| Metric | What it measures | Target | Acronym |
|--------|-----------------|--------|---------|
| Largest Contentful Paint | How fast the main content loads | < 2.5 seconds | LCP |
| Cumulative Layout Shift | How much the page layout jumps around | < 0.1 | CLS |
| Interaction to Next Paint | How fast the page responds to clicks | < 200 ms | INP |

### What affects performance in this project

**Positives:**

- Server-side rendering (no JavaScript framework to boot)
- Vite generates optimized, minified, content-hashed CSS/JS bundles
- Tailwind CSS purges unused styles (the final CSS is small)
- Alpine.js is lightweight (~15 KB minified)
- SQLite is fast for read-heavy workloads with low traffic

**Areas to improve:**

| Issue | Impact | Fix |
|-------|--------|-----|
| Google Fonts loaded from CDN | Adds 2 extra DNS lookups + font download time (affects LCP) | Self-host fonts (see [GDPR section](#11-gdpr--analytics)) |
| Hero images not optimized | Large JPEGs slow down LCP | Compress and serve WebP (see [Image SEO](#9-image-seo)) |
| No image lazy loading | All images load at once | Add `loading="lazy"` to below-fold images |
| No `preload` for critical assets | Browser discovers fonts/images late | Add preload hints (see below) |
| CSS background-image for hero | Browser cannot preload it; discovers it only after CSS loads | Consider an `<img>` tag or add a `<link rel="preload">` |

### Preloading critical assets

Add preload hints for the most important resources in `layout.blade.php`:

```blade
{{-- Preload the hero background image for faster LCP --}}
@if(isset($heroImage))
    <link rel="preload" as="image" href="{{ asset('images/' . $heroImage) }}">
@endif
```

This requires passing the hero image path up to the layout. Alternatively, for the homepage:

```blade
{{-- Only on homepage --}}
@if(request()->routeIs('home'))
    <link rel="preload" as="image" href="{{ asset('images/heroes/home_hero_bg.jpg') }}">
@endif
```

### Server-side performance

- Enable Laravel's route caching in production: `php artisan route:cache`
- Enable config caching: `php artisan config:cache`
- Enable view caching: `php artisan view:cache`
- Consider adding HTTP caching headers for static pages (the SecurityHeaders middleware could set `Cache-Control: public, max-age=3600` for non-admin pages)

---

## 11. GDPR & Analytics

### Google Fonts: the GDPR problem

The current site loads fonts directly from Google's CDN:

```html
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:...&family=Sora:...&display=swap"
      rel="stylesheet">
```

**The problem**: When a visitor loads your page, their browser sends a request to `fonts.googleapis.com` and `fonts.gstatic.com`. This transmits the visitor's IP address to Google -- a US company. Under Swiss data protection law (nDSG, effective since September 2023) and EU GDPR, this constitutes a transfer of personal data to a third country without consent. Several European courts (notably a German court in Munich, January 2022) have ruled this is illegal without prior user consent.

**For a Swiss IT company, this is a credibility issue.** Clients expect you to understand data protection.

### Fix: Self-host Google Fonts

1. **Download the fonts** using [google-webfonts-helper](https://gwfh.mranftl.com/fonts) or download them directly from Google Fonts.

2. **Place font files in your project**:

```
public/fonts/
    sora-400.woff2
    sora-500.woff2
    sora-600.woff2
    sora-700.woff2
    sora-800.woff2
    dm-sans-300.woff2
    dm-sans-400.woff2
    dm-sans-500.woff2
    dm-sans-600.woff2
    dm-sans-700.woff2
    dm-sans-400-italic.woff2
    dm-sans-700-italic.woff2
```

3. **Add @font-face declarations** to `resources/css/app.css`:

```css
@font-face {
    font-family: 'Sora';
    src: url('/fonts/sora-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'Sora';
    src: url('/fonts/sora-700.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
}

/* ... repeat for each weight/style combination you use ... */
```

4. **Remove the Google Fonts `<link>` tags** from `layout.blade.php`:

```blade
{{-- DELETE these three lines --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:...&family=Sora:...&display=swap" rel="stylesheet">
```

5. **Update the CSP** in `SecurityHeaders.php` -- remove `https://fonts.googleapis.com` from `style-src` and `https://fonts.gstatic.com` from `font-src` (since fonts now come from your own server).

**Performance bonus**: Self-hosting fonts eliminates two DNS lookups and two TLS handshakes, which can save 100-300ms on the first page load.

### Cookie-free analytics options

If you want to understand how visitors use the site without adding cookie banners, consider these GDPR-friendly alternatives to Google Analytics:

| Tool | Hosting | Price | Cookie-free | Notes |
|------|---------|-------|-------------|-------|
| [Plausible](https://plausible.io/) | EU cloud or self-hosted | From EUR 9/mo | Yes | Lightweight script (~1 KB), EU-based |
| [Fathom](https://usefathom.com/) | EU-available | From $14/mo | Yes | Simple dashboard, no consent needed |
| [Umami](https://umami.is/) | Self-hosted (free) | Free | Yes | Open source, can run on your own server |
| [Matomo](https://matomo.org/) | Self-hosted | Free | Configurable | More complex but full-featured; can be configured cookie-free |

**Recommended for HILOTEC: Plausible or Umami.** Both are cookie-free, respect visitor privacy, and do not require a cookie consent banner. For a Swiss IT company that handles client infrastructure, this sends the right message about data protection.

### Adding Plausible (example)

```blade
{{-- In layout.blade.php, inside <head> --}}
@production
    <script defer data-domain="hilotec.com" src="https://plausible.io/js/script.js"></script>
@endproduction
```

Update the CSP in `SecurityHeaders.php`:

```php
"script-src 'self' https://plausible.io",
"connect-src 'self' https://plausible.io",
```

---

## 12. SEO Checklist

Use this checklist to track implementation progress. Items are ordered by priority.

### High priority

- [ ] **Add canonical URL tag** to `layout.blade.php` -- `<link rel="canonical" href="{{ url()->current() }}">`
- [ ] **Self-host Google Fonts** -- download, add `@font-face`, remove CDN links (GDPR compliance)
- [ ] **Add meta description to homepage** -- the homepage currently has no `<meta name="description">`
- [ ] **Generate sitemap.xml** -- install `spatie/laravel-sitemap` or create a manual sitemap
- [ ] **Add LocalBusiness JSON-LD** -- structured data for Google Knowledge Panel
- [ ] **Submit sitemap to Google Search Console** -- register the site and submit the sitemap URL
- [ ] **Add `og:url`, `og:locale`, `og:site_name`** to layout

### Medium priority

- [ ] **Add Twitter Card meta tags** -- `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`
- [ ] **Add `loading="lazy"` to below-fold images** -- post cards, team photos, reference logos
- [ ] **Improve robots.txt** -- block `/admin`, `/livewire`, `/storage/`, reference sitemap
- [ ] **Add Article JSON-LD to blog posts** -- structured data for news articles
- [ ] **Add Service JSON-LD to service pages** -- structured data for service offerings
- [ ] **Optimize images** -- compress JPEGs, generate WebP alternatives
- [ ] **Add `@stack('head')` to layout** -- enables per-page structured data injection
- [ ] **Set up cookie-free analytics** -- Plausible, Umami, or Fathom

### Low priority (nice to have)

- [ ] **Add `meta_title` and `meta_description` to Post model** -- allows SEO-specific titles separate from display titles
- [ ] **Preload hero images** -- add `<link rel="preload">` for LCP improvement
- [ ] **Convert hero images to WebP** -- 25-35% smaller file sizes
- [ ] **Add breadcrumb structured data** -- helps Google display breadcrumb navigation in search results
- [ ] **Set up 301 redirects** for any renamed slugs
- [ ] **Audit alt text** on all images -- ensure every meaningful image has descriptive alt text

---

## 13. Monitoring & Tools

### Google Search Console (essential)

Google Search Console (GSC) is a free tool from Google that shows you how your site appears in search results. It is the single most important SEO tool.

**Setup:**

1. Go to [search.google.com/search-console](https://search.google.com/search-console)
2. Click "Add property" and enter `https://hilotec.com`
3. Verify ownership via DNS TXT record (recommended for sysadmins):
   - Add a TXT record to `hilotec.com` with the value Google provides
   - This is the most reliable method and does not require code changes
4. After verification, submit your sitemap: Sitemaps > Add `https://hilotec.com/sitemap.xml`

**What to monitor:**

- **Coverage report**: Shows which pages Google has indexed and any errors (404s, server errors)
- **Performance report**: Shows which search queries bring visitors, click-through rates, and average position
- **Core Web Vitals**: Shows real-world performance data from Chrome users
- **Manual actions**: Alerts if Google has penalized your site (rare, but important to check)

### Google Lighthouse

Lighthouse is built into Chrome DevTools and scores your page on Performance, Accessibility, Best Practices, and SEO.

**How to run:**

1. Open your site in Chrome
2. Press F12 (DevTools) > "Lighthouse" tab
3. Select "Navigation" mode, check all categories
4. Click "Analyze page load"

**Target scores:**

- Performance: 90+
- Accessibility: 90+
- Best Practices: 90+
- SEO: 90+ (should be easy to achieve with the fixes in this doc)

### Other useful tools

| Tool | Purpose | URL |
|------|---------|-----|
| PageSpeed Insights | Performance testing with real-world data | [pagespeed.web.dev](https://pagespeed.web.dev/) |
| Rich Results Test | Validate structured data / JSON-LD | [search.google.com/test/rich-results](https://search.google.com/test/rich-results) |
| Ahrefs Webmaster Tools | Free backlink checker and site audit | [ahrefs.com/webmaster-tools](https://ahrefs.com/webmaster-tools) |
| Screaming Frog SEO Spider | Desktop crawler that finds broken links, missing meta tags, etc. (free up to 500 URLs) | [screamingfrog.co.uk](https://www.screamingfrog.co.uk/seo-spider/) |
| Facebook Sharing Debugger | Test Open Graph previews | [developers.facebook.com/tools/debug](https://developers.facebook.com/tools/debug/) |
| LinkedIn Post Inspector | Test how links appear on LinkedIn | [linkedin.com/post-inspector](https://www.linkedin.com/post-inspector/) |

### Ongoing SEO maintenance

SEO is not a one-time task. Schedule these recurring activities:

| Task | Frequency | Tool |
|------|-----------|------|
| Check Google Search Console for errors | Weekly | GSC |
| Review search performance (queries, CTR) | Monthly | GSC |
| Run Lighthouse audit | Monthly | Chrome DevTools |
| Publish new blog content | 1-2x per month | Filament Admin |
| Check for broken links | Quarterly | Screaming Frog |
| Review and update meta descriptions | Quarterly | Filament Admin |
| Re-generate sitemap (if not automated) | After content changes | Artisan command |
