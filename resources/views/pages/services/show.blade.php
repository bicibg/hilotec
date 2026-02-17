<x-layout title="{{ $service->title }}" metaDescription="{{ $service->excerpt }}">
    <x-hero
        heading="{{ $service->title }}"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section class="py-20 bg-hilotec-dark">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                {{-- Main Content --}}
                <div class="lg:col-span-3">
                    @if($service->icon)
                        <img src="{{ asset('images/icons/' . $service->icon) }}" alt="" class="w-16 h-16 mb-6">
                    @endif

                    <div class="prose prose-invert prose-lg max-w-none">
                        {!! $service->body !!}
                    </div>

                    <div class="mt-10">
                        <x-button href="/kontakt" variant="blue" size="lg">
                            Unverbindlich anfragen
                        </x-button>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <h3 class="text-sm font-heading font-semibold text-hilotec-gold uppercase tracking-wider mb-4">
                            Alle Leistungen
                        </h3>
                        <nav class="flex flex-col gap-1">
                            @foreach($services as $s)
                                <a
                                    href="{{ route('services.show', $s->slug) }}"
                                    class="block py-2 px-3 rounded-lg text-sm transition-colors {{ $s->id === $service->id ? 'bg-hilotec-surface text-hilotec-gold' : 'text-hilotec-gray hover:text-white hover:bg-hilotec-surface/50' }}"
                                >
                                    {{ $s->title }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
