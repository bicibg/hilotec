{{--
    Site Footer Component
    Dark background, 5-column grid: Logo | Fernwartung | Navigation | Anschrift | Kontakt
    Gold headings. Copyright bar at bottom.
--}}
<footer class="bg-hilotec-darker pt-16 pb-8">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
            {{-- Column 1: Logo --}}
            <div>
                <a href="{{ route('home') }}">
                    <img
                        src="{{ asset('images/branding/logo.png') }}"
                        alt="{{ setting('general.company_name') }}"
                        class="h-14 w-auto mb-4"
                    >
                </a>
            </div>

            {{-- Column 2: Fernwartung --}}
            <div>
                <h4 class="text-hilotec-gold font-heading font-semibold text-sm uppercase tracking-wider mb-4">Fernwartung</h4>
                <p class="text-hilotec-gray text-sm mb-4">{{ setting('footer.teamviewer_text') }}</p>
                <a href="{{ setting('footer.teamviewer_url') }}" target="_blank" rel="noopener noreferrer">
                    <img
                        src="{{ asset('images/branding/teamviewer_badge.png') }}"
                        alt="TeamViewer"
                        class="h-10 w-auto"
                    >
                </a>
            </div>

            {{-- Column 3: Navigation --}}
            <div>
                <h4 class="text-hilotec-gold font-heading font-semibold text-sm uppercase tracking-wider mb-4">Navigation</h4>
                <nav class="flex flex-col gap-2">
                    <a href="/" class="text-hilotec-gray text-sm hover:text-white transition-colors">Home</a>
                    <a href="/angebot" class="text-hilotec-gray text-sm hover:text-white transition-colors">Angebot</a>
                    <a href="/referenzen" class="text-hilotec-gray text-sm hover:text-white transition-colors">Referenzen</a>
                    <a href="/ueber-uns" class="text-hilotec-gray text-sm hover:text-white transition-colors">Über uns</a>
                    <a href="/aktuelles" class="text-hilotec-gray text-sm hover:text-white transition-colors">Aktuelles</a>
                    <a href="/kontakt" class="text-hilotec-gray text-sm hover:text-white transition-colors">Kontakt</a>
                </nav>
            </div>

            {{-- Column 4: Anschrift --}}
            <div>
                <h4 class="text-hilotec-gold font-heading font-semibold text-sm uppercase tracking-wider mb-4">Anschrift</h4>
                <address class="text-hilotec-gray text-sm not-italic leading-relaxed">
                    {{ setting('general.company_name') }}<br>
                    {{ setting('contact.address_line1') }}<br>
                    {{ setting('contact.address_zip_city') }}<br>
                    {{ setting('contact.address_country') }}
                </address>
            </div>

            {{-- Column 5: Kontakt --}}
            <div>
                <h4 class="text-hilotec-gold font-heading font-semibold text-sm uppercase tracking-wider mb-4">Kontakt</h4>
                <div class="text-sm space-y-3">
                    <div>
                        <p class="text-hilotec-gray">{{ setting('contact.phone_label_infra') }}:</p>
                        <a href="tel:{{ str_replace(' ', '', setting('contact.phone_support_infra')) }}" class="text-white hover:text-hilotec-gold transition-colors">
                            {{ setting('contact.phone_support_infra') }}
                        </a>
                    </div>
                    <div>
                        <p class="text-hilotec-gray">{{ setting('contact.phone_label_software') }}:</p>
                        <a href="tel:{{ str_replace(' ', '', setting('contact.phone_support_software')) }}" class="text-white hover:text-hilotec-gold transition-colors">
                            {{ setting('contact.phone_support_software') }}
                        </a>
                    </div>
                    <div>
                        <a href="mailto:{{ setting('contact.email') }}" class="text-hilotec-gold hover:text-hilotec-gold-light transition-colors">
                            {{ setting('contact.email') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copyright Bar --}}
        <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-hilotec-gray-dark text-xs">&copy;2026 HILOTEC Engineering + Consulting AG | Website by Bugra Ergin</p>
            <div class="flex items-center gap-4">
                <a href="/impressum" class="text-hilotec-gray-dark text-xs hover:text-white transition-colors">Impressum</a>
                <a href="/datenschutz" class="text-hilotec-gray-dark text-xs hover:text-white transition-colors">Datenschutz</a>
                @if(setting('social.linkedin'))
                    <a href="{{ setting('social.linkedin') }}" target="_blank" rel="noopener noreferrer" class="text-hilotec-gray-dark hover:text-white transition-colors" aria-label="LinkedIn">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                @endif
                @if(setting('social.github'))
                    <a href="{{ setting('social.github') }}" target="_blank" rel="noopener noreferrer" class="text-hilotec-gray-dark hover:text-white transition-colors" aria-label="GitHub">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</footer>
