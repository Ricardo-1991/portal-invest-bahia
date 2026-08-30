@php
    use Illuminate\Support\Facades\Route;

    $locales = config('pib.locales');
    $routeName = Route::currentRouteName() ?: 'public.home';
    $routeParams = request()->route()?->parameters() ?? [];
    // URLs equivalentes da página atual em cada idioma (para seletor e hreflang).
    $localeUrls = collect($locales)->mapWithKeys(fn ($label, $code) => [
        $code => route($routeName, array_merge($routeParams, ['locale' => $code])),
    ]);

    $metaTitle = trim($__env->yieldContent('title', config('app.name')));
    $metaDescription = trim($__env->yieldContent('description', __('site.meta.description')));
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf8f1">
    <title>{{ $metaTitle }} - {{ config('app.name') }}</title>

    <meta name="description" content="{{ $metaDescription }}">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/hero-rural-bahia.png') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ asset('images/hero-rural-bahia.png') }}">

    <link rel="icon" href="{{ asset('images/logo.jpeg') }}" type="image/jpeg">
    <link rel="canonical" href="{{ url()->current() }}">

    @foreach ($localeUrls as $code => $url)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $localeUrls['pt'] }}">

    {{-- Precisa vir antes do primeiro paint, senão dropdowns e modal piscam visíveis. --}}
    <style>[x-cloak]{display:none!important}</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col text-onyx-900 antialiased">
    <div class="grain" aria-hidden="true"></div>

    <a href="#conteudo" class="skip-link">@lang('site.a11y.skip_to_content')</a>

    @include('public.partials.header', ['localeUrls' => $localeUrls])

    <main id="conteudo" class="relative z-10 flex-1">
        @yield('content')
    </main>

    @include('public.partials.footer')
</body>
</html>
