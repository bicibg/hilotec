{{--
    Site Header Component
    Sticky header with transparent-to-solid transition on scroll.
    Logo left, navigation right. Mobile hamburger menu.
--}}
<header
    x-data="{ scrolled: false, mobileOpen: false }"
    x-init="scrolled = window.scrollY > 50; window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
    :class="mobileOpen ? 'bg-hilotec-dark shadow-lg border-b border-white/5' : (scrolled ? 'bg-hilotec-dark/95 backdrop-blur-sm shadow-lg' : 'bg-transparent')"
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
>
    {{-- Mobile backdrop — closes menu on tap outside --}}
    <div
        x-show="mobileOpen"
        x-cloak
        @click="mobileOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="md:hidden fixed inset-0 bg-black/30 -z-10"
    ></div>

    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="{{ route('home') }}">
                <img
                    src="{{ asset('images/branding/logo.png') }}"
                    alt="{{ setting('general.company_name') }}"
                    class="h-12 w-auto"
                >
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-8">
                @php
                    $navItems = [
                        ['label' => 'Home', 'route' => 'home', 'url' => '/'],
                        ['label' => 'Angebot', 'route' => 'services.index', 'url' => '/angebot'],
                        ['label' => 'Referenzen', 'route' => 'references.index', 'url' => '/referenzen'],
                        ['label' => 'Über uns', 'route' => 'about', 'url' => '/ueber-uns'],
                        ['label' => 'Aktuelles', 'route' => 'posts.index', 'url' => '/aktuelles'],
                        ['label' => 'Kontakt', 'route' => 'contact', 'url' => '/kontakt'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    @php
                        $isActive = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*');
                        if ($item['url'] === '/') $isActive = request()->is('/') || request()->routeIs('home');
                    @endphp
                    <a
                        href="{{ $item['url'] }}"
                        class="relative text-sm font-medium transition-colors duration-200 {{ $isActive ? 'text-hilotec-gold' : 'text-white hover:text-hilotec-gold' }}"
                    >
                        {{ $item['label'] }}
                        @if($isActive)
                            <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-hilotec-gold rounded-full"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- Mobile Menu Button --}}
            <button
                @click="mobileOpen = !mobileOpen"
                class="md:hidden text-white p-2"
                aria-label="Menü"
            >
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation --}}
        <nav
            x-show="mobileOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden pb-4 border-t border-white/10"
        >
            @foreach($navItems as $item)
                @php
                    $isActive = request()->is(ltrim($item['url'], '/')) || request()->is(ltrim($item['url'], '/') . '/*');
                    if ($item['url'] === '/') $isActive = request()->is('/') || request()->routeIs('home');
                @endphp
                <a
                    href="{{ $item['url'] }}"
                    class="block py-3 px-4 text-sm font-medium {{ $isActive ? 'text-hilotec-gold' : 'text-white hover:text-hilotec-gold' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
