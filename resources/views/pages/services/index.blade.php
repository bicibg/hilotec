<x-layout title="Angebot" metaDescription="IT-Dienstleistungen für KMU: Infrastruktur, Sicherheit, Cloud, Backup, Software, VoIP, Virtualisierung und Beratung.">
    <x-hero
        heading="Unser Angebot"
        subheading="Umfassende IT-Dienstleistungen für Ihr KMU"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
