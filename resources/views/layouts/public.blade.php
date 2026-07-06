@php
    use Illuminate\Support\Facades\Route;

    $locales = config('pib.locales');
    $routeName = Route::currentRouteName() ?: 'public.home';
    $routeParams = request()->route()?->parameters() ?? [];
    // URLs equivalentes da página atual em cada idioma (para seletor e hreflang).
    $localeUrls = collect($locales)->mapWithKeys(fn ($label, $code) => [
        $code => route($routeName, array_merge($routeParams, ['locale' => $code])),
    ]);
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0a08">
    <title>@yield('title', config('app.name')) - {{ config('app.name') }}</title>

    <link rel="icon" href="{{ asset('images/logo.jpeg') }}" type="image/jpeg">

    @foreach ($localeUrls as $code => $url)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $localeUrls['pt'] }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-onyx-950 text-onyx-100 antialiased flex flex-col">
    @include('public.partials.header', ['localeUrls' => $localeUrls])

    <main class="flex-1">
        @yield('content')
    </main>

    @include('public.partials.footer')
</body>
</html>
