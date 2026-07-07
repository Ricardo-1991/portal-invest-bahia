@extends('layouts.public')

@php
    $l = app()->getLocale();
    $categoryRoutes = [
        'fazenda' => route('public.fazenda', $l),
        'ativo' => route('public.ativo', $l),
        'servico' => route('public.servico', $l),
    ];
@endphp

@section('title', $page->getTranslation('title', $l, false) ?: config('app.name'))

@section('content')
    {{-- Hero compacto: busca em evidência, inventário logo abaixo da dobra --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/hero-rural-bahia.png') }}" alt="" class="h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-onyx-950/82 via-onyx-950/45 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-linen-50 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 md:py-20 lg:px-8">
            <div class="max-w-3xl" data-reveal>
                <span class="inline-flex rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200 backdrop-blur">
                    @lang('site.hero.eyebrow')
                </span>
                <h1 class="mt-5 max-w-2xl font-display text-4xl leading-tight text-white md:text-5xl">
                    @lang('site.hero.headline')
                </h1>

                <form method="GET" action="{{ route('public.fazenda', $l) }}"
                      class="mt-7 grid gap-3 rounded-2xl border border-white/20 bg-white p-3 shadow-2xl sm:grid-cols-[1fr_auto]">
                    <input type="text" name="q" placeholder="@lang('site.search.home_placeholder')"
                           class="min-h-12 rounded-xl border border-linen-200 bg-linen-50 px-4 text-onyx-900 placeholder:text-onyx-500 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                    <button type="submit" class="rounded-xl bg-ouro-500 px-6 py-3 font-semibold text-onyx-950 transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] hover:bg-ouro-400 active:scale-[0.98]">
                        @lang('site.search.button')
                    </button>
                </form>

                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                    @foreach ($categoryRoutes as $key => $url)
                        <a href="{{ $url }}" class="rounded-full border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] hover:border-ouro-300 hover:text-ouro-200">
                            {{ __('site.nav.'.$key) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Inventário em primeiro lugar: destaques logo após o hero --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8" data-reveal>
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
            <div class="grid gap-6 rounded-3xl border border-linen-200 bg-white p-8 shadow-sm md:grid-cols-[1.2fr_0.8fr] md:items-center">
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
    </section>

    {{-- Conteúdo editável da página (admin) --}}
    @if ($body = $page->getTranslation('body', $l, false))
        <section class="mx-auto max-w-4xl px-4 pb-14 sm:px-6 lg:px-8">
            <div class="prose prose-pib max-w-none rounded-3xl border border-linen-200 bg-white/80 p-8 shadow-sm">{!! $body !!}</div>
        </section>
    @endif

    {{-- Faixa discreta de confiança (uma linha, sem virar seção de marketing) --}}
    <section class="mx-auto max-w-7xl px-4 pb-6 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 rounded-2xl bg-onyx-950 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
            <strong class="font-display text-lg font-medium">@lang('site.trust.title')</strong>
            <a href="{{ route('public.contatos', $l) }}" class="text-sm font-semibold text-ouro-200 transition hover:text-white">
                @lang('site.cta.contact_broker') &rarr;
            </a>
        </div>
    </section>

    {{-- Eventos: carrossel coverflow --}}
    @include('public.partials.events', ['events' => $events])
@endsection
