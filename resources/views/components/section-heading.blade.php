{{--
    Section Heading Component
    Usage: <x-section-heading title="Unsere Leistungen" subtitle="Optional description" />
    Props:
    - title (string) — Section title
    - subtitle (string, optional) — Section subtitle
    - centered (bool) — Center alignment, default true
    - light (bool) — Light text on dark background, default false (dark text on light bg)
    - label (string, optional) — Gold pill label above heading
--}}
@props(['title', 'subtitle' => null, 'centered' => true, 'light' => false, 'label' => null])

<div class="reveal {{ $centered ? 'text-center' : '' }} mb-12">
    @if($label)
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold font-heading tracking-wider uppercase mb-4 {{ $light ? 'bg-hilotec-gold/15 text-hilotec-gold' : 'bg-hilotec-gold/10 text-hilotec-gold-dark' }}">
            {{ $label }}
        </span>
    @endif
    <h2 class="text-3xl md:text-4xl font-heading font-bold {{ $light ? 'text-white' : 'text-hilotec-text' }} mb-4">
        {{ $title }}
    </h2>
    @if($subtitle)
        <p class="text-lg {{ $light ? 'text-gray-300' : 'text-hilotec-text-light' }} max-w-2xl {{ $centered ? 'mx-auto' : '' }}">
            {{ $subtitle }}
        </p>
    @endif
</div>
