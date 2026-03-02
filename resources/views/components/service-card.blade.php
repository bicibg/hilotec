{{--
    Service Card Component — Alpine Precision design
    White elevated card with gold left accent bar, amber icon circle.
    Usage: <x-service-card :service="$service" />
    Props:
    - service (App\Models\Service) — Service model instance
--}}
@props(['service'])

<a
    href="{{ route('services.show', $service->slug) }}"
    class="reveal group block bg-white rounded-xl pl-5 pr-6 py-6 card-elevated gold-bar-left hover:-translate-y-1 transition-all duration-300"
>
    @if($service->icon)
        <div class="mb-4">
            <div class="w-12 h-12 rounded-lg bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                <img
                    src="{{ asset('images/icons/' . $service->icon) }}"
                    alt=""
                    class="w-7 h-7"
                    loading="lazy"
                >
            </div>
        </div>
    @endif

    <h3 class="text-lg font-heading font-semibold text-hilotec-text mb-3 group-hover:text-hilotec-gold-dark transition-colors">
        {{ $service->title }}
    </h3>

    <p class="text-hilotec-text-light text-sm leading-relaxed">
        {{ Str::limit($service->excerpt, 150) }}
    </p>

    <span class="inline-flex items-center gap-1 mt-4 text-sm font-medium text-hilotec-blue group-hover:text-hilotec-blue-dark transition-colors">
        Mehr erfahren
        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </span>
</a>
