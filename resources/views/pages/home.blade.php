<x-layout>
    {{-- Hero Section --}}
    <x-hero
        heading="{{ setting('general.company_slogan') }}"
        subheading="{{ setting('general.company_subtitle') }}"
        image="heroes/home_hero_bg.jpg"
        ctaText="Kontakt aufnehmen"
        ctaUrl="/kontakt"
        :fullHeight="true"
    />

    {{-- Services Section --}}
    <section class="py-20 bg-hilotec-dark">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading
                title="Unsere Leistungen"
                subtitle="Alles was Ihr KMU im Bereich der Informationstechnologie braucht — aus einer Hand."
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($services as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>

            <div class="text-center mt-10">
                <x-button href="/angebot" variant="outline">
                    Alle Leistungen ansehen
                </x-button>
            </div>
        </div>
    </section>

    {{-- About Teaser Section --}}
    <section class="py-20 bg-hilotec-surface">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-heading font-bold text-white mb-6">
                        Ihr IT-Partner seit {{ setting('general.founded_year') }}
                    </h2>
                    <p class="text-hilotec-gray leading-relaxed mb-6">
                        {{ setting('general.about_short') }}
                    </p>
                    <x-button href="/ueber-uns" variant="outline">
                        Mehr über uns
                    </x-button>
                </div>
                <div class="relative">
                    <div class="aspect-video rounded-xl overflow-hidden">
                        <img
                            src="{{ asset('images/heroes/ueber_uns_hero_bg.jpg') }}"
                            alt="Emmental Landschaft"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
