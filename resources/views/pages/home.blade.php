<x-layout>
    {{-- Hero Section --}}
    <x-hero
        heading="{{ setting('general.company_slogan') }}"
        subheading="{{ setting('general.company_subtitle') }}"
        image="heroes/home_hero_bg.jpg"
        ctaText="Kontakt aufnehmen"
        ctaUrl="/kontakt"
        badge="IT-Komplettbetreuung für KMU"
        :fullHeight="true"
    />

    {{-- Services Section — Light background --}}
    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading
                title="Unsere Leistungen"
                subtitle="Alles was Ihr KMU im Bereich der Informationstechnologie braucht — aus einer Hand."
                label="Angebot"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($services as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>

            <div class="text-center mt-10 reveal">
                <x-button href="/angebot" variant="outline-dark">
                    Alle Leistungen ansehen
                </x-button>
            </div>
        </div>
    </section>

    {{-- Stats Band — Dark section with animated counters --}}
    <section class="relative py-16 bg-hilotec-dark overflow-hidden">
        {{-- Subtle grid overlay --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

        <div class="relative max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @php
                    $stats = [
                        ['value' => (int) date('Y') - (int) setting('general.founded_year'), 'suffix' => '+', 'label' => 'Jahre Erfahrung'],
                        ['value' => 55, 'suffix' => '+', 'label' => 'Zufriedene Kunden'],
                        ['value' => 8, 'suffix' => '', 'label' => 'IT-Dienstleistungen'],
                        ['value' => 24, 'suffix' => '/7', 'label' => 'Monitoring'],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div
                        class="reveal"
                        x-data="animatedCounter({{ $stat['value'] }})"
                        x-intersect.once="startCounting()"
                    >
                        <div class="text-4xl md:text-5xl font-heading font-bold text-white mb-2">
                            <span x-text="current">0</span><span class="text-hilotec-gold">{{ $stat['suffix'] }}</span>
                        </div>
                        <p class="text-gray-400 text-sm font-medium">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- About Teaser Section — Light background --}}
    <section class="py-20 bg-hilotec-light-alt">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="reveal">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold font-heading tracking-wider uppercase mb-4 bg-hilotec-gold/10 text-hilotec-gold-dark">
                        Über uns
                    </span>
                    <h2 class="text-3xl md:text-4xl font-heading font-bold text-hilotec-text mb-6">
                        Ihr IT-Partner seit {{ setting('general.founded_year') }}
                    </h2>
                    <p class="text-hilotec-text-light leading-relaxed mb-6">
                        {{ setting('general.about_short') }}
                    </p>
                    <x-button href="/ueber-uns" variant="outline-dark">
                        Mehr über uns
                    </x-button>
                </div>
                <div class="reveal stagger-2">
                    <div class="relative">
                        <div class="aspect-video rounded-2xl overflow-hidden card-elevated">
                            <img
                                src="{{ asset('images/heroes/ueber_uns_hero_bg.jpg') }}"
                                alt="Emmental Landschaft"
                                class="w-full h-full object-cover"
                            >
                        </div>
                        {{-- Decorative accent --}}
                        <div class="absolute -bottom-3 -right-3 w-24 h-24 border-2 border-hilotec-gold/20 rounded-2xl -z-10"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
