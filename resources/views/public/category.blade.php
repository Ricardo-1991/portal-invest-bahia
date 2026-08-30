@extends('layouts.public')

@php
    $l = app()->getLocale();
    $isAllCategories = $category === 'all';
    // Caminhos relativos a public/ — o componente resolve a URL e a versão .webp.
    $categoryImages = [
        'all' => 'images/hero-rural-bahia.png',
        'fazenda' => 'images/category-fazenda.png',
        'ativo' => 'images/category-ativo.png',
        'servico' => 'images/category-servico.png',
    ];
@endphp

@section('title', $isAllCategories ? __('site.listing.all_title') : ($page->getTranslation('title', $l, false) ?: __('site.nav.'.$category)))
@section('description', __('site.categories.'.$category))

@section('content')
    <div x-data="categorySearch(
        @js(route('public.'.$category, $l)),
        @js($filters),
        @js([
            'all' => route('public.all', $l),
            'fazenda' => route('public.fazenda', $l),
            'ativo' => route('public.ativo', $l),
            'servico' => route('public.servico', $l),
        ]),
        @js($regionsByCategory)
    )">
        {{-- Faixa de categoria inspirada no cabeçalho compacto da referência. --}}
        <x-media-hero :poster="$categoryImages[$category]" eager media-class="opacity-20 object-[center_58%]" class="border-b border-linen-300 bg-linen-100">
            <x-slot:overlay>
                <div class="absolute inset-0 bg-gradient-to-r from-linen-100 via-linen-100/95 to-linen-100/55"></div>
            </x-slot:overlay>

            <div class="mx-auto max-w-7xl px-4 py-11 sm:px-6 lg:px-8 lg:py-14">
                <span data-reveal class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">
                    @lang('site.listing.category')
                </span>
                <h1 data-reveal style="--reveal-delay: 90ms"
                    class="mt-2 max-w-3xl font-display text-4xl font-semibold leading-tight tracking-tight text-onyx-950 md:text-5xl">
                    {{ $isAllCategories ? __('site.listing.all_title') : ($page->getTranslation('title', $l, false) ?: __('site.nav.'.$category)) }}
                </h1>
                <div data-reveal style="--reveal-delay: 180ms">
                    @if (! $isAllCategories && ($body = $page->getTranslation('body', $l, false)))
                        <div class="prose prose-pib mt-4 max-w-2xl text-onyx-600">{!! $body !!}</div>
                    @else
                        <p class="mt-4 max-w-2xl leading-relaxed text-onyx-600">{{ __('site.categories.'.$category) }}</p>
                    @endif
                </div>
            </div>
        </x-media-hero>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-12">
            @include('public.partials.listing-filters', ['filterMode' => 'category'])

            <div id="category-results" class="mt-9 transition-opacity duration-300 ease-pib" :class="loading && 'opacity-50'">
                @include('public.partials.category-results')
            </div>
        </section>
    </div>
@endsection
