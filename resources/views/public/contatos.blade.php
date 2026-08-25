@extends('layouts.public')

@php
    $l = app()->getLocale();

    $contact = config('pib.contact');
    $whatsapp = preg_replace('/\D/', '', (string) ($contact['whatsapp'] ?? ''));
@endphp

@section('title', $page->getTranslation('title', $l, false) ?: __('site.nav.contatos'))
@section('description', __('site.contact.fallback'))

@section('content')
    <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:gap-16 lg:px-8 lg:py-24">
        <div data-reveal>
            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.nav.contatos')</span>
            <h1 class="mt-3 font-display text-4xl font-medium leading-[1.08] tracking-tight text-onyx-950 md:text-6xl">
                {{ $page->getTranslation('title', $l, false) ?: __('site.contact.title') }}
            </h1>
            @if ($body = $page->getTranslation('body', $l, false))
                <div class="prose prose-pib mt-6 max-w-none">{!! $body !!}</div>
            @else
                <p class="mt-6 max-w-2xl text-lg leading-relaxed text-onyx-600">@lang('site.contact.fallback')</p>
            @endif

            {{-- Canais diretos: só aparecem os que estiverem configurados. --}}
            <ul class="mt-10 divide-y divide-linen-200 border-y border-linen-200">
                @if ($contact['email'] ?? null)
                    <li>
                        <a href="mailto:{{ $contact['email'] }}" class="group flex items-center gap-4 py-5 transition-colors duration-200 ease-pib hover:text-ouro-700">
                            <svg class="h-5 w-5 shrink-0 text-ouro-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                            <span class="min-w-0 flex-1">
                                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-onyx-500">@lang('site.cta.email')</span>
                                <span class="mt-0.5 block truncate font-medium text-onyx-900 group-hover:text-ouro-700">{{ $contact['email'] }}</span>
                            </span>
                            <span aria-hidden="true" class="shrink-0 text-onyx-400 transition-transform duration-300 ease-pib group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </li>
                @endif

                @if ($whatsapp)
                    <li>
                        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" class="group flex items-center gap-4 py-5 transition-colors duration-200 ease-pib hover:text-ouro-700">
                            <svg class="h-5 w-5 shrink-0 text-sage-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            <span class="min-w-0 flex-1">
                                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-onyx-500">@lang('site.cta.whatsapp')</span>
                                <span class="mt-0.5 block font-medium text-onyx-900 tabular group-hover:text-ouro-700">{{ $contact['whatsapp'] }}</span>
                            </span>
                            <span aria-hidden="true" class="shrink-0 text-onyx-400 transition-transform duration-300 ease-pib group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </li>
                @endif

                @if ($contact['phone'] ?? null)
                    <li>
                        <a href="tel:{{ preg_replace('/[^\d+]/', '', $contact['phone']) }}" class="group flex items-center gap-4 py-5 transition-colors duration-200 ease-pib hover:text-ouro-700">
                            <svg class="h-5 w-5 shrink-0 text-ouro-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                            <span class="min-w-0 flex-1">
                                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-onyx-500">@lang('site.contact.card_eyebrow')</span>
                                <span class="mt-0.5 block font-medium text-onyx-900 tabular group-hover:text-ouro-700">{{ $contact['phone'] }}</span>
                            </span>
                            <span aria-hidden="true" class="shrink-0 text-onyx-400 transition-transform duration-300 ease-pib group-hover:translate-x-1">&rarr;</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="lg:sticky lg:top-28 lg:self-start" data-reveal style="--reveal-delay: 120ms">
            <div class="rounded-panel bg-onyx-950 p-8 text-white shadow-panel md:p-10">
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-400">@lang('site.contact.card_eyebrow')</span>
                <h2 class="mt-3 font-display text-2xl font-medium leading-snug md:text-3xl">@lang('site.contact.card_title')</h2>
                <p class="mt-4 leading-relaxed text-linen-200">@lang('site.contact.card_text')</p>

                <div class="mt-9 grid gap-3">
                    @if ($contact['email'] ?? null)
                        <a href="mailto:{{ $contact['email'] }}"
                           class="rounded-pill bg-white px-6 py-3 text-center font-semibold text-onyx-950 transition duration-300 ease-pib hover:bg-ouro-200 active:scale-[0.98]">
                            @lang('site.cta.email')
                        </a>
                    @endif
                    <a href="{{ route('public.fazenda', $l) }}"
                       class="rounded-pill border border-white/25 px-6 py-3 text-center font-semibold text-white transition duration-300 ease-pib hover:border-ouro-200 hover:text-ouro-200 active:scale-[0.98]">
                        @lang('site.cta.view_portfolio')
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
