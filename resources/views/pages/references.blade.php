<x-layout title="Referenzen" metaDescription="Unsere Referenzen: Kunden aus verschiedenen Branchen vertrauen auf HILOTEC IT-Dienstleistungen.">
    <x-hero
        heading="Referenzen"
        subheading="Kunden aus verschiedenen Branchen vertrauen auf unsere IT-Lösungen"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section
        class="py-20 bg-hilotec-dark"
        x-data="{ activeCategory: 'all' }"
    >
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filter Tabs --}}
            <div class="flex flex-wrap gap-2 mb-12">
                <button
                    @click="activeCategory = 'all'"
                    :class="activeCategory === 'all' ? 'bg-hilotec-gold text-black' : 'bg-hilotec-surface text-hilotec-gray hover:text-white'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                >
                    Alle
                </button>
                @foreach($categories as $category)
                    <button
                        @click="activeCategory = '{{ $category->slug }}'"
                        :class="activeCategory === '{{ $category->slug }}' ? 'bg-hilotec-gold text-black' : 'bg-hilotec-surface text-hilotec-gray hover:text-white'"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    >
                        {{ $category->name }}
                        <span class="ml-1 opacity-60">({{ $category->references->count() }})</span>
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
                    <h3 class="text-xl font-heading font-semibold text-hilotec-gold mb-4 pb-2 border-b border-hilotec-gold/20">
                        {{ $category->name }}
                    </h3>

                    <div>
                        @foreach($category->references as $reference)
                            <x-reference-item :reference="$reference" />
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
