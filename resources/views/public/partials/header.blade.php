@php $l = app()->getLocale(); @endphp

<header x-data="{ open: false }" class="sticky top-0 z-30 border-b border-linen-200 bg-linen-50/90 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-18 items-center justify-between gap-4 py-3">
            <a href="{{ route('public.home', $l) }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                     class="h-12 w-12 rounded-xl object-cover shadow-sm">
                <div class="hidden leading-tight sm:block">
                    <span class="block font-display text-lg font-semibold text-onyx-950">Portal Invest Bahia</span>
                    <span class="block text-xs uppercase tracking-[0.24em] text-ouro-700">Rural Real Estate</span>
                </div>
            </a>

            <nav class="hidden min-w-0 items-center gap-5 lg:flex xl:gap-7">
                <a href="{{ route('public.home', $l) }}" class="whitespace-nowrap text-sm font-semibold text-onyx-700 transition hover:text-ouro-700">@lang('site.nav.home')</a>
                <a href="{{ route('public.fazenda', $l) }}" class="whitespace-nowrap text-sm font-semibold text-onyx-700 transition hover:text-ouro-700">@lang('site.nav.fazenda')</a>
                <a href="{{ route('public.ativo', $l) }}" class="whitespace-nowrap text-sm font-semibold text-onyx-700 transition hover:text-ouro-700">@lang('site.nav.ativo')</a>
                <a href="{{ route('public.servico', $l) }}" class="whitespace-nowrap text-sm font-semibold text-onyx-700 transition hover:text-ouro-700">@lang('site.nav.servico')</a>
                <a href="{{ route('public.informacoes', $l) }}" class="whitespace-nowrap text-sm font-semibold text-onyx-700 transition hover:text-ouro-700">@lang('site.nav.informacoes')</a>
                <a href="{{ route('public.contatos', $l) }}" class="whitespace-nowrap text-sm font-semibold text-onyx-700 transition hover:text-ouro-700">@lang('site.nav.contatos')</a>
            </nav>

            <div class="flex shrink-0 items-center gap-3">
                <div x-data="{ langOpen: false }" class="relative">
                    <button @click="langOpen = !langOpen"
                            class="flex items-center gap-1 rounded-full border border-linen-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-onyx-700 shadow-sm transition hover:border-ouro-500 hover:text-ouro-700">
                        {{ $l }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="langOpen" @click.outside="langOpen = false" x-cloak
                         class="absolute right-0 z-20 mt-2 w-44 overflow-hidden rounded-xl border border-linen-200 bg-white shadow-xl">
                        @foreach ($localeUrls as $code => $url)
                            <a href="{{ $url }}"
                               class="block px-4 py-2.5 text-sm text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700 {{ $code === $l ? 'font-semibold text-ouro-700' : '' }}">
                                {{ config('pib.locales')[$code] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('public.contatos', $l) }}"
                   class="hidden rounded-full bg-onyx-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-ouro-700 md:inline-flex">
                    @lang('site.cta.contact_broker')
                </a>

                <button @click="open = !open" class="rounded-full border border-linen-300 bg-white p-2 text-onyx-700 lg:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <nav x-show="open" x-cloak class="flex flex-col gap-1 border-t border-linen-200 py-3 lg:hidden">
            <a href="{{ route('public.home', $l) }}" class="rounded-lg px-2 py-2 font-semibold text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700">@lang('site.nav.home')</a>
            <a href="{{ route('public.fazenda', $l) }}" class="rounded-lg px-2 py-2 font-semibold text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700">@lang('site.nav.fazenda')</a>
            <a href="{{ route('public.ativo', $l) }}" class="rounded-lg px-2 py-2 font-semibold text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700">@lang('site.nav.ativo')</a>
            <a href="{{ route('public.servico', $l) }}" class="rounded-lg px-2 py-2 font-semibold text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700">@lang('site.nav.servico')</a>
            <a href="{{ route('public.informacoes', $l) }}" class="rounded-lg px-2 py-2 font-semibold text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700">@lang('site.nav.informacoes')</a>
            <a href="{{ route('public.contatos', $l) }}" class="rounded-lg px-2 py-2 font-semibold text-onyx-700 transition hover:bg-linen-100 hover:text-ouro-700">@lang('site.nav.contatos')</a>
        </nav>
    </div>
</header>
