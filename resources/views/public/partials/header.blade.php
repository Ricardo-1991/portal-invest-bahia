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

    $contact = config('pib.contact');
    $phone = $contact['phones'][0] ?? null;
@endphp

<header x-data="{ open: false }" class="site-header sticky top-0 z-30 border-b border-linen-200 bg-white/95 backdrop-blur">
    <div class="border-t-[3px] border-ouro-500 bg-white">
        <div class="mx-auto flex min-h-20 max-w-7xl items-center justify-between gap-5 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('public.home', $l) }}" class="flex shrink-0 items-center gap-3.5">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                     width="56" height="56" decoding="async"
                     class="h-14 w-14 rounded-card object-cover shadow-card">
                <div class="hidden leading-tight sm:block lg:hidden xl:block">
                    <span class="block font-display text-xl font-semibold tracking-tight text-onyx-950">Portal Invest Bahia</span>
                    <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.2em] text-ouro-700">@lang('site.hero.eyebrow')</span>
                </div>
            </a>

            <div class="hidden items-center gap-8 lg:flex">
                @if ($phone)
                    <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="group flex items-center gap-3 text-sm">
                        <span class="grid h-9 w-9 place-items-center rounded-pill bg-sage-50 text-sage-700 transition group-hover:bg-sage-700 group-hover:text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        </span>
                        <span><span class="block text-[11px] text-onyx-500">@lang('site.contact.phone')</span><strong class="block font-semibold text-onyx-800 tabular">{{ $phone }}</strong></span>
                    </a>
                @endif
                <a href="{{ route('public.contatos', $l) }}"
                   class="rounded-card bg-onyx-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition duration-200 ease-pib hover:bg-ouro-600 active:scale-[0.98]">
                    @lang('site.cta.contact_broker')
                </a>
            </div>

            <div class="flex items-center gap-2 lg:hidden">
                <x-locale-flags :locale-urls="$localeUrls" />
                <button @click="open = !open"
                        :aria-expanded="open"
                        aria-controls="nav-mobile"
                        class="rounded-card border border-linen-300 bg-white p-2 text-onyx-700 transition duration-200 ease-pib hover:border-ouro-500 hover:text-ouro-700 active:scale-95"
                        aria-label="@lang('site.a11y.menu')">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="border-t border-linen-200 bg-linen-50/95">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="hidden items-center justify-between lg:flex">
                <nav class="flex min-w-0 items-center gap-8">
                    @foreach ($navItems as $route => $key)
                        @php $active = $current === $route; @endphp
                        <a href="{{ route($route, $l) }}" @if ($active) aria-current="page" @endif
                           class="group relative py-4 text-sm font-semibold transition-colors duration-200 hover:text-ouro-700 {{ $active ? 'text-ouro-700' : 'text-onyx-600' }}">
                            @lang($key)
                            <span class="absolute inset-x-0 bottom-0 h-0.5 origin-left bg-ouro-500 transition-transform duration-300 {{ $active ? 'scale-x-100' : 'scale-x-0 group-hover:scale-x-100' }}"></span>
                        </a>
                    @endforeach
                </nav>

                <x-locale-flags :locale-urls="$localeUrls" />
            </div>

            <nav id="nav-mobile" x-show="open" x-cloak
             x-transition:enter="transition ease-pib duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col gap-1 py-3 lg:hidden">
                @foreach ($navItems as $route => $key)
                    @php $active = $current === $route; @endphp
                    <a href="{{ route($route, $l) }}" @if ($active) aria-current="page" @endif
                       class="rounded-card border-l-2 px-3 py-2.5 font-semibold transition hover:bg-white hover:text-ouro-700 {{ $active ? 'border-ouro-500 bg-white text-ouro-700' : 'border-transparent text-onyx-700' }}">@lang($key)</a>
                @endforeach
            </nav>
        </div>
    </div>
</header>
