<?php

namespace App\Filament\Support;

use Closure;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Constrói um conjunto de abas (uma por idioma) para campos traduzíveis.
 *
 * Como o Filament v5 ainda não tem plugin oficial de tradução, os campos usam
 * statePath aninhado (ex.: "title.pt") e as páginas do Resource carregam/salvam
 * o array completo via getTranslations()/setTranslations().
 */
class LocaleTabs
{
    /** Idiomas suportados => rótulo exibido. */
    public const LOCALES = [
        'pt' => 'Português',
        'en' => 'English',
        'es' => 'Español',
        'it' => 'Italiano',
    ];

    /**
     * @param  Closure(string $locale): array  $fields  Recebe o locale e devolve os componentes daquela aba.
     */
    public static function make(Closure $fields, string $label = 'Traduções'): Tabs
    {
        return Tabs::make($label)
            ->tabs(
                collect(self::LOCALES)
                    ->map(fn (string $rotulo, string $locale) => Tab::make($rotulo)->schema($fields($locale)))
                    ->values()
                    ->all()
            )
            ->columnSpanFull();
    }
}
