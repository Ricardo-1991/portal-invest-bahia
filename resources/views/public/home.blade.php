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
    {{-- Hero com vídeo de fundo. Sem o MP4 em public/videos/, o poster assume. --}}
    <x-media-hero poster="images/hero-rural-bahia.png"
                  video="videos/hero.mp4"
                  eager
                  class="flex hero-tall items-center">
        <x-slot:overlay>
            <div class="absolute inset-0 bg-gradient-to-r from-onyx-950/85 via-onyx-950/50 to-onyx-950/15"></div>
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-linen-50 to-transparent"></div>
        </x-slot:overlay>

        <div class="mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 md:py-28 lg:px-8">
            <div class="max-w-3xl">
                <span data-reveal
                      class="inline-flex rounded-pill border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200 backdrop-blur">
                    @lang('site.hero.eyebrow')
                </span>

                <h1 data-reveal style="--reveal-delay: 90ms"
                    class="mt-6 max-w-2xl font-display text-4xl font-medium leading-[1.08] tracking-tight text-white md:text-6xl">
                    @lang('site.hero.headline')
                </h1>

                <p data-reveal style="--reveal-delay: 180ms"
                   class="mt-5 max-w-xl text-base leading-relaxed text-linen-200 md:text-lg">
                    @lang('site.hero.tagline')
                </p>

                <form method="GET" action="{{ route('public.fazenda', $l) }}"
                      data-reveal style="--reveal-delay: 270ms"
                      class="mt-9 grid gap-3 rounded-panel border border-white/20 bg-white p-3 shadow-panel sm:grid-cols-[1fr_auto]">
                    <label for="hero-q" class="sr-only">@lang('site.search.home_placeholder')</label>
                    <input id="hero-q" type="text" name="q" placeholder="@lang('site.search.home_placeholder')"
                           class="min-h-12 rounded-card border border-linen-200 bg-linen-50 px-4 text-onyx-900 transition duration-200 ease-pib placeholder:text-onyx-500 focus:border-ouro-500 focus:outline-none focus:ring-2 focus:ring-ouro-200">
                    <button type="submit"
                            class="rounded-card bg-ouro-500 px-7 py-3 font-semibold text-onyx-950 transition duration-300 ease-pib hover:bg-ouro-400 active:scale-[0.98]">
                        @lang('site.search.button')
                    </button>
                </form>

                <div data-reveal style="--reveal-delay: 360ms" class="mt-5 flex flex-wrap gap-3 text-sm">
                    @foreach ($categoryRoutes as $key => $url)
                        <a href="{{ $url }}"
                           class="rounded-pill border border-white/25 bg-white/10 px-4 py-2 font-semibold text-white backdrop-blur transition duration-300 ease-pib hover:border-ouro-300 hover:bg-white/20 hover:text-ouro-200 active:scale-95">
                            {{ __('site.nav.'.$key) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </x-media-hero>

    {{-- Inventário em primeiro lugar: destaques logo após o hero --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
        <div class="mb-10 flex flex-col justify-between gap-4 md:flex-row md:items-end" data-reveal>
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.listing.featured_eyebrow')</span>
                <h2 class="mt-3 max-w-xl font-display text-3xl font-medium tracking-tight text-onyx-950 md:text-4xl">@lang('site.listing.featured')</h2>
            </div>
            <a href="{{ route('public.fazenda', $l) }}"
               class="group inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-onyx-950 transition-colors duration-200 ease-pib hover:text-ouro-700">
                @lang('site.cta.view_portfolio')
                <span class="transition-transform duration-300 ease-pib group-hover:translate-x-1">&rarr;</span>
            </a>
        </div>

        @if ($featured->isNotEmpty())
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $listing)
                    <div data-reveal style="--reveal-delay: {{ min($loop->index, 5) * 80 }}ms" class="flex">
                        @include('public.partials.listing-card', ['listing' => $listing])
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid gap-6 rounded-panel border border-linen-200 bg-white p-8 shadow-card md:grid-cols-[1.2fr_0.8fr] md:items-center" data-reveal>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.empty.eyebrow')</span>
                    <h3 class="mt-3 font-display text-3xl font-medium text-onyx-950">@lang('site.empty.title')</h3>
                    <p class="mt-4 max-w-2xl text-onyx-600">@lang('site.empty.text')</p>
                </div>
                <div class="flex flex-wrap gap-3 md:justify-end">
                    <a href="{{ route('public.contatos', $l) }}" class="rounded-pill bg-onyx-950 px-5 py-3 text-sm font-semibold text-white transition duration-200 ease-pib hover:bg-ouro-700 active:scale-95">@lang('site.cta.contact_broker')</a>
                    <a href="{{ route('public.fazenda', $l) }}" class="rounded-pill border border-linen-300 bg-white px-5 py-3 text-sm font-semibold text-onyx-950 transition duration-200 ease-pib hover:border-ouro-500 active:scale-95">@lang('site.cta.view_portfolio')</a>
                </div>
            </div>
        @endif
    </section>

    {{--
        Escolha de categoria. Layout assimétrico de propósito (texto à esquerda,
        lista empilhada à direita) em vez das três colunas iguais de sempre.
    --}}
    <section class="border-y border-linen-200 bg-linen-50/60">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:gap-16 lg:px-8 lg:py-20">
            <div data-reveal>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.sections.portfolio_eyebrow')</span>
                <h2 class="mt-3 font-display text-3xl font-medium tracking-tight text-onyx-950 md:text-4xl">@lang('site.sections.portfolio_title')</h2>
                <p class="mt-5 max-w-md leading-relaxed text-onyx-600">@lang('site.sections.portfolio_text')</p>
            </div>

            <ul class="divide-y divide-linen-200 border-t border-linen-200 lg:border-t-0">
                @foreach ($categoryRoutes as $key => $url)
                    <li data-reveal style="--reveal-delay: {{ $loop->index * 100 }}ms">
                        <a href="{{ $url }}" class="group flex items-center gap-6 py-6 transition-colors duration-300 ease-pib hover:bg-white/70 lg:px-4">
                            <span class="font-display text-sm text-ouro-600 tabular">0{{ $loop->iteration }}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block font-display text-xl text-onyx-950 transition-colors duration-200 ease-pib group-hover:text-ouro-700 md:text-2xl">
                                    {{ __('site.nav.'.$key) }}
                                </span>
                                <span class="mt-1 block text-sm leading-relaxed text-onyx-600">
                                    {{ __('site.categories.'.$key) }}
                                </span>
                            </span>
                            <span aria-hidden="true"
                                  class="shrink-0 text-xl text-onyx-400 transition-all duration-300 ease-pib group-hover:translate-x-1 group-hover:text-ouro-600">&rarr;</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Conteúdo editável da página (admin) --}}
    @if ($body = $page->getTranslation('body', $l, false))
        <section class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="prose prose-pib max-w-none rounded-panel border border-linen-200 bg-white/80 p-8 shadow-card" data-reveal>{!! $body !!}</div>
        </section>
    @endif

    {{-- Faixa de confiança --}}
    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 rounded-panel bg-onyx-950 px-8 py-10 text-white shadow-panel md:grid-cols-[1.4fr_0.6fr] md:items-center md:gap-10" data-reveal>
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-400">@lang('site.trust.eyebrow')</span>
                <strong class="mt-3 block max-w-lg font-display text-2xl font-medium leading-snug md:text-3xl">@lang('site.trust.title')</strong>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-linen-200">@lang('site.trust.text')</p>
            </div>
            <div class="md:justify-self-end">
                <a href="{{ route('public.contatos', $l) }}"
                   class="group inline-flex items-center gap-2 rounded-pill border border-ouro-400/40 bg-ouro-400/10 px-6 py-3 text-sm font-semibold text-ouro-200 transition duration-300 ease-pib hover:border-ouro-400 hover:bg-ouro-400 hover:text-onyx-950 active:scale-95">
                    @lang('site.cta.contact_broker')
                    <span class="transition-transform duration-300 ease-pib group-hover:translate-x-1">&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    {{-- Eventos: carrossel coverflow --}}
    @include('public.partials.events', ['events' => $events])
@endsection
