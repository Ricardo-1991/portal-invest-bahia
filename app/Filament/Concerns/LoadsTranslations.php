<?php

namespace App\Filament\Concerns;

/**
 * Carrega os campos traduzíveis do registro como arrays completos ao preencher
 * o formulário (Edit), permitindo editar cada idioma nas abas.
 */
trait LoadsTranslations
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->getRecord()->getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->getRecord()->getTranslations($attribute);
        }

        return $data;
    }
}
