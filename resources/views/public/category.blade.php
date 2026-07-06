@extends('layouts.public')

@php $l = app()->getLocale(); @endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.'.$category))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8"
         x-data="categorySearch(@js(route('public.'.$category, $l)), @js($filters))">
        <h1 class="font-display text-3xl text-onyx-50 md:text-4xl">
            {{ $page->getTranslation('title', $l, false) ?: __('site.nav.'.$category) }}
        </h1>

        @if ($body = $page->getTranslation('body', $l, false))
            <div class="prose prose-invert prose-pib mt-4 max-w-none">{!! $body !!}</div>
        @endif

        {{-- Busca e filtros: ao vivo via Alpine/fetch; GET normal como fallback sem JS --}}
        <form method="GET" @submit.prevent="search()"
              class="mt-8 flex flex-col gap-3 rounded-2xl border border-onyx-700 bg-onyx-900 p-4 sm:flex-row">
            <input type="text" name="q" x-model="q" @input.debounce.400ms="search()"
                   placeholder="@lang('site.search.placeholder')"
                   class="flex-1 rounded-lg border border-onyx-700 bg-onyx-950 px-4 py-2 text-onyx-100 placeholder:text-onyx-500 focus:border-ouro-500 focus:outline-none focus:ring-1 focus:ring-ouro-500">

            <select name="region" x-model="region" @change="search()"
                    class="rounded-lg border border-onyx-700 bg-onyx-950 px-4 py-2 text-onyx-100 focus:border-ouro-500 focus:outline-none focus:ring-1 focus:ring-ouro-500">
                <option value="">@lang('site.search.region_all')</option>
                @foreach ($regions as $region)
                    <option value="{{ $region }}">{{ $region }}</option>
                @endforeach
            </select>

            <button type="submit" class="rounded-lg bg-ouro-400 px-6 py-2 font-semibold text-onyx-950 transition hover:bg-ouro-200">
                @lang('site.search.button')
            </button>
        </form>

        {{-- Resultados: substituídos ao vivo pela busca; nova busca sempre volta à página 1 --}}
        <div id="category-results" :class="loading ? 'opacity-50 transition-opacity' : ''">
            @include('public.partials.category-results')
        </div>
    </div>
@endsection
