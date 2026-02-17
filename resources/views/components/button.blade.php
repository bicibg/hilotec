{{--
    Button Component
    Usage: <x-button href="/kontakt" variant="blue" size="lg">Kontakt aufnehmen</x-button>
    Props:
    - href (string, optional) — makes it an <a> tag; without it, renders <button>
    - variant (string) — 'blue' (default), 'gold', 'outline', 'outline-dark'
    - size (string) — 'md' (default), 'lg'
    - type (string) — button type when not a link, default 'button'
--}}
@props(['href' => null, 'variant' => 'blue', 'size' => 'md', 'type' => 'button'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-heading font-semibold rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:scale-[1.02] active:scale-[0.98]';

    $variantClasses = match($variant) {
        'blue' => 'bg-hilotec-blue text-white hover:bg-hilotec-blue-dark focus:ring-hilotec-blue glow-blue',
        'gold' => 'bg-hilotec-gold text-black hover:bg-hilotec-gold-dark focus:ring-hilotec-gold glow-gold',
        'outline' => 'border-2 border-hilotec-gold text-hilotec-gold hover:bg-hilotec-gold hover:text-black focus:ring-hilotec-gold',
        'outline-dark' => 'border-2 border-hilotec-dark text-hilotec-dark hover:bg-hilotec-dark hover:text-white focus:ring-hilotec-dark',
        default => 'bg-hilotec-blue text-white hover:bg-hilotec-blue-dark focus:ring-hilotec-blue glow-blue',
    };

    $sizeClasses = match($size) {
        'lg' => 'px-8 py-3.5 text-base',
        default => 'px-6 py-2.5 text-sm',
    };

    $classes = "$baseClasses $variantClasses $sizeClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
