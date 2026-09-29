@php
    $l = app()->getLocale();
    $detailUrl = route('public.listing', [$l, $listing->slug]);

    // Contato do corretor (mesmo padrão da página de detalhe).
    $whatsapp = preg_replace('/\D/', '', (string) $listing->user?->whatsapp);
    $waText = rawurlencode($listing->title.' - '.$detailUrl);

    $excerpt = $listing->subtitle ?: $listing->description;
@endphp

{{-- Card horizontal: foto à esquerda, dados à direita.
     É um <article> (não um <a> único) porque contém o botão de WhatsApp. --}}
<article class="group overflow-hidden rounded-panel border border-linen-200 bg-white shadow-card transition-all duration-500 ease-pib hover:-translate-y-1 hover:shadow-card-hover md:grid md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]">
    <a href="{{ $detailUrl }}" class="relative block overflow-hidden bg-linen-100" tabindex="-1" aria-hidden="true">
        <img src="{{ $listing->mainImageUrl('public_thumb') ?: asset('images/property-placeholder.webp') }}"
             alt="" loading="lazy" decoding="async"
             class="aspect-[4/3] size-full object-contain p-1 transition-opacity duration-300 ease-pib group-hover:opacity-95 md:aspect-auto md:min-h-[280px]">
    </a>

    <div class="flex flex-col gap-3 p-6 md:p-7">
        <div class="flex flex-wrap items-center gap-2">
            <span class="rounded-pill bg-ouro-200/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-ouro-700">
                {{ __('site.nav.'.$listing->category) }}
            </span>
            @if ($listing->region)
                <span class="text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">{{ $listing->region }}</span>
            @endif
        </div>

        <h3 class="font-display text-2xl leading-tight text-onyx-950">
            <a href="{{ $detailUrl }}" class="transition-colors duration-300 ease-pib hover:text-ouro-700">{{ $listing->title }}</a>
        </h3>

        {{-- Metadados com ícones (área, categoria, região) --}}
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-onyx-600">
            @if ((float) $listing->area > 0)
                <span class="inline-flex items-center gap-1.5 tabular">
                    <svg class="h-4 w-4 shrink-0 text-ouro-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                    <x-listing-area :listing="$listing" />
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-4 w-4 shrink-0 text-ouro-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                {{ __('site.nav.'.$listing->category) }}
            </span>
            @if ($listing->region)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0 text-ouro-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    {{ $listing->region }}
                </span>
            @endif
        </div>

        @if ($excerpt)
            <p class="line-clamp-2 text-sm leading-relaxed text-onyx-600">{{ $excerpt }}</p>
        @endif

        <div class="mt-auto flex flex-wrap items-end justify-between gap-4 border-t border-linen-200 pt-5">
            <x-listing-price :listing="$listing" size="lg" />

            <div class="flex shrink-0 flex-wrap gap-2">
                <a href="{{ $detailUrl }}"
                   class="inline-flex items-center gap-1.5 rounded-pill border border-onyx-300 px-5 py-2.5 text-sm font-semibold text-onyx-800 transition-all duration-300 ease-pib hover:border-ouro-600 hover:text-ouro-700 active:scale-[0.98]">
                    @lang('site.cta.details')
                </a>
                @if ($whatsapp)
                    {{-- sage-700 em vez do verde padrão do Tailwind: mantém a paleta do site. --}}
                    <a href="https://wa.me/{{ $whatsapp }}?text={{ $waText }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 rounded-pill bg-sage-700 px-5 py-2.5 text-sm font-semibold text-white transition-all duration-300 ease-pib hover:bg-sage-800 active:scale-[0.98]">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                        @lang('site.cta.whatsapp')
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>
