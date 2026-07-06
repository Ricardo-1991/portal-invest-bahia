@php $l = app()->getLocale(); @endphp
<a href="{{ route('public.listing', [$l, $listing->slug]) }}"
   class="group flex flex-col overflow-hidden rounded-2xl border border-onyx-700 bg-onyx-800 transition hover:-translate-y-1 hover:border-ouro-600 hover:shadow-[0_16px_40px_-12px_rgba(212,175,55,0.25)]">
    <div class="aspect-[3/2] w-full overflow-hidden bg-onyx-900">
        @if ($url = $listing->mainImageUrl('thumb'))
            <img src="{{ $url }}" alt="{{ $listing->title }}" loading="lazy"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="grid h-full w-full place-items-center">
                <img src="{{ asset('images/logo.jpeg') }}" alt="" class="h-12 w-12 rounded-lg object-cover opacity-40">
            </div>
        @endif
    </div>
    <div class="flex flex-1 flex-col gap-1 p-4">
        <span class="text-xs font-semibold uppercase tracking-wide text-ouro-500">
            {{ __('site.nav.'.$listing->category) }}
        </span>
        <h3 class="line-clamp-2 font-display text-lg text-onyx-50">{{ $listing->title }}</h3>
        @if ($listing->subtitle)
            <p class="line-clamp-1 text-sm text-onyx-400">{{ $listing->subtitle }}</p>
        @endif
        <div class="mt-auto flex items-center justify-between pt-3 text-sm">
            @if ($listing->region)
                <span class="text-onyx-400">{{ $listing->region }}</span>
            @endif
            @if ($listing->price)
                <span class="font-semibold text-ouro-400">
                    R$ {{ number_format((float) $listing->price, 2, ',', '.') }}
                </span>
            @endif
        </div>
    </div>
</a>
