{{--
    Main Layout Component
    Usage: <x-layout title="Page Title">...content...</x-layout>
    Props:
    - title (string, optional) — Page title suffix after company name
    - metaDescription (string, optional) — Meta description for SEO
    - metaImage (string, optional) — OG image path
--}}
@props(['title' => null, 'metaDescription' => null, 'metaImage' => null])

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' — ' . setting('general.company_name') : setting('general.company_name') }}</title>

    @if($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif

    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:title" content="{{ $title ?? setting('general.company_name') }}">
    @if($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:image" content="{{ asset($metaImage ?? 'images/meta/og_image.jpg') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="de_CH">
    <meta property="og:site_name" content="{{ setting('general.company_name') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? setting('general.company_name') }}">
    @if($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    <meta name="twitter:image" content="{{ asset($metaImage ?? 'images/meta/og_image.jpg') }}">

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/meta/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/meta/apple-touch-icon.png') }}">

    {{-- Font preloads --}}
    <link rel="preload" href="{{ asset('fonts/sora/sora-v17-latin-regular.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/sora/sora-v17-latin-700.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('fonts/dm-sans/dm-sans-v17-latin-regular.woff2') }}" as="font" type="font/woff2" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            "addressLocality": "Langnau i.E.",
            "postalCode": "3550",
            "addressRegion": "BE",
            "addressCountry": "CH"
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

    @stack('head')
</head>
<body class="min-h-screen flex flex-col">
    <x-header />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-footer-cta />
    <x-footer />
</body>
</html>
