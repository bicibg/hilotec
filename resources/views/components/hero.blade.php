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
    - badge (string, optional) — Gold pill badge text above heading
--}}
@props([
    'heading',
    'subheading' => null,
    'image' => 'heroes/inner_page_hero_bg.jpg',
    'ctaText' => null,
    'ctaUrl' => null,
    'fullHeight' => false,
    'centered' => false,
    'badge' => null,
])

<section
    class="relative bg-cover bg-center bg-no-repeat flex items-center {{ $fullHeight ? 'min-h-screen' : 'pt-32 pb-20' }}"
    style="background-image: url('{{ asset('images/' . $image) }}')"
>
    {{-- Dark overlay with gradient --}}
    <div class="absolute inset-0 bg-gradient-to-b from-hilotec-dark/70 via-hilotec-dark/50 to-hilotec-dark/80"></div>

    {{-- Subtle grid pattern overlay --}}
    @if($fullHeight)
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px;"></div>
    @endif

    <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8 w-full {{ $fullHeight ? 'py-32' : '' }}">
        <div class="{{ $centered ? 'text-center max-w-3xl mx-auto' : 'max-w-2xl' }}">
            @if($badge)
                <div class="reveal stagger-1 mb-6">
                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold font-heading tracking-wider uppercase bg-hilotec-gold/15 text-hilotec-gold border border-hilotec-gold/30">
                        {{ $badge }}
                    </span>
                </div>
            @endif

            <h1 class="reveal stagger-2 text-4xl md:text-5xl lg:text-6xl font-heading font-bold {{ $fullHeight ? 'italic' : '' }} text-white leading-tight mb-4">
                {{ $heading }}
            </h1>

            @if($subheading)
                <p class="reveal stagger-3 text-lg md:text-xl text-gray-300 mb-8">
                    {{ $subheading }}
                </p>
            @endif

            @if($ctaText && $ctaUrl)
                <div class="reveal stagger-4">
                    <x-button href="{{ $ctaUrl }}" variant="gold" size="lg">
                        {{ $ctaText }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </x-button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>

    {{-- Scroll indicator --}}
    @if($fullHeight)
        <button
            onclick="this.closest('section').nextElementSibling?.scrollIntoView({ behavior: 'smooth' })"
            class="absolute bottom-8 left-1/2 -translate-x-1/2 cursor-pointer bg-transparent border-0 group"
        >
            <div class="flex flex-col items-center gap-2 text-white/60 hover:text-white transition-colors">
                <span class="text-xs font-medium tracking-wider uppercase font-heading">Entdecken</span>
                <div class="w-6 h-10 border-2 border-current rounded-full flex justify-center pt-2">
                    <div class="w-1.5 h-1.5 bg-current rounded-full animate-bounce"></div>
                </div>
            </div>
        </button>
    @endif

    {{-- SVG wave divider at bottom --}}
    <div class="absolute bottom-0 left-0 right-0 overflow-hidden leading-none">
        <svg class="relative block w-full h-12 md:h-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6.01,68.2-17.3,103.6-29.44,38.4-13.17,79.2-22.24,120-17.35,18.3,2.19,35.55,7.02,52.6,13.16,10.15,3.66,20.1,7.97,30.4,11.5V120H0Z" class="fill-hilotec-light"></path>
        </svg>
    </div>
</section>
