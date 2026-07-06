@php $l = app()->getLocale(); @endphp
<header x-data="{ open: false }" class="sticky top-0 z-30 border-b border-onyx-700 bg-onyx-950/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            {{-- Logo real (monograma PB dourado sobre preto) --}}
            <a href="{{ route('public.home', $l) }}" class="flex shrink-0 items-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                     class="h-12 w-12 rounded-lg object-cover">
            </a>

            {{-- Navegação (desktop, uma única linha) --}}
            <nav class="hidden min-w-0 items-center gap-5 lg:flex xl:gap-7">
                <a href="{{ route('public.home', $l) }}" class="whitespace-nowrap text-sm font-medium text-onyx-200 transition hover:text-ouro-400">@lang('site.nav.home')</a>
                <a href="{{ route('public.fazenda', $l) }}" class="whitespace-nowrap text-sm font-medium text-onyx-200 transition hover:text-ouro-400">@lang('site.nav.fazenda')</a>
                <a href="{{ route('public.ativo', $l) }}" class="whitespace-nowrap text-sm font-medium text-onyx-200 transition hover:text-ouro-400">@lang('site.nav.ativo')</a>
                <a href="{{ route('public.servico', $l) }}" class="whitespace-nowrap text-sm font-medium text-onyx-200 transition hover:text-ouro-400">@lang('site.nav.servico')</a>
                <a href="{{ route('public.informacoes', $l) }}" class="whitespace-nowrap text-sm font-medium text-onyx-200 transition hover:text-ouro-400">@lang('site.nav.informacoes')</a>
                <a href="{{ route('public.contatos', $l) }}" class="whitespace-nowrap text-sm font-medium text-onyx-200 transition hover:text-ouro-400">@lang('site.nav.contatos')</a>
            </nav>

            <div class="flex shrink-0 items-center gap-3">
                {{-- Seletor de idioma --}}
                <div x-data="{ langOpen: false }" class="relative">
                    <button @click="langOpen = !langOpen"
                            class="flex items-center gap-1 rounded-lg border border-onyx-700 px-2.5 py-1.5 text-sm uppercase text-onyx-200 transition hover:border-ouro-500 hover:text-ouro-400">
                        {{ $l }}
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="langOpen" @click.outside="langOpen = false" x-cloak
                         class="absolute right-0 z-20 mt-2 w-40 overflow-hidden rounded-lg border border-onyx-700 bg-onyx-900 shadow-xl">
                        @foreach ($localeUrls as $code => $url)
                            <a href="{{ $url }}"
                               class="block px-3 py-2 text-sm text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400 {{ $code === $l ? 'text-ouro-400 font-semibold' : '' }}">
                                {{ config('pib.locales')[$code] }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Botão menu (mobile/tablet) --}}
                <button @click="open = !open" class="text-onyx-200 lg:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Navegação (mobile/tablet) --}}
        <nav x-show="open" x-cloak class="flex flex-col gap-1 border-t border-onyx-700 py-3 lg:hidden">
            <a href="{{ route('public.home', $l) }}" class="rounded-lg px-2 py-2 text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400">@lang('site.nav.home')</a>
            <a href="{{ route('public.fazenda', $l) }}" class="rounded-lg px-2 py-2 text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400">@lang('site.nav.fazenda')</a>
            <a href="{{ route('public.ativo', $l) }}" class="rounded-lg px-2 py-2 text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400">@lang('site.nav.ativo')</a>
            <a href="{{ route('public.servico', $l) }}" class="rounded-lg px-2 py-2 text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400">@lang('site.nav.servico')</a>
            <a href="{{ route('public.informacoes', $l) }}" class="rounded-lg px-2 py-2 text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400">@lang('site.nav.informacoes')</a>
            <a href="{{ route('public.contatos', $l) }}" class="rounded-lg px-2 py-2 text-onyx-200 transition hover:bg-onyx-800 hover:text-ouro-400">@lang('site.nav.contatos')</a>
        </nav>
    </div>
</header>
