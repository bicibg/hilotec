<x-layout
    title="{{ $page->meta_title ?? $page->title }}"
    metaDescription="{{ $page->meta_description }}"
>
    <x-hero
        heading="{{ $page->hero_heading ?? $page->title }}"
        :subheading="$page->hero_subheading"
        image="{{ $page->hero_image ?? 'heroes/inner_page_hero_bg.jpg' }}"
    />

    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto reveal">
                <div class="prose prose-lg prose-light max-w-none">
                    {!! $page->body !!}
                </div>
            </div>
        </div>
    </section>
</x-layout>
