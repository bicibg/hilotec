{{--
    Hero Section Component
    Usage: <x-hero heading="Title" image="heroes/home_hero_bg.jpg" :fullHeight="true">Optional content</x-hero>
    Props:
    - heading (string) — Main heading text
    - subheading (string, optional) — Subtitle text
    - image (string) — Background image path relative to /images/
    - ctaText (string, optional) — CTA button text
    - ctaUrl (string, optional) — CTA button URL
    - fullHeight (bool) — Full viewport height (true) or compact (false)
    - centered (bool) — Center text (true) or left-align (false)
--}}
@props([
    'heading',
    'subheading' => null,
    'image' => 'heroes/inner_page_hero_bg.jpg',
    'ctaText' => null,
    'ctaUrl' => null,
    'fullHeight' => false,
    'centered' => false,
])

<section
    class="relative bg-cover bg-center bg-no-repeat flex items-center {{ $fullHeight ? 'min-h-screen' : 'pt-32 pb-20' }}"
    style="background-image: url('{{ asset('images/' . $image) }}')"
>
    <div class="absolute inset-0 bg-hilotec-dark/50"></div>

    <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 w-full {{ $fullHeight ? 'py-32' : '' }}">
        <div class="{{ $centered ? 'text-center max-w-3xl mx-auto' : 'max-w-2xl' }}">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-heading font-bold {{ $fullHeight ? 'italic' : '' }} text-white leading-tight mb-4">
                {{ $heading }}
            </h1>

            @if($subheading)
                <p class="text-lg md:text-xl text-hilotec-gray-light mb-8">
                    {{ $subheading }}
                </p>
            @endif

            @if($ctaText && $ctaUrl)
                <x-button href="{{ $ctaUrl }}" variant="blue" size="lg">
                    {{ $ctaText }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </x-button>
            @endif

            {{ $slot }}
        </div>
    </div>

    @if($fullHeight)
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <div class="flex flex-col items-center gap-2 text-white/70">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <span class="text-xs font-medium tracking-wider uppercase">Entdecken</span>
            </div>
        </div>
    @endif
</section>
