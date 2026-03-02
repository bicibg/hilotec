<x-layout
    title="{{ $post->title }}"
    metaDescription="{{ $post->excerpt }}"
    metaImage="{{ $post->featured_image ? 'storage/' . $post->featured_image : null }}"
>
    @push('head')
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ $post->title }}",
        "description": "{{ $post->excerpt }}",
        "datePublished": "{{ $post->published_at?->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "author": {
            "@type": "Organization",
            "name": "{{ setting('general.company_name') }}"
        },
        "publisher": {
            "@type": "Organization",
            "name": "{{ setting('general.company_name') }}",
            "logo": {
                "@type": "ImageObject",
                "url": "{{ asset('images/branding/logo.png') }}"
            }
        }
        @if($post->featured_image)
        ,"image": "{{ asset('storage/' . $post->featured_image) }}"
        @endif
    }
    </script>
    @endpush

    <x-hero
        heading="{{ $post->title }}"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto reveal">
                @if($post->published_at)
                    <time class="text-hilotec-text-muted text-sm mb-6 block">
                        {{ $post->published_at->format('d. F Y') }}
                    </time>
                @endif

                @if($post->excerpt)
                    <p class="text-xl text-hilotec-text-light leading-relaxed mb-8 border-l-4 border-hilotec-gold pl-4">
                        {{ $post->excerpt }}
                    </p>
                @endif

                <div class="prose prose-lg prose-light max-w-none">
                    {!! $post->body !!}
                </div>

                <div class="mt-12 pt-8 border-t border-hilotec-border">
                    <x-button href="/aktuelles" variant="outline-dark">
                        &larr; Zurück zur Übersicht
                    </x-button>
                </div>
            </div>
        </div>
    </section>
</x-layout>
