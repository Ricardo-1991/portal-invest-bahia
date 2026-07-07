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
        $images->push(asset('images/property-placeholder.png'));
    }

    $whatsapp = preg_replace('/\D/', '', (string) $broker?->whatsapp);
    $emailTo = $broker?->email_public ?: $broker?->email;
    $subject = rawurlencode($listing->title);
@endphp

@section('title', $listing->title)

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm font-semibold text-onyx-600">
            <a href="{{ route('public.'.$listing->category, $l) }}" class="transition hover:text-ouro-700">
                &larr; {{ __('site.nav.'.$listing->category) }}
            </a>
        </nav>

        <div class="grid gap-8 lg:grid-cols-[1.5fr_0.8fr]">
            <div>
                <div x-data="{ i: 0, total: {{ $images->count() }} }">
                    <div class="aspect-[16/10] w-full overflow-hidden rounded-3xl border border-linen-200 bg-white shadow-sm">
                        @foreach ($images as $index => $url)
                            <img x-show="i === {{ $index }}" src="{{ $url }}" alt="{{ $listing->title }}"
                                 class="h-full w-full object-cover" @if($index > 0) x-cloak @endif>
                        @endforeach
                    </div>

                    @if ($images->count() > 1)
                        <div class="mt-4 flex items-center gap-3 overflow-x-auto pb-2">
                            @foreach ($images as $index => $url)
                                <button @click="i = {{ $index }}"
                                        :class="i === {{ $index }} ? 'ring-2 ring-ouro-500' : ''"
                                        class="h-20 w-24 shrink-0 overflow-hidden rounded-2xl border border-linen-200 bg-white shadow-sm">
                                    <img src="{{ $url }}" class="h-full w-full object-cover" alt="">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <article class="mt-8 rounded-3xl border border-linen-200 bg-white p-6 shadow-sm md:p-8">
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">
                        {{ __('site.nav.'.$listing->category) }}
                    </span>
                    <h1 class="mt-3 font-display text-4xl leading-tight text-onyx-950 md:text-5xl">{{ $listing->title }}</h1>
                    @if ($listing->subtitle)
                        <p class="mt-3 text-xl leading-relaxed text-onyx-600">{{ $listing->subtitle }}</p>
                    @endif

                    <dl class="mt-8 grid gap-4 border-y border-linen-200 py-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-onyx-500">@lang('site.listing.category')</dt>
                            <dd class="mt-1 font-semibold text-onyx-950">{{ __('site.nav.'.$listing->category) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-onyx-500">@lang('site.listing.region')</dt>
                            <dd class="mt-1 font-semibold text-onyx-950">{{ $listing->region ?: __('site.listing.on_request') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-onyx-500">@lang('site.listing.price')</dt>
                            <dd class="mt-1 font-semibold text-ouro-700">
                                @if ($listing->price)
                                    R$ {{ number_format((float) $listing->price, 2, ',', '.') }}
                                @else
                                    @lang('site.listing.on_request')
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <p class="mt-6 whitespace-pre-line text-lg leading-relaxed text-onyx-700">{{ $listing->description }}</p>
                </article>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="rounded-[2rem] border border-linen-200 bg-white p-6 shadow-xl">
                    <div class="rounded-3xl bg-linen-50 p-5">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">
                            {{ __('site.nav.'.$listing->category) }}
                        </span>
                        <div class="mt-4">
                            <span class="block text-xs uppercase tracking-wide text-onyx-500">@lang('site.listing.price')</span>
                            <strong class="mt-1 block font-display text-3xl font-semibold text-onyx-950">
                                @if ($listing->price)
                                    R$ {{ number_format((float) $listing->price, 2, ',', '.') }}
                                @else
                                    @lang('site.listing.on_request')
                                @endif
                            </strong>
                        </div>
                        <div class="mt-4 border-t border-white pt-4">
                            <span class="block text-xs uppercase tracking-wide text-onyx-500">@lang('site.listing.region')</span>
                            <span class="mt-1 block font-semibold text-onyx-950">{{ $listing->region ?: __('site.listing.on_request') }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.cta.contact_broker')</span>
                        <h2 class="mt-3 font-display text-2xl text-onyx-950">{{ $broker?->name }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-onyx-600">@lang('site.contact.card_text')</p>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        @if ($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}?text={{ $subject }}" target="_blank" rel="noopener"
                               class="rounded-full bg-green-600 px-5 py-3 text-center font-semibold text-white transition hover:bg-green-700">
                                @lang('site.cta.whatsapp')
                            </a>
                        @endif
                        @if ($emailTo)
                            <a href="mailto:{{ $emailTo }}?subject={{ $subject }}"
                               class="rounded-full border border-ouro-500 px-5 py-3 text-center font-semibold text-onyx-950 transition hover:bg-ouro-400">
                                @lang('site.cta.email')
                            </a>
                        @endif
                    </div>

                    <div class="mt-6 rounded-3xl border border-linen-200 p-4 text-sm leading-relaxed text-onyx-600">
                        <strong class="block text-onyx-950">@lang('site.listing.category')</strong>
                        <span>{{ __('site.nav.'.$listing->category) }}</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
