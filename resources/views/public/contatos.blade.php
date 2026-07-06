@extends('layouts.public')

@php $l = app()->getLocale(); @endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.contatos'))

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="font-display text-3xl text-onyx-50 md:text-4xl">
            {{ $page->getTranslation('title', $l, false) ?: __('site.nav.contatos') }}
        </h1>
        @if ($body = $page->getTranslation('body', $l, false))
            <div class="prose prose-invert prose-pib mt-6 max-w-none">{!! $body !!}</div>
        @endif
    </div>
@endsection
