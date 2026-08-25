@php $l = app()->getLocale(); @endphp

{{-- Card vertical — usado no grid de destaques da home. --}}
<a href="{{ route('public.listing', [$l, $listing->slug]) }}"
   class="group flex h-full w-full flex-col overflow-hidden rounded-panel border border-linen-200 bg-white shadow-card transition duration-500 ease-pib hover:-translate-y-1 hover:shadow-card-hover">
    <div class="relative aspect-[16/11] w-full overflow-hidden bg-linen-100">
        <img src="{{ $listing->mainImageUrl('thumb') ?: asset('images/property-placeholder.webp') }}"
             alt="{{ $listing->mainImageUrl('thumb') ? $listing->title : '' }}"
             loading="lazy" decoding="async"
             class="size-full object-cover transition-transform duration-700 ease-pib group-hover:scale-105">

        {{-- Gradiente na base da foto: segura o contraste do selo em imagens claras. --}}
        <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-onyx-950/25 to-transparent"></div>

        <span class="absolute left-4 top-4 rounded-pill bg-white/90 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-onyx-800 shadow-sm backdrop-blur">
            {{ __('site.nav.'.$listing->category) }}
        </span>
    </div>

    <div class="flex flex-1 flex-col gap-4 p-6">
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-ouro-700">
            <span>{{ $listing->region ?: __('site.listing.on_request') }}</span>
            @if ((float) $listing->area > 0)
                <span class="h-1 w-1 rounded-pill bg-ouro-400"></span>
                <span class="tabular"><x-listing-area :listing="$listing" /></span>
            @endif
        </div>

        <div>
            <h3 class="line-clamp-2 font-display text-2xl leading-tight text-onyx-950 transition-colors duration-300 ease-pib group-hover:text-ouro-700">
                {{ $listing->title }}
            </h3>
            @if ($listing->subtitle)
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-onyx-600">{{ $listing->subtitle }}</p>
            @endif
        </div>

        {{-- mt-auto fixa o rodapé na base: os CTAs alinham entre cards de alturas diferentes. --}}
        <div class="mt-auto flex items-end justify-between gap-4 border-t border-linen-200 pt-5">
            <x-listing-price :listing="$listing" size="sm" />

            <span class="shrink-0 rounded-pill bg-onyx-950 px-4 py-2 text-sm font-semibold text-white transition duration-300 ease-pib group-hover:bg-ouro-700">
                @lang('site.cta.details')
            </span>
        </div>
    </div>
</a>
