@extends('layouts.public')

@php
    $l = app()->getLocale();
    $broker = $listing->user;

    // Monta a galeria: imagem principal primeiro, depois a galeria.
    $images = collect();
    if ($main = $listing->mainImageUrl()) {
        $images->push($main);
    }
    foreach ($gallery as $media) {
        $images->push($media->getUrl());
    }

    // Contato do corretor.
    $whatsapp = preg_replace('/\D/', '', (string) $broker?->whatsapp);
    $emailTo = $broker?->email_public ?: $broker?->email;
    $subject = rawurlencode($listing->title);
@endphp

@section('title', $listing->title)

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <nav class="mb-6 text-sm text-onyx-400">
            <a href="{{ route('public.'.$listing->category, $l) }}" class="transition hover:text-ouro-400">
                &larr; {{ __('site.nav.'.$listing->category) }}
            </a>
        </nav>

        <div class="grid gap-10 lg:grid-cols-2">
            {{-- Galeria --}}
            <div x-data="{ i: 0, total: {{ $images->count() }} }">
                <div class="aspect-[4/3] w-full overflow-hidden rounded-2xl border border-onyx-700 bg-onyx-900">
                    @if ($images->isNotEmpty())
                        @foreach ($images as $index => $url)
                            <img x-show="i === {{ $index }}" src="{{ $url }}" alt="{{ $listing->title }}"
                                 class="h-full w-full object-cover" @if($index > 0) x-cloak @endif>
                        @endforeach
                    @else
                        <div class="grid h-full w-full place-items-center">
                            <img src="{{ asset('images/logo.jpeg') }}" alt="" class="h-16 w-16 rounded-lg object-cover opacity-40">
                        </div>
                    @endif
                </div>

                @if ($images->count() > 1)
                    <div class="mt-3 flex items-center justify-between">
                        <button @click="i = (i - 1 + total) % total" class="rounded-lg border border-onyx-700 px-3 py-1 text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">‹</button>
                        <div class="flex gap-2">
                            @foreach ($images as $index => $url)
                                <button @click="i = {{ $index }}"
                                        :class="i === {{ $index }} ? 'ring-2 ring-ouro-500' : ''"
                                        class="h-14 w-14 overflow-hidden rounded-lg border border-onyx-700">
                                    <img src="{{ $url }}" class="h-full w-full object-cover" alt="">
                                </button>
                            @endforeach
                        </div>
                        <button @click="i = (i + 1) % total" class="rounded-lg border border-onyx-700 px-3 py-1 text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">›</button>
                    </div>
                @endif
            </div>

            {{-- Informações --}}
            <div>
                <span class="text-xs font-semibold uppercase tracking-wide text-ouro-500">
                    {{ __('site.nav.'.$listing->category) }}
                </span>
                <h1 class="mt-1 font-display text-3xl text-onyx-50">{{ $listing->title }}</h1>
                @if ($listing->subtitle)
                    <p class="mt-1 text-lg text-onyx-300">{{ $listing->subtitle }}</p>
                @endif

                <dl class="mt-6 grid grid-cols-2 gap-4">
                    @if ($listing->region)
                        <div>
                            <dt class="text-xs uppercase text-onyx-500">@lang('site.listing.region')</dt>
                            <dd class="font-semibold text-onyx-100">{{ $listing->region }}</dd>
                        </div>
                    @endif
                    @if ($listing->price)
                        <div>
                            <dt class="text-xs uppercase text-onyx-500">@lang('site.listing.price')</dt>
                            <dd class="font-semibold text-ouro-400">R$ {{ number_format((float) $listing->price, 2, ',', '.') }}</dd>
                        </div>
                    @endif
                </dl>

                <p class="mt-6 whitespace-pre-line leading-relaxed text-onyx-200">{{ $listing->description }}</p>

                {{-- Contato do corretor --}}
                <div class="mt-8 rounded-2xl border border-onyx-700 bg-onyx-900 p-5">
                    <p class="font-semibold text-onyx-50">{{ $broker?->name }}</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if ($whatsapp)
                            <a href="https://wa.me/{{ $whatsapp }}?text={{ $subject }}" target="_blank" rel="noopener"
                               class="rounded-lg bg-green-600 px-5 py-2.5 font-semibold text-white transition hover:bg-green-700">
                                @lang('site.cta.whatsapp')
                            </a>
                        @endif
                        @if ($emailTo)
                            <a href="mailto:{{ $emailTo }}?subject={{ $subject }}"
                               class="rounded-lg border border-ouro-500 px-5 py-2.5 font-semibold text-ouro-400 transition hover:bg-ouro-400 hover:text-onyx-950">
                                @lang('site.cta.email')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
