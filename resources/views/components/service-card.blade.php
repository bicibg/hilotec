{{--
    Service Card Component
    Usage: <x-service-card :service="$service" />
    Props:
    - service (App\Models\Service) — Service model instance
--}}
@props(['service'])

<a
    href="{{ route('services.show', $service->slug) }}"
    class="group block bg-hilotec-surface border border-white/5 rounded-xl p-6 hover:border-hilotec-gold/30 transition-all duration-300"
>
    @if($service->icon)
        <div class="mb-4">
            <img
                src="{{ asset('images/icons/' . $service->icon) }}"
                alt=""
                class="w-12 h-12"
            >
        </div>
    @endif

    <h3 class="text-lg font-heading font-semibold text-white mb-3 group-hover:text-hilotec-gold transition-colors">
        {{ $service->title }}
    </h3>

    <p class="text-hilotec-gray text-sm leading-relaxed">
        {{ Str::limit($service->excerpt, 150) }}
    </p>

    <span class="inline-flex items-center gap-1 mt-4 text-sm text-hilotec-blue group-hover:text-hilotec-gold transition-colors">
        Mehr erfahren
        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </span>
</a>
