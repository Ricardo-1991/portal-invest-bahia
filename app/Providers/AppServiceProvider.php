<?php

namespace App\Providers;

use App\Notifications\ResetPassword as PortalResetPassword;
use Filament\Auth\Notifications\ResetPassword as FilamentResetPassword;
use Filament\Notifications\Notification;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FilamentResetPassword::class, PortalResetPassword::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Permite restaurar, após o redirecionamento, somente a view conhecida
        // usada pelo retorno centralizado de criação de registros.
        Notification::configureUsing(
            fn (Notification $notification) => $notification
                ->safeViews('filament.notifications.creation-success'),
        );

        $heroVideoMaxKilobytes = config('pib.uploads.hero_video_max_kb');

        // Media Library usa bytes e vem limitada a 10 MB por padrão. Livewire
        // precisa aceitar o arquivo temporário antes de o Filament validar o
        // limite funcional de 100 MB e apresentar a mensagem no formulário.
        config([
            'media-library.max_file_size' => $heroVideoMaxKilobytes * 1024,
            'livewire.temporary_file_upload.rules' => [
                'required',
                'file',
                'max:'.config('pib.uploads.temporary_max_kb'),
            ],
        ]);

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
