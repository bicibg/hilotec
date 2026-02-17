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

    {{-- Body Content --}}
    <section class="py-20 bg-hilotec-dark">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <div class="prose prose-invert prose-lg max-w-none">
                    {!! $page->body !!}
                </div>
            </div>
        </div>
    </section>

    {{-- Team Section --}}
    @if($teamMembers->isNotEmpty())
        <section class="py-20 bg-hilotec-surface">
            <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
                <x-section-heading title="Unser Team" />

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($teamMembers as $member)
                        <div class="bg-hilotec-dark rounded-xl p-6 border border-white/5">
                            @if($member->photo)
                                <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4 bg-hilotec-surface">
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-24 h-24 rounded-full bg-hilotec-surface mx-auto mb-4 flex items-center justify-center">
                                    <span class="text-2xl font-heading font-bold text-hilotec-gold">{{ substr($member->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <h3 class="text-lg font-heading font-semibold text-white text-center">{{ $member->name }}</h3>
                            @if($member->role)
                                <p class="text-hilotec-gold text-sm text-center mt-1">{{ $member->role }}</p>
                            @endif
                            @if($member->bio)
                                <p class="text-hilotec-gray text-sm text-center mt-3">{{ $member->bio }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
