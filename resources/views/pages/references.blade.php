<x-layout title="Referenzen" metaDescription="Unsere Referenzen: Kunden aus verschiedenen Branchen vertrauen auf HILOTEC IT-Dienstleistungen.">
    <x-hero
        heading="Referenzen"
        subheading="Kunden aus verschiedenen Branchen vertrauen auf unsere IT-Lösungen"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section
        class="py-20 bg-hilotec-light"
        x-data="{ activeCategory: 'all' }"
    >
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filter Tabs --}}
            <div class="reveal flex flex-wrap gap-2 mb-12">
                <button
                    @click="activeCategory = 'all'"
                    :class="activeCategory === 'all' ? 'bg-hilotec-dark text-white shadow-md' : 'bg-white text-hilotec-text-light hover:text-hilotec-text hover:shadow-md'"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border border-hilotec-border"
                >
                    Alle
                </button>
                @foreach($categories as $category)
                    <button
                        @click="activeCategory = '{{ $category->slug }}'"
                        :class="activeCategory === '{{ $category->slug }}' ? 'bg-hilotec-dark text-white shadow-md' : 'bg-white text-hilotec-text-light hover:text-hilotec-text hover:shadow-md'"
                        class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border border-hilotec-border"
                    >
                        {{ $category->name }}
                        <span class="ml-1 opacity-50">({{ $category->references->count() }})</span>
                    </button>
                @endforeach
            </div>

            {{-- Reference Categories --}}
            @foreach($categories as $category)
                <div
                    x-show="activeCategory === 'all' || activeCategory === '{{ $category->slug }}'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="mb-12 last:mb-0"
                >
                    <div class="bg-white rounded-xl p-6 card-elevated">
                        <h3 class="text-xl font-heading font-semibold text-hilotec-text mb-4 pb-2 border-b border-hilotec-border flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-hilotec-gold rounded-full"></span>
                            {{ $category->name }}
                        </h3>

                        <div>
                            @foreach($category->references as $reference)
                                <x-reference-item :reference="$reference" />
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
