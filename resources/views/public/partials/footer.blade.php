@php $l = app()->getLocale(); @endphp

<footer class="mt-16 border-t border-linen-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-8 md:grid-cols-[1fr_auto] md:items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia" class="h-12 w-12 rounded-xl object-cover">
                <div>
                    <span class="block font-display text-xl text-onyx-950">Portal Invest Bahia</span>
                    <span class="block text-sm text-onyx-500">@lang('site.footer.tagline')</span>
                </div>
            </div>
            <nav class="flex flex-wrap gap-4 text-sm font-semibold text-onyx-600">
                <a href="{{ route('public.fazenda', $l) }}" class="hover:text-ouro-700">@lang('site.nav.fazenda')</a>
                <a href="{{ route('public.ativo', $l) }}" class="hover:text-ouro-700">@lang('site.nav.ativo')</a>
                <a href="{{ route('public.servico', $l) }}" class="hover:text-ouro-700">@lang('site.nav.servico')</a>
                <a href="{{ route('public.contatos', $l) }}" class="hover:text-ouro-700">@lang('site.nav.contatos')</a>
            </nav>
        </div>
        <p class="mt-8 text-sm text-onyx-500">
            &copy; {{ date('Y') }} Portal Invest Bahia. @lang('site.footer.rights')
        </p>
    </div>
</footer>

<style>[x-cloak]{display:none!important}</style>
