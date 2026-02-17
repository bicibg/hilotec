{{--
    Main Layout Component
    Usage: <x-layout title="Page Title">...content...</x-layout>
    Props:
    - title (string, optional) — Page title suffix after company name
    - metaDescription (string, optional) — Meta description for SEO
    - metaImage (string, optional) — OG image path
    - fullHero (bool, optional) — Whether the page has a full-height hero (affects header style)
--}}
@props(['title' => null, 'metaDescription' => null, 'metaImage' => null, 'fullHero' => false])

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

    <meta property="og:title" content="{{ $title ?? setting('general.company_name') }}">
    @if($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:image" content="{{ asset($metaImage ?? 'images/meta/og_image.jpg') }}">
    <meta property="og:type" content="website">

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/meta/favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/meta/apple-touch-icon.png') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
