@php $l = app()->getLocale(); @endphp

<a href="{{ route('public.listing', [$l, $listing->slug]) }}"
   class="group flex h-full flex-col overflow-hidden rounded-[2rem] border border-linen-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
    <div class="relative aspect-[16/11] w-full overflow-hidden bg-linen-100">
        @if ($url = $listing->mainImageUrl('thumb'))
            <img src="{{ $url }}" alt="{{ $listing->title }}" loading="lazy"
                 class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
        @else
            <img src="{{ asset('images/property-placeholder.png') }}" alt="" loading="lazy"
                 class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
        @endif

        <div class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-onyx-800 shadow-sm">
            {{ __('site.nav.'.$listing->category) }}
        </div>
    </div>

    <div class="flex flex-1 flex-col gap-4 p-5">
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-ouro-700">
            <span>{{ $listing->region ?: __('site.listing.on_request') }}</span>
            <span class="h-1 w-1 rounded-full bg-ouro-400"></span>
            <span>{{ __('site.nav.'.$listing->category) }}</span>
        </div>

        <div>
            <h3 class="line-clamp-2 font-display text-2xl leading-tight text-onyx-950">{{ $listing->title }}</h3>
        @if ($listing->subtitle)
            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-onyx-600">{{ $listing->subtitle }}</p>
        @endif
        </div>

        <div class="mt-auto flex items-end justify-between gap-4 border-t border-linen-200 pt-4">
            <div>
                <span class="block text-xs uppercase tracking-wide text-onyx-500">@lang('site.listing.price')</span>
                <span class="block text-lg font-semibold leading-tight text-ouro-700">
                    @if ($listing->price)
                        R$ {{ number_format((float) $listing->price, 2, ',', '.') }}
                    @else
                        @lang('site.listing.on_request')
                    @endif
                </span>
            </div>

            <span class="shrink-0 rounded-full bg-onyx-950 px-4 py-2 text-sm font-semibold text-white transition group-hover:bg-ouro-700">
                @lang('site.cta.details')
            </span>
        </div>
    </div>
</a>
