{{--
    Post Card Component
    Usage: <x-post-card :post="$post" />
    Props:
    - post (App\Models\Post) — Post model instance
--}}
@props(['post'])

<a
    href="{{ route('posts.show', $post->slug) }}"
    class="group block bg-hilotec-surface border border-white/5 rounded-xl overflow-hidden hover:border-hilotec-gold/30 transition-all duration-300"
>
    @if($post->featured_image)
        <div class="aspect-video bg-hilotec-dark overflow-hidden">
            <img
                src="{{ asset('storage/' . $post->featured_image) }}"
                alt="{{ $post->title }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            >
        </div>
    @endif

    <div class="p-6">
        @if($post->published_at)
            <time class="text-xs text-hilotec-gray-dark font-medium">
                {{ $post->published_at->format('d.m.Y') }}
            </time>
        @endif

        <h3 class="text-lg font-heading font-semibold text-white mt-2 mb-3 group-hover:text-hilotec-gold transition-colors">
            {{ $post->title }}
        </h3>

        @if($post->excerpt)
            <p class="text-hilotec-gray text-sm leading-relaxed">
                {{ Str::limit($post->excerpt, 160) }}
            </p>
        @endif

        <span class="inline-flex items-center gap-1 mt-4 text-sm text-hilotec-blue group-hover:text-hilotec-gold transition-colors">
            Weiterlesen
            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </span>
    </div>
</a>
