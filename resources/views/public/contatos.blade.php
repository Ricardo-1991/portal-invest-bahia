@extends('layouts.public')

@php $l = app()->getLocale(); @endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.contatos'))

@section('content')
    <section class="mx-auto grid max-w-7xl gap-8 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
        <div>
            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.nav.contatos')</span>
            <h1 class="mt-3 font-display text-4xl text-onyx-950 md:text-6xl">
                {{ $page->getTranslation('title', $l, false) ?: __('site.contact.title') }}
            </h1>
            @if ($body = $page->getTranslation('body', $l, false))
                <div class="prose prose-pib mt-6 max-w-none">{!! $body !!}</div>
            @else
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-onyx-600">@lang('site.contact.fallback')</p>
            @endif
        </div>

        <div class="rounded-3xl bg-onyx-950 p-8 text-white shadow-xl">
            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-200">@lang('site.contact.card_eyebrow')</span>
            <h2 class="mt-3 font-display text-3xl">@lang('site.contact.card_title')</h2>
            <p class="mt-4 text-linen-200">@lang('site.contact.card_text')</p>
            <div class="mt-8 grid gap-3">
                <a href="mailto:admin@pib.com.br" class="rounded-full bg-white px-5 py-3 text-center font-semibold text-onyx-950 transition hover:bg-ouro-200">
                    @lang('site.cta.email')
                </a>
                <a href="{{ route('public.fazenda', $l) }}" class="rounded-full border border-white/25 px-5 py-3 text-center font-semibold text-white transition hover:border-ouro-200 hover:text-ouro-200">
                    @lang('site.cta.view_portfolio')
                </a>
            </div>
        </div>
    </section>
@endsection
