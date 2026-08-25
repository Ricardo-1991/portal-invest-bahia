@php $l = app()->getLocale(); @endphp

<footer class="relative z-10 mt-16 border-t border-linen-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-[1fr_auto] md:items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                     width="48" height="48" loading="lazy" decoding="async"
                     class="h-12 w-12 rounded-card object-cover">
                <div>
                    <span class="block font-display text-xl text-onyx-950">Portal Invest Bahia</span>
                    <span class="block text-sm text-onyx-500">@lang('site.footer.tagline')</span>
                </div>
            </div>
            <nav class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold text-onyx-600">
                <a href="{{ route('public.home', $l) }}" class="transition-colors duration-200 ease-pib hover:text-ouro-700">@lang('site.nav.home')</a>
                <a href="{{ route('public.fazenda', $l) }}" class="transition-colors duration-200 ease-pib hover:text-ouro-700">@lang('site.nav.fazenda')</a>
                <a href="{{ route('public.ativo', $l) }}" class="transition-colors duration-200 ease-pib hover:text-ouro-700">@lang('site.nav.ativo')</a>
                <a href="{{ route('public.servico', $l) }}" class="transition-colors duration-200 ease-pib hover:text-ouro-700">@lang('site.nav.servico')</a>
                <a href="{{ route('public.informacoes', $l) }}" class="transition-colors duration-200 ease-pib hover:text-ouro-700">@lang('site.nav.informacoes')</a>
                <a href="{{ route('public.contatos', $l) }}" class="transition-colors duration-200 ease-pib hover:text-ouro-700">@lang('site.nav.contatos')</a>
            </nav>
        </div>
        <p class="mt-10 border-t border-linen-200 pt-6 text-sm text-onyx-500">
            &copy; {{ date('Y') }} Portal Invest Bahia. @lang('site.footer.rights')
        </p>
    </div>
</footer>
