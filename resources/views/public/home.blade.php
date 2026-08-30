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
    {{-- Hero limpo: o vídeo vem do Filament; sem upload, permanece o poster atual. --}}
    <x-media-hero poster="images/hero-rural-bahia.png"
                  :video-url="$page->heroVideoUrl()"
                  video-fit="contain"
                  eager
                  class="home-video-hero border-b border-linen-200 bg-onyx-950">
        <x-slot:overlay>
            <div class="absolute inset-0 bg-onyx-950/10"></div>
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-onyx-950/25 to-transparent"></div>
        </x-slot:overlay>
    </x-media-hero>

    <section class="relative z-10 mx-auto -mt-10 max-w-7xl px-4 sm:-mt-12 sm:px-6 lg:-mt-14 lg:px-8" data-reveal>
        <div x-data="listingFilter(@js($regionsByCategory), @js($filters))">
            @include('public.partials.listing-filters', ['filterMode' => 'home'])
        </div>
    </section>

    {{-- Inventário em primeiro lugar: destaques logo após o hero --}}
    <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-20 lg:pt-16">
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
