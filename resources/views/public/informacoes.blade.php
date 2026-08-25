@extends('layouts.public')

@php $l = app()->getLocale(); @endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.informacoes'))
@section('description', __('site.info.fallback'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div data-reveal>
            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.nav.informacoes')</span>
            <h1 class="mt-3 max-w-3xl font-display text-4xl font-medium leading-[1.08] tracking-tight text-onyx-950 md:text-5xl">
                {{ $page->getTranslation('title', $l, false) ?: __('site.nav.informacoes') }}
            </h1>
        </div>

        <div class="mt-10 rounded-panel border border-linen-200 bg-white p-8 shadow-card md:p-12" data-reveal style="--reveal-delay: 120ms">
            @if ($body = $page->getTranslation('body', $l, false))
                <div class="prose prose-pib max-w-none">{!! $body !!}</div>
            @else
                <p class="text-lg leading-relaxed text-onyx-600">@lang('site.info.fallback')</p>
            @endif
        </div>

        <div class="mt-8 flex flex-col gap-4 rounded-panel bg-onyx-950 px-8 py-7 text-white sm:flex-row sm:items-center sm:justify-between"
             data-reveal style="--reveal-delay: 200ms">
            <strong class="font-display text-lg font-medium leading-snug">@lang('site.trust.title')</strong>
            <a href="{{ route('public.contatos', $l) }}"
               class="group inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-ouro-200 transition-colors duration-200 ease-pib hover:text-white">
                @lang('site.cta.contact_broker')
                <span class="transition-transform duration-300 ease-pib group-hover:translate-x-1">&rarr;</span>
            </a>
        </div>
    </section>

    @include('public.partials.events', ['events' => $events])
@endsection
