@extends('layouts.public')

@php
    $l = app()->getLocale();
    $broker = $listing->user;

    $images = collect();
    if ($main = $listing->mainImageUrl()) {
        $images->push($main);
    }
    foreach ($gallery as $media) {
        $images->push($media->getUrl());
    }
    if ($images->isEmpty()) {
        $images->push(asset('images/property-placeholder.webp'));
    }

    $whatsapp = preg_replace('/\D/', '', (string) $broker?->whatsapp);
    $emailTo = $broker?->email_public ?: $broker?->email;
    $subject = rawurlencode($listing->title);

    $hasArea = (float) $listing->area > 0;
@endphp

@section('title', $listing->title)
@section('description', $listing->subtitle ?: \Illuminate\Support\Str::limit(strip_tags((string) $listing->description), 155))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="mb-8 text-sm font-semibold text-onyx-600">
            <a href="{{ route('public.'.$listing->category, $l) }}"
               class="group inline-flex items-center gap-2 transition-colors duration-200 ease-pib hover:text-ouro-700">
                <span aria-hidden="true" class="transition-transform duration-300 ease-pib group-hover:-translate-x-1">&larr;</span>
                {{ __('site.nav.'.$listing->category) }}
            </a>
        </nav>

        <div class="grid gap-8 lg:grid-cols-[1.5fr_0.8fr]">
            <div>
                <div x-data="{ i: 0 }" data-reveal>
                    <div class="aspect-[16/10] w-full overflow-hidden rounded-panel border border-linen-200 bg-white shadow-card">
                        @foreach ($images as $index => $url)
                            <img x-show="i === {{ $index }}" src="{{ $url }}" alt="{{ $listing->title }}"
                                 @if ($index > 0) x-cloak loading="lazy" @endif
                                 class="size-full object-cover">
                        @endforeach
                    </div>

                    @if ($images->count() > 1)
                        <div class="mt-5">
                            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-onyx-500">
                                @lang('site.listing.gallery')
                            </span>
                            <div class="mt-3 flex items-center gap-3 overflow-x-auto pb-2">
                                @foreach ($images as $index => $url)
                                    <button type="button" @click="i = {{ $index }}"
                                            :class="i === {{ $index }} ? 'ring-2 ring-ouro-500 ring-offset-2 ring-offset-linen-50' : 'opacity-70 hover:opacity-100'"
                                            class="h-20 w-24 shrink-0 overflow-hidden rounded-card border border-linen-200 bg-white shadow-sm transition duration-300 ease-pib">
                                        <img src="{{ $url }}" class="size-full object-cover" alt="" loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <article class="mt-8 rounded-panel border border-linen-200 bg-white p-6 shadow-card md:p-10" data-reveal style="--reveal-delay: 100ms">
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">
                        {{ __('site.nav.'.$listing->category) }}
                    </span>
                    <h1 class="mt-3 font-display text-4xl font-medium leading-[1.1] tracking-tight text-onyx-950 md:text-5xl">{{ $listing->title }}</h1>
                    @if ($listing->subtitle)
                        <p class="mt-4 text-xl leading-relaxed text-onyx-600">{{ $listing->subtitle }}</p>
                    @endif

                    <dl class="mt-9 grid gap-6 border-y border-linen-200 py-7 sm:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">@lang('site.listing.category')</dt>
                            <dd class="mt-1.5 font-semibold text-onyx-950">{{ __('site.nav.'.$listing->category) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">@lang('site.listing.region')</dt>
                            <dd class="mt-1.5 font-semibold text-onyx-950">{{ $listing->region ?: __('site.listing.on_request') }}</dd>
                        </div>
                        @if ($hasArea)
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">@lang('site.listing.area')</dt>
                                <dd class="mt-1.5 font-semibold text-onyx-950 tabular"><x-listing-area :listing="$listing" /></dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">@lang('site.listing.price')</dt>
                            <dd class="mt-1.5">
                                <x-listing-price :listing="$listing" size="sm" :label="false" />
                            </dd>
                        </div>
                    </dl>

                    <p class="mt-7 whitespace-pre-line text-lg leading-relaxed text-onyx-700">{{ $listing->description }}</p>
                </article>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start" data-reveal style="--reveal-delay: 180ms">
                <div class="rounded-panel border border-linen-200 bg-white p-6 shadow-panel">
                    <div class="rounded-card bg-linen-50 p-6">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">
                            {{ __('site.nav.'.$listing->category) }}
                        </span>

                        <x-listing-price :listing="$listing" size="lg" class="mt-5" />

                        <div class="mt-5 grid gap-4 border-t border-linen-200 pt-5 sm:grid-cols-2 lg:grid-cols-1">
                            <div>
                                <span class="block text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">@lang('site.listing.region')</span>
                                <span class="mt-1 block font-semibold text-onyx-950">{{ $listing->region ?: __('site.listing.on_request') }}</span>
                            </div>
                            @if ($hasArea)
                                <div>
                                    <span class="block text-xs font-semibold uppercase tracking-[0.14em] text-onyx-500">@lang('site.listing.area')</span>
                                    <span class="mt-1 block font-semibold text-onyx-950 tabular"><x-listing-area :listing="$listing" /></span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-7">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.cta.contact_broker')</span>
                        <h2 class="mt-3 font-display text-2xl text-onyx-950">{{ $broker?->name }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-onyx-600">@lang('site.contact.card_text')</p>
                    </div>

                    <div class="mt-7 flex flex-col gap-3">
                        @if ($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}?text={{ $subject }}" target="_blank" rel="noopener"
                               class="inline-flex items-center justify-center gap-2 rounded-pill bg-sage-700 px-5 py-3 text-center font-semibold text-white transition duration-300 ease-pib hover:bg-sage-800 active:scale-[0.98]">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                @lang('site.cta.whatsapp')
                            </a>
                        @endif
                        @if ($emailTo)
                            <a href="mailto:{{ $emailTo }}?subject={{ $subject }}"
                               class="rounded-pill border border-ouro-500 px-5 py-3 text-center font-semibold text-onyx-950 transition duration-300 ease-pib hover:bg-ouro-400 active:scale-[0.98]">
                                @lang('site.cta.email')
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
