<x-layout
    title="Über uns"
    metaDescription="{{ $page->meta_description }}"
>
    <x-hero
        heading="{{ $page->hero_heading }}"
        subheading="{{ $page->hero_subheading }}"
        image="{{ $page->hero_image }}"
        :fullHeight="true"
        :centered="true"
    />

    {{-- Body Content — Light background --}}
    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto reveal">
                <div class="prose prose-lg prose-light max-w-none">
                    {!! $page->body !!}
                </div>
            </div>
        </div>
    </section>

    {{-- Team Section — Alt light background --}}
    @if($teamMembers->isNotEmpty())
        <section class="py-20 bg-hilotec-light-alt">
            <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
                <x-section-heading title="Unser Team" label="Team" />

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($teamMembers as $member)
                        <div class="reveal bg-white rounded-xl p-6 card-elevated text-center">
                            @if($member->photo)
                                <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4 ring-2 ring-hilotec-gold/20 ring-offset-2">
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-24 h-24 rounded-full bg-hilotec-light-alt mx-auto mb-4 flex items-center justify-center ring-2 ring-hilotec-gold/20 ring-offset-2">
                                    <span class="text-2xl font-heading font-bold text-hilotec-gold">{{ substr($member->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <h3 class="text-lg font-heading font-semibold text-hilotec-text">{{ $member->name }}</h3>
                            @if($member->role)
                                <p class="text-hilotec-gold-dark text-sm mt-1">{{ $member->role }}</p>
                            @endif
                            @if($member->bio)
                                <p class="text-hilotec-text-light text-sm mt-3">{{ $member->bio }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
