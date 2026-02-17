{{--
    Footer CTA Section
    Yellow card with dark text over a matrix rain background image.
    Appears on all pages above the footer.
--}}
<section class="relative py-20 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/backgrounds/footer_cta_bg.jpg') }}')">
    <div class="absolute inset-0 bg-hilotec-dark/40"></div>
    <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-hilotec-gold rounded-lg p-8 md:p-10">
                <p class="text-lg md:text-xl font-heading font-semibold text-black leading-relaxed mb-6">
                    {{ setting('footer.cta_heading') }}
                </p>
                <x-button href="{{ setting('footer.cta_button_url') }}" variant="blue" size="lg">
                    {{ setting('footer.cta_button_text') }}
                    <svg class="w-4 h-4 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </x-button>
            </div>
        </div>
    </div>
</section>
