@php
    $l = app()->getLocale();

    // Rota atual, para marcar o item de navegação ativo.
    $current = Illuminate\Support\Facades\Route::currentRouteName();

    $navItems = [
        'public.home' => 'site.nav.home',
        'public.fazenda' => 'site.nav.fazenda',
        'public.ativo' => 'site.nav.ativo',
        'public.servico' => 'site.nav.servico',
        'public.informacoes' => 'site.nav.informacoes',
        'public.contatos' => 'site.nav.contatos',
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-30 border-b border-linen-200 bg-linen-50/90 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-20 items-center justify-between gap-4 py-3">
            <a href="{{ route('public.home', $l) }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                     width="48" height="48" decoding="async"
                     class="h-12 w-12 rounded-card object-cover shadow-sm">
                <div class="hidden leading-tight sm:block">
                    <span class="block font-display text-lg font-semibold text-onyx-950">Portal Invest Bahia</span>
                </div>
            </a>

            <nav class="hidden min-w-0 items-center gap-5 lg:flex xl:gap-7">
                @foreach ($navItems as $route => $key)
                    @php $active = $current === $route; @endphp
                    <a href="{{ route($route, $l) }}"
                       @if ($active) aria-current="page" @endif
                       class="group relative whitespace-nowrap py-1 text-sm font-semibold transition-colors duration-200 ease-pib hover:text-ouro-700 {{ $active ? 'text-ouro-700' : 'text-onyx-700' }}">
                        @lang($key)
                        {{-- Sublinhado: fixo no item ativo, cresce do centro no hover dos demais. --}}
                        <span class="absolute inset-x-0 -bottom-0.5 h-px origin-center bg-ouro-500 transition-transform duration-300 ease-pib {{ $active ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                    </a>
                @endforeach
            </nav>

            <div class="flex shrink-0 items-center gap-3">
                <div x-data="{ langOpen: false }" class="relative">
                    <button @click="langOpen = !langOpen"
                            :aria-expanded="langOpen"
                            aria-haspopup="true"
                            class="flex items-center gap-1 rounded-pill border border-linen-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-onyx-700 shadow-sm transition duration-200 ease-pib hover:border-ouro-500 hover:text-ouro-700 active:scale-95">
                        {{ $l }}
                        <svg class="h-4 w-4 transition-transform duration-200 ease-pib" :class="langOpen && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="langOpen" @click.outside="langOpen = false" @keydown.escape.window="langOpen = false" x-cloak
                         x-transition:enter="transition ease-pib duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-pib duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute right-0 z-20 mt-2 w-44 overflow-hidden rounded-card border border-linen-200 bg-white shadow-panel">
                        @foreach ($localeUrls as $code => $url)
                            <a href="{{ $url }}"
                               class="block px-4 py-2.5 text-sm text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700 {{ $code === $l ? 'font-semibold text-ouro-700' : '' }}">
                                {{ config('pib.locales')[$code] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('public.contatos', $l) }}"
                   class="hidden rounded-pill bg-onyx-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition duration-200 ease-pib hover:bg-ouro-700 active:scale-95 md:inline-flex">
                    @lang('site.cta.contact_broker')
                </a>

                <button @click="open = !open"
                        :aria-expanded="open"
                        aria-controls="nav-mobile"
                        class="rounded-pill border border-linen-300 bg-white p-2 text-onyx-700 transition duration-200 ease-pib hover:border-ouro-500 hover:text-ouro-700 active:scale-95 lg:hidden"
                        aria-label="@lang('site.a11y.menu')">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <nav id="nav-mobile" x-show="open" x-cloak
             x-transition:enter="transition ease-pib duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="flex flex-col gap-1 border-t border-linen-200 py-3 lg:hidden">
            @foreach ($navItems as $route => $key)
                @php $active = $current === $route; @endphp
                <a href="{{ route($route, $l) }}"
                   @if ($active) aria-current="page" @endif
                   class="rounded-lg border-l-2 px-3 py-2 font-semibold transition duration-200 ease-pib hover:bg-linen-100 hover:text-ouro-700 {{ $active ? 'border-ouro-500 bg-linen-100/60 text-ouro-700' : 'border-transparent text-onyx-700' }}">
                    @lang($key)
                </a>
            @endforeach
        </nav>
    </div>
</header>
