<x-layout title="Aktuelles" metaDescription="Neuigkeiten und Fachartikel von HILOTEC Engineering + Consulting AG.">
    <x-hero
        heading="Aktuelles"
        subheading="Neuigkeiten und Fachartikel"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            @else
                <p class="text-hilotec-text-light text-center text-lg">
                    Aktuell sind keine Beiträge vorhanden.
                </p>
            @endif
        </div>
    </section>
</x-layout>
