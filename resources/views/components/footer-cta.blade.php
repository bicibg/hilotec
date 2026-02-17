{{--
    Footer CTA Section — Alpine Precision design
    Dark gradient background with glassmorphic card and dot pattern.
    Appears on all pages above the footer.
--}}
<section class="relative py-24 bg-gradient-to-br from-hilotec-dark via-hilotec-surface to-hilotec-dark overflow-hidden">
    {{-- Dot pattern overlay --}}
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px); background-size: 24px 24px;"></div>

    {{-- SVG wave at top for transition from light content --}}
    <div class="absolute top-0 left-0 right-0 overflow-hidden leading-none rotate-180">
        <svg class="relative block w-full h-12 md:h-16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,60 C150,90 350,30 600,60 C850,90 1050,30 1200,60 L1200,120 L0,120 Z" class="fill-hilotec-light"></path>
        </svg>
    </div>

    <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="reveal max-w-2xl mx-auto">
            <div class="glass rounded-2xl border border-white/10 p-8 md:p-10 text-center">
                <p class="text-lg md:text-xl font-heading font-semibold text-white leading-relaxed mb-6">
                    {{ setting('footer.cta_heading') }}
                </p>
                <x-button href="{{ setting('footer.cta_button_url') }}" variant="gold" size="lg">
                    {{ setting('footer.cta_button_text') }}
                    <svg class="w-4 h-4 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </x-button>
            </div>
        </div>
    </div>
</section>
