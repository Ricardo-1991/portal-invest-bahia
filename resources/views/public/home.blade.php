@extends('layouts.public')

@php
    $l = app()->getLocale();
    $categories = [
        'fazenda' => [
            'route' => route('public.fazenda', $l),
            'image' => asset('images/category-fazenda.png'),
            'description' => __('site.categories.fazenda'),
        ],
        'ativo' => [
            'route' => route('public.ativo', $l),
            'image' => asset('images/category-ativo.png'),
            'description' => __('site.categories.ativo'),
        ],
        'servico' => [
            'route' => route('public.servico', $l),
            'image' => asset('images/category-servico.png'),
            'description' => __('site.categories.servico'),
        ],
    ];
@endphp

@section('title', $page->getTranslation('title', $l, false) ?: config('app.name'))

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-rural-bahia.png') }}" alt="" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-onyx-950/82 via-onyx-950/45 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-linen-50 to-transparent"></div>
        </div>

        <div class="relative mx-auto grid min-h-[680px] max-w-7xl items-center px-4 py-16 sm:px-6 lg:px-8">
            <div class="max-w-3xl" data-reveal>
                <span class="inline-flex rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200 backdrop-blur">
                    @lang('site.hero.eyebrow')
                </span>
                <h1 class="mt-6 max-w-3xl font-display text-5xl leading-[0.98] text-white md:text-7xl">
                    @lang('site.hero.headline')
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-linen-100 md:text-xl">@lang('site.hero.tagline')</p>

                <form method="GET" action="{{ route('public.fazenda', $l) }}"
                      class="mt-8 grid gap-3 rounded-2xl border border-white/20 bg-white p-3 shadow-2xl sm:grid-cols-[1fr_auto]">
                    <input type="text" name="q" placeholder="@lang('site.search.home_placeholder')"
                           class="min-h-12 rounded-xl border border-linen-200 bg-linen-50 px-4 text-onyx-900 placeholder:text-onyx-500 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                    <button type="submit" class="rounded-xl bg-ouro-500 px-6 py-3 font-semibold text-onyx-950 transition hover:bg-ouro-400">
                        @lang('site.search.button')
                    </button>
                </form>

                <div class="mt-5 flex flex-wrap gap-3 text-sm">
                    @foreach ($categories as $key => $category)
                        <a href="{{ $category['route'] }}" class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition hover:border-ouro-300 hover:text-ouro-200">
                            {{ __('site.nav.'.$key) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if ($body = $page->getTranslation('body', $l, false))
        <section class="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="prose prose-pib max-w-none rounded-3xl border border-linen-200 bg-white/80 p-8 shadow-sm">{!! $body !!}</div>
        </section>
    @endif

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8" data-reveal>
        <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.sections.portfolio_eyebrow')</span>
                <h2 class="mt-3 font-display text-3xl text-onyx-950 md:text-4xl">@lang('site.sections.portfolio_title')</h2>
            </div>
            <p class="max-w-xl text-onyx-600">@lang('site.sections.portfolio_text')</p>
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            @foreach ($categories as $key => $category)
                <a href="{{ $category['route'] }}"
                   class="group overflow-hidden rounded-3xl border border-linen-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ $category['image'] }}" alt="" loading="lazy"
                             class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-ouro-700">{{ __('site.nav.'.$key) }}</span>
                        <h3 class="mt-2 font-display text-2xl text-onyx-950">{{ __('site.nav.'.$key) }}</h3>
                        <p class="mt-3 text-sm leading-relaxed text-onyx-600">{{ $category['description'] }}</p>
                        <span class="mt-5 inline-flex text-sm font-semibold text-onyx-950 transition group-hover:text-ouro-700">
                            @lang('site.cta.details') &rarr;
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-white/65 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.listing.featured_eyebrow')</span>
                    <h2 class="mt-3 font-display text-3xl text-onyx-950 md:text-4xl">@lang('site.listing.featured')</h2>
                </div>
                <a href="{{ route('public.fazenda', $l) }}" class="text-sm font-semibold text-onyx-950 transition hover:text-ouro-700">
                    @lang('site.cta.view_portfolio') &rarr;
                </a>
            </div>

            @if ($featured->isNotEmpty())
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $listing)
                        @include('public.partials.listing-card', ['listing' => $listing])
                    @endforeach
                </div>
            @else
                <div class="grid gap-6 rounded-3xl border border-linen-200 bg-linen-50 p-8 md:grid-cols-[1.2fr_0.8fr] md:items-center">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.empty.eyebrow')</span>
                        <h3 class="mt-3 font-display text-3xl text-onyx-950">@lang('site.empty.title')</h3>
                        <p class="mt-4 max-w-2xl text-onyx-600">@lang('site.empty.text')</p>
                    </div>
                    <div class="flex flex-wrap gap-3 md:justify-end">
                        <a href="{{ route('public.contatos', $l) }}" class="rounded-full bg-onyx-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-ouro-700">@lang('site.cta.contact_broker')</a>
                        <a href="{{ route('public.fazenda', $l) }}" class="rounded-full border border-linen-300 bg-white px-5 py-3 text-sm font-semibold text-onyx-950 transition hover:border-ouro-500">@lang('site.cta.view_portfolio')</a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-8 rounded-3xl bg-onyx-950 p-8 text-white md:grid-cols-3 md:p-10">
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200">@lang('site.trust.eyebrow')</span>
                <h2 class="mt-3 font-display text-3xl">@lang('site.trust.title')</h2>
            </div>
            <p class="text-linen-200 md:col-span-2">@lang('site.trust.text')</p>
        </div>
    </section>

    @include('public.partials.events', ['events' => $events])
@endsection
