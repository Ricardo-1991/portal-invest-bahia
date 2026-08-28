@php $l = app()->getLocale(); @endphp

<footer class="relative z-10 mt-20 border-t border-linen-300 bg-linen-100/80">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-16">
        <div class="grid gap-10 md:grid-cols-[1.2fr_0.8fr_0.8fr]">
            <div class="max-w-sm">
                <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                     width="48" height="48" loading="lazy" decoding="async"
                     class="h-12 w-12 rounded-card object-cover">
                <div>
                        <span class="block font-display text-xl font-semibold text-onyx-950">Portal Invest Bahia</span>
                        <span class="block text-xs font-semibold uppercase tracking-[0.16em] text-ouro-700">@lang('site.hero.eyebrow')</span>
                    </div>
                </div>
                <p class="mt-5 text-sm leading-relaxed text-onyx-600">@lang('site.footer.tagline')</p>
            </div>
            <div>
                <h2 class="border-l-4 border-ouro-500 pl-3 text-sm font-semibold text-onyx-950">Portal</h2>
                <nav class="mt-5 grid gap-2.5 text-sm text-onyx-600">
                    <a href="{{ route('public.home', $l) }}" class="transition hover:text-ouro-700">@lang('site.nav.home')</a>
                    <a href="{{ route('public.informacoes', $l) }}" class="transition hover:text-ouro-700">@lang('site.nav.informacoes')</a>
                    <a href="{{ route('public.contatos', $l) }}" class="transition hover:text-ouro-700">@lang('site.nav.contatos')</a>
                </nav>
            </div>
            <div>
                <h2 class="border-l-4 border-ouro-500 pl-3 text-sm font-semibold text-onyx-950">@lang('site.sections.portfolio_eyebrow')</h2>
                <nav class="mt-5 grid gap-2.5 text-sm text-onyx-600">
                    <a href="{{ route('public.fazenda', $l) }}" class="transition hover:text-ouro-700">@lang('site.nav.fazenda')</a>
                    <a href="{{ route('public.ativo', $l) }}" class="transition hover:text-ouro-700">@lang('site.nav.ativo')</a>
                    <a href="{{ route('public.servico', $l) }}" class="transition hover:text-ouro-700">@lang('site.nav.servico')</a>
                </nav>
            </div>
        </div>
    </div>
    <div class="border-t border-linen-300 bg-linen-200/60">
        <p class="mx-auto max-w-7xl px-4 py-5 text-xs text-onyx-500 sm:px-6 lg:px-8">&copy; {{ date('Y') }} Portal Invest Bahia. @lang('site.footer.rights')</p>
    </div>
</footer>
