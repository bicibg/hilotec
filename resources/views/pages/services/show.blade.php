<x-layout title="{{ $service->title }}" metaDescription="{{ $service->excerpt }}">
    @push('head')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "name": "{{ $service->title }}",
        "description": "{{ $service->excerpt }}",
        "provider": {
            "@type": "LocalBusiness",
            "name": "{{ setting('general.company_name') }}"
        },
        "url": "{{ url()->current() }}"
    }
    </script>
    @endpush

    <x-hero
        heading="{{ $service->title }}"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                {{-- Main Content --}}
                <div class="lg:col-span-3 reveal">
                    @if($service->icon)
                        <div class="w-16 h-16 rounded-xl bg-amber-50 flex items-center justify-center mb-6">
                            <img src="{{ asset('images/icons/' . $service->icon) }}" alt="" class="w-10 h-10" loading="lazy">
                        </div>
                    @endif

                    <div class="prose prose-lg prose-light max-w-none">
                        {!! clean($service->body) !!}
                    </div>

                    <div class="mt-10">
                        <x-button href="/kontakt" variant="blue" size="lg">
                            Unverbindlich anfragen
                        </x-button>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-24 reveal stagger-2">
                        <div class="bg-white rounded-xl p-5 card-elevated">
                            <h3 class="text-sm font-heading font-semibold text-hilotec-gold-dark uppercase tracking-wider mb-4">
                                Alle Leistungen
                            </h3>
                            <nav class="flex flex-col gap-1">
                                @foreach($services as $s)
                                    <a
                                        href="{{ route('services.show', $s->slug) }}"
                                        class="block py-2 px-3 rounded-lg text-sm transition-colors {{ $s->id === $service->id ? 'bg-hilotec-light-alt text-hilotec-blue font-medium' : 'text-hilotec-text-light hover:text-hilotec-text hover:bg-hilotec-light-alt' }}"
                                    >
                                        {{ $s->title }}
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
