<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Corretores publicam apenas no próprio idioma; o site exibe o
        // conteúdo no idioma escolhido pelo visitante quando existir, cai
        // para o português quando não, e para qualquer idioma preenchido
        // quando nem o português existir (ex.: corretor que só publica em
        // italiano).
        app(Translatable::class)->fallback(
            fallbackLocale: config('app.fallback_locale'),
            fallbackAny: true,
        );
    }
}
