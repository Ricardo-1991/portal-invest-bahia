@php
    $l = app()->getLocale();
    $detailUrl = route('public.listing', [$l, $listing->slug]);
    $showWhatsapp = $showWhatsapp ?? false;
    $whatsapp = preg_replace('/\D/', '', (string) $listing->user?->whatsapp);
    $waText = rawurlencode($listing->title.' - '.$detailUrl);
@endphp

{{-- Card vertical — usado na home e no catálogo, inspirado na grade da referência. --}}
<article class="group flex h-full w-full flex-col overflow-hidden rounded-panel border border-linen-200 bg-white shadow-card transition duration-500 ease-pib hover:-translate-y-1 hover:border-linen-300 hover:shadow-card-hover">
    <a href="{{ $detailUrl }}" class="listing-media relative block aspect-[4/3] w-full overflow-hidden bg-linen-100">
        <img src="{{ $listing->mainImageUrl('public_thumb') ?: asset('images/property-placeholder.webp') }}"
             alt="{{ $listing->mainImageUrl('public_thumb') ? $listing->title : '' }}"
             loading="lazy" decoding="async"
             class="size-full object-contain p-1 transition-opacity duration-300 ease-pib group-hover:opacity-95">

        <div class="absolute inset-x-0 top-0 flex items-center justify-between bg-onyx-950/62 px-4 py-2.5 text-xs font-semibold text-white backdrop-blur-sm transition-colors duration-300 group-hover:bg-ouro-600/92">
            <span>@lang('site.cta.details')</span>
            <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
        </div>
    </a>

    <div class="flex flex-1 flex-col gap-3 p-5">
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-pill bg-ouro-200/55 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-ouro-700">{{ __('site.nav.'.$listing->category) }}</span>
            <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-onyx-500">{{ $listing->region ?: __('site.listing.on_request') }}</span>
            @if ((float) $listing->area > 0)
                <span class="h-1 w-1 rounded-pill bg-ouro-400"></span>
                <span class="text-[11px] font-semibold uppercase tracking-[0.12em] text-onyx-500 tabular"><x-listing-area :listing="$listing" /></span>
            @endif
        </div>

        <div>
            <h3 class="line-clamp-2 font-display text-[1.35rem] font-semibold leading-tight text-onyx-950">
                <a href="{{ $detailUrl }}" class="transition-colors duration-300 ease-pib group-hover:text-ouro-700">{{ $listing->title }}</a>
            </h3>
            @if ($listing->subtitle)
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-onyx-600">{{ $listing->subtitle }}</p>
            @endif
        </div>

        <div class="mt-auto flex items-end justify-between gap-3 border-t border-linen-200 pt-4">
            <x-listing-price :listing="$listing" size="sm" />

            @if ($showWhatsapp && $whatsapp)
                <a href="https://wa.me/{{ $whatsapp }}?text={{ $waText }}" target="_blank" rel="noopener"
                   class="inline-flex shrink-0 items-center gap-1.5 rounded-card bg-sage-700 px-3 py-2 text-xs font-semibold text-white transition duration-300 hover:bg-sage-800 active:scale-[0.98]">
                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                    @lang('site.cta.whatsapp')
                </a>
            @endif
        </div>
    </div>
</article>
