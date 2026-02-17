{{--
    Reference Item Component — Alpine Precision design
    Light card style for light backgrounds.
    Usage: <x-reference-item :reference="$reference" />
    Props:
    - reference (App\Models\Reference) — Reference model instance
--}}
@props(['reference'])

<div class="border-b border-hilotec-border py-4 last:border-b-0">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
        <div class="flex-1">
            <h4 class="text-hilotec-text font-medium">
                @if($reference->website)
                    <a href="https://{{ $reference->website }}" target="_blank" rel="noopener noreferrer" class="hover:text-hilotec-blue transition-colors">
                        {{ $reference->company_name }}
                        <svg class="w-3 h-3 inline-block ml-1 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                @else
                    {{ $reference->company_name }}
                @endif
            </h4>
            @if($reference->address)
                <p class="text-hilotec-text-muted text-sm mt-0.5">{{ $reference->address }}</p>
            @endif
        </div>
        <p class="text-hilotec-text-light text-sm sm:text-right sm:max-w-md">{{ $reference->description }}</p>
    </div>
</div>
