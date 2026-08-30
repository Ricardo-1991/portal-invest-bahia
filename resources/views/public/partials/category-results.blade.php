{{--
    Fragmento reaproveitado na resposta AJAX da busca ao vivo (X-PIB-Partial),
    injetado via innerHTML em app.js. Duas consequências:
      - Alpine não inicializa componentes aqui dentro;
      - [data-reveal] também não funciona (o IntersectionObserver só observa o
        que existia no load), então nada de animação de entrada neste arquivo.
    Todo o estado visual daqui tem que ser CSS puro.
--}}
@if ($listings->isEmpty())
    <div class="rounded-panel border border-linen-200 bg-white p-10 text-center shadow-card">
        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.empty.eyebrow')</span>
        <h2 class="mt-3 font-display text-2xl text-onyx-950 md:text-3xl">@lang('site.listing.no_results')</h2>
        <p class="mx-auto mt-3 max-w-2xl leading-relaxed text-onyx-600">@lang('site.empty.search_text')</p>
        <a href="{{ route('public.contatos', app()->getLocale()) }}"
           class="mt-7 inline-flex rounded-pill bg-onyx-950 px-6 py-3 text-sm font-semibold text-white transition duration-300 ease-pib hover:bg-ouro-700 active:scale-95">
            @lang('site.cta.contact_broker')
        </a>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($listings as $listing)
            @include('public.partials.listing-card', ['listing' => $listing, 'showWhatsapp' => true])
        @endforeach
    </div>

    {{ $listings->links() }}
@endif
