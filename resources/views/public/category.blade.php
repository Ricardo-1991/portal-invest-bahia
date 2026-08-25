@extends('layouts.public')

@php
    $l = app()->getLocale();
    // Caminhos relativos a public/ — o componente resolve a URL e a versão .webp.
    $categoryImages = [
        'fazenda' => 'images/category-fazenda.png',
        'ativo' => 'images/category-ativo.png',
        'servico' => 'images/category-servico.png',
    ];
@endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.'.$category))
@section('description', __('site.categories.'.$category))

@section('content')
    <div x-data="categorySearch(@js(route('public.'.$category, $l)), @js($filters))">
        {{-- Mesmo componente do hero da home, aqui sem vídeo. --}}
        <x-media-hero :poster="$categoryImages[$category]" eager media-class="opacity-55" class="bg-onyx-950">
            <x-slot:overlay>
                <div class="absolute inset-0 bg-gradient-to-r from-onyx-950 via-onyx-950/70 to-onyx-950/10"></div>
            </x-slot:overlay>

            <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
                <span data-reveal class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200">
                    @lang('site.listing.category')
                </span>
                <h1 data-reveal style="--reveal-delay: 90ms"
                    class="mt-3 max-w-3xl font-display text-4xl font-medium leading-[1.08] tracking-tight text-white md:text-6xl">
                    {{ $page->getTranslation('title', $l, false) ?: __('site.nav.'.$category) }}
                </h1>
                <div data-reveal style="--reveal-delay: 180ms">
                    @if ($body = $page->getTranslation('body', $l, false))
                        <div class="prose prose-invert mt-6 max-w-2xl text-linen-100">{!! $body !!}</div>
                    @else
                        <p class="mt-6 max-w-2xl text-lg leading-relaxed text-linen-200">{{ __('site.categories.'.$category) }}</p>
                    @endif
                </div>
            </div>
        </x-media-hero>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            {{-- Margem negativa: a barra de filtros sobrepõe a base do hero. --}}
            <form method="GET" @submit.prevent="search()"
                  class="relative z-10 -mt-16 grid gap-3 rounded-panel border border-linen-200 bg-white p-4 shadow-panel md:grid-cols-[1fr_260px_auto]">
                <label for="filtro-q" class="sr-only">@lang('site.search.placeholder')</label>
                <input id="filtro-q" type="text" name="q" x-model="q" @input.debounce.400ms="search()"
                       placeholder="@lang('site.search.placeholder')"
                       class="min-h-12 rounded-card border border-linen-200 bg-linen-50 px-4 text-onyx-900 transition duration-200 ease-pib placeholder:text-onyx-500 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">

                <label for="filtro-regiao" class="sr-only">@lang('site.listing.region')</label>
                <select id="filtro-regiao" name="region" x-model="region" @change="search()"
                        class="min-h-12 rounded-card border border-linen-200 bg-linen-50 px-4 text-onyx-900 transition duration-200 ease-pib focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                    <option value="">@lang('site.search.region_all')</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </select>

                <button type="submit"
                        class="rounded-card bg-onyx-950 px-7 py-3 font-semibold text-white transition duration-300 ease-pib hover:bg-ouro-700 active:scale-[0.98]">
                    @lang('site.search.button')
                </button>
            </form>

            <div id="category-results" class="mt-10 transition-opacity duration-300 ease-pib" :class="loading && 'opacity-50'">
                @include('public.partials.category-results')
            </div>
        </section>
    </div>
@endsection
