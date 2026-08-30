<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// Raiz redireciona para o idioma preferido do navegador (ou português).
Route::get('/', function () {
    $preferred = request()->getPreferredLanguage(array_keys(config('pib.locales'))) ?? 'pt';

    return redirect("/{$preferred}");
});

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('pib.locales')))])
    ->middleware('setlocale')
    ->group(function () {
        Route::get('/', [PublicController::class, 'home'])->name('public.home');
        Route::get('/buscar', [PublicController::class, 'search'])->name('public.search');

        Route::get('/oportunidades', [PublicController::class, 'category'])
            ->defaults('category', 'all')->name('public.all');
        Route::get('/fazendas', [PublicController::class, 'category'])
            ->defaults('category', 'fazenda')->name('public.fazenda');
        Route::get('/ativos', [PublicController::class, 'category'])
            ->defaults('category', 'ativo')->name('public.ativo');
        Route::get('/servicos', [PublicController::class, 'category'])
            ->defaults('category', 'servico')->name('public.servico');

        Route::get('/informacoes', [PublicController::class, 'informacoes'])->name('public.informacoes');
        Route::get('/contatos', [PublicController::class, 'contatos'])->name('public.contatos');

        Route::get('/anuncio/{slug}', [PublicController::class, 'show'])->name('public.listing');
    });
