<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Define o locale da aplicação a partir do segmento {locale} da URL.
 * Rotas públicas vivem sob /pt, /en, /es, /it.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (is_string($locale) && array_key_exists($locale, config('pib.locales'))) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
