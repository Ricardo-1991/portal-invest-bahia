@extends('layouts.public')

@php
    $l = app()->getLocale();
    $categoryImages = [
        'fazenda' => asset('images/category-fazenda.png'),
        'ativo' => asset('images/category-ativo.png'),
        'servico' => asset('images/category-servico.png'),
    ];
@endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.'.$category))

@section('content')
    <div x-data="categorySearch(@js(route('public.'.$category, $l)), @js($filters))">
        <section class="relative overflow-hidden bg-onyx-950">
            <img src="{{ $categoryImages[$category] }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-55">
            <div class="absolute inset-0 bg-gradient-to-r from-onyx-950 via-onyx-950/70 to-onyx-950/10"></div>
            <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200">@lang('site.listing.category')</span>
                <h1 class="mt-3 max-w-3xl font-display text-4xl text-white md:text-6xl">
                    {{ $page->getTranslation('title', $l, false) ?: __('site.nav.'.$category) }}
                </h1>
                @if ($body = $page->getTranslation('body', $l, false))
                    <div class="prose prose-invert mt-5 max-w-2xl text-linen-100">{!! $body !!}</div>
                @else
                    <p class="mt-5 max-w-2xl text-lg text-linen-100">{{ __('site.categories.'.$category) }}</p>
                @endif
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <form method="GET" @submit.prevent="search()"
                  class="-mt-16 grid gap-3 rounded-3xl border border-linen-200 bg-white p-4 shadow-xl md:grid-cols-[1fr_260px_auto]">
                <input type="text" name="q" x-model="q" @input.debounce.400ms="search()"
                       placeholder="@lang('site.search.placeholder')"
                       class="min-h-12 rounded-xl border border-linen-200 bg-linen-50 px-4 text-onyx-900 placeholder:text-onyx-500 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">

                <select name="region" x-model="region" @change="search()"
                        class="min-h-12 rounded-xl border border-linen-200 bg-linen-50 px-4 text-onyx-900 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                    <option value="">@lang('site.search.region_all')</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region }}">{{ $region }}</option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-xl bg-onyx-950 px-6 py-3 font-semibold text-white transition hover:bg-ouro-700">
                    @lang('site.search.button')
                </button>
            </form>

            <div id="category-results" :class="loading ? 'opacity-50 transition-opacity' : ''">
                @include('public.partials.category-results')
            </div>
        </section>
    </div>
@endsection
