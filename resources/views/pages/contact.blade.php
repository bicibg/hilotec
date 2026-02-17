<x-layout title="Kontakt" metaDescription="Kontaktieren Sie HILOTEC Engineering + Consulting AG. Wir helfen Ihnen gerne weiter.">
    <x-hero
        heading="Kontakt"
        subheading="Wir freuen uns auf Ihre Anfrage"
        image="heroes/inner_page_hero_bg.jpg"
    />

    <section class="py-20 bg-hilotec-light">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Contact Form --}}
                <div class="reveal">
                    <div class="bg-white rounded-2xl p-8 card-elevated">
                        <h2 class="text-2xl font-heading font-bold text-hilotec-text mb-6">Schreiben Sie uns</h2>

                        @if(session('success'))
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                                <p class="text-green-700">{{ session('success') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.send') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-medium text-hilotec-text mb-2">Name *</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    class="w-full px-4 py-3 bg-hilotec-light border border-hilotec-border rounded-xl text-hilotec-text placeholder-hilotec-text-muted focus:border-hilotec-gold focus:ring-1 focus:ring-hilotec-gold focus:outline-none transition-colors"
                                    placeholder="Ihr Name"
                                >
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-hilotec-text mb-2">E-Mail *</label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full px-4 py-3 bg-hilotec-light border border-hilotec-border rounded-xl text-hilotec-text placeholder-hilotec-text-muted focus:border-hilotec-gold focus:ring-1 focus:ring-hilotec-gold focus:outline-none transition-colors"
                                    placeholder="Ihre E-Mail-Adresse"
                                >
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-hilotec-text mb-2">Telefon</label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="w-full px-4 py-3 bg-hilotec-light border border-hilotec-border rounded-xl text-hilotec-text placeholder-hilotec-text-muted focus:border-hilotec-gold focus:ring-1 focus:ring-hilotec-gold focus:outline-none transition-colors"
                                    placeholder="Ihre Telefonnummer"
                                >
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-hilotec-text mb-2">Nachricht *</label>
                                <textarea
                                    id="message"
                                    name="message"
                                    rows="5"
                                    required
                                    class="w-full px-4 py-3 bg-hilotec-light border border-hilotec-border rounded-xl text-hilotec-text placeholder-hilotec-text-muted focus:border-hilotec-gold focus:ring-1 focus:ring-hilotec-gold focus:outline-none transition-colors resize-y"
                                    placeholder="Ihre Nachricht"
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-button type="submit" variant="blue" size="lg">
                                Nachricht senden
                            </x-button>
                        </form>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="reveal stagger-2">
                    <h2 class="text-2xl font-heading font-bold text-hilotec-text mb-6">Kontaktinformationen</h2>

                    <div class="space-y-6">
                        {{-- Address --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-hilotec-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-hilotec-text font-medium mb-1">Adresse</h4>
                                <address class="text-hilotec-text-light text-sm not-italic leading-relaxed">
                                    {{ setting('general.company_name') }}<br>
                                    {{ setting('contact.address_line1') }}<br>
                                    {{ setting('contact.address_zip_city') }}<br>
                                    {{ setting('contact.address_country') }}
                                </address>
                            </div>
                        </div>

                        {{-- Phone Infrastructure --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-hilotec-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-hilotec-text font-medium mb-1">{{ setting('contact.phone_label_infra') }}</h4>
                                <a href="tel:{{ str_replace(' ', '', setting('contact.phone_support_infra')) }}" class="text-hilotec-blue hover:text-hilotec-blue-dark transition-colors">
                                    {{ setting('contact.phone_support_infra') }}
                                </a>
                            </div>
                        </div>

                        {{-- Phone Software --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-hilotec-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-hilotec-text font-medium mb-1">{{ setting('contact.phone_label_software') }}</h4>
                                <a href="tel:{{ str_replace(' ', '', setting('contact.phone_support_software')) }}" class="text-hilotec-blue hover:text-hilotec-blue-dark transition-colors">
                                    {{ setting('contact.phone_support_software') }}
                                </a>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-hilotec-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-hilotec-text font-medium mb-1">E-Mail</h4>
                                <a href="mailto:{{ setting('contact.email') }}" class="text-hilotec-blue hover:text-hilotec-blue-dark transition-colors">
                                    {{ setting('contact.email') }}
                                </a>
                            </div>
                        </div>

                        {{-- Business Hours --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-hilotec-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-hilotec-text font-medium mb-1">Öffnungszeiten</h4>
                                <p class="text-hilotec-text-light text-sm">{{ setting('contact.business_hours') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
