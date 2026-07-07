@extends('layouts.public')

@php $l = app()->getLocale(); @endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.informacoes'))

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
        <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.nav.informacoes')</span>
        <h1 class="mt-3 font-display text-4xl text-onyx-950 md:text-5xl">
            {{ $page->getTranslation('title', $l, false) ?: __('site.nav.informacoes') }}
        </h1>
        <div class="mt-8 rounded-3xl border border-linen-200 bg-white p-8 shadow-sm">
            @if ($body = $page->getTranslation('body', $l, false))
                <div class="prose prose-pib max-w-none">{!! $body !!}</div>
            @else
                <p class="text-lg leading-relaxed text-onyx-600">@lang('site.info.fallback')</p>
            @endif
        </div>
    </section>

    @include('public.partials.events', ['events' => $events])
@endsection
