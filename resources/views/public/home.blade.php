@extends('layouts.public')

@php $l = app()->getLocale(); @endphp

@section('title', $page->getTranslation('title', $l, false) ?: config('app.name'))

@section('content')
    {{-- Hero: split assimétrico, mensagem à esquerda, marca à direita --}}
    <section class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-12 px-4 pt-16 pb-16 sm:px-6 md:pt-20 md:pb-24 lg:grid-cols-2 lg:px-8">
        <div data-reveal>
            <h1 class="max-w-xl font-display text-4xl leading-tight text-onyx-50 md:text-5xl lg:text-6xl">
                {{ $page->getTranslation('title', $l, false) ?: __('site.hero.headline') }}
            </h1>
            <p class="mt-5 max-w-md text-base leading-relaxed text-onyx-300">@lang('site.hero.tagline')</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('public.fazenda', $l) }}"
                   class="rounded-lg bg-ouro-400 px-6 py-3 text-sm font-semibold text-onyx-950 transition hover:bg-ouro-200">
                    @lang('site.nav.fazenda')
                </a>
                <a href="{{ route('public.ativo', $l) }}"
                   class="rounded-lg border border-onyx-600 px-6 py-3 text-sm font-semibold text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">
                    @lang('site.nav.ativo')
                </a>
            </div>
        </div>

        <div data-reveal style="transition-delay: 120ms" class="relative flex items-center justify-center">
            <div class="absolute h-72 w-72 rounded-full bg-ouro-500/20 blur-3xl"></div>
            <img src="{{ asset('images/logo.jpeg') }}" alt="Portal Invest Bahia"
                 class="relative h-64 w-64 rounded-2xl object-cover shadow-2xl sm:h-80 sm:w-80">
        </div>
    </section>

    {{-- Conteúdo editável da página inicial --}}
    @if ($body = $page->getTranslation('body', $l, false))
        <section class="mx-auto max-w-4xl px-4 pb-16 sm:px-6 lg:px-8">
            <div class="prose prose-invert prose-pib max-w-none">{!! $body !!}</div>
        </section>
    @endif

    {{-- Navegação por categoria (bento assimétrico com fotografia) --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8" data-reveal>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <a href="{{ route('public.fazenda', $l) }}"
               class="group relative row-span-2 overflow-hidden rounded-2xl border border-onyx-700">
                <img src="https://picsum.photos/seed/pib-fazenda-bahia/900/1100" alt="" loading="lazy"
                     class="h-full min-h-[22rem] w-full object-cover transition duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-onyx-950 via-onyx-950/20 to-transparent"></div>
                <span class="absolute bottom-6 left-6 font-display text-2xl text-onyx-50">@lang('site.nav.fazenda')</span>
            </a>

            <a href="{{ route('public.ativo', $l) }}"
               class="group relative overflow-hidden rounded-2xl border border-onyx-700">
                <img src="https://picsum.photos/seed/pib-ativos-bahia/900/500" alt="" loading="lazy"
                     class="h-full min-h-[10rem] w-full object-cover transition duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-onyx-950 via-onyx-950/20 to-transparent"></div>
                <span class="absolute bottom-5 left-6 font-display text-xl text-onyx-50">@lang('site.nav.ativo')</span>
            </a>

            <a href="{{ route('public.servico', $l) }}"
               class="group relative overflow-hidden rounded-2xl border border-onyx-700">
                <img src="https://picsum.photos/seed/pib-servicos-bahia/900/500" alt="" loading="lazy"
                     class="h-full min-h-[10rem] w-full object-cover transition duration-500 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-onyx-950 via-onyx-950/20 to-transparent"></div>
                <span class="absolute bottom-5 left-6 font-display text-xl text-onyx-50">@lang('site.nav.servico')</span>
            </a>
        </div>
    </section>

    {{-- Destaques --}}
    @if ($featured->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
            <h2 class="mb-6 font-display text-2xl text-onyx-50" data-reveal>@lang('site.listing.featured')</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($featured as $listing)
                    @include('public.partials.listing-card', ['listing' => $listing])
                @endforeach
            </div>
        </section>
    @endif

    @include('public.partials.events', ['events' => $events])
@endsection
