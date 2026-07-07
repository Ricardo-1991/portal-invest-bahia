@if ($listings->isEmpty())
    <div class="mt-10 rounded-3xl border border-linen-200 bg-white p-10 text-center shadow-sm">
        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.empty.eyebrow')</span>
        <h2 class="mt-3 font-display text-3xl text-onyx-950">@lang('site.listing.no_results')</h2>
        <p class="mx-auto mt-3 max-w-2xl text-onyx-600">@lang('site.empty.search_text')</p>
        <a href="{{ route('public.contatos', app()->getLocale()) }}"
           class="mt-6 inline-flex rounded-full bg-onyx-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-ouro-700">
            @lang('site.cta.contact_broker')
        </a>
    </div>
@else
    {{-- Lista vertical de cards horizontais (1 por linha, referência chaozao) --}}
    <div class="mt-10 space-y-6">
        @foreach ($listings as $listing)
            @include('public.partials.listing-card-horizontal', ['listing' => $listing])
        @endforeach
    </div>

    <div class="mt-8">{{ $listings->links() }}</div>
@endif
