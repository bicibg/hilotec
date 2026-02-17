{{--
    Section Heading Component
    Usage: <x-section-heading title="Unsere Leistungen" subtitle="Optional description" />
    Props:
    - title (string) — Section title
    - subtitle (string, optional) — Section subtitle
    - centered (bool) — Center alignment, default true
    - light (bool) — Light text on dark background, default true
--}}
@props(['title', 'subtitle' => null, 'centered' => true, 'light' => true])

<div class="{{ $centered ? 'text-center' : '' }} mb-12">
    <h2 class="text-3xl md:text-4xl font-heading font-bold {{ $light ? 'text-white' : 'text-hilotec-dark' }} mb-4">
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="text-lg {{ $light ? 'text-hilotec-gray' : 'text-hilotec-gray-dark' }} max-w-2xl {{ $centered ? 'mx-auto' : '' }}">
            {{ $subtitle }}
        </p>
    @endif
</div>
