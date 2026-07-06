<?php

namespace App\Filament\Concerns;

use App\Filament\Support\LocaleTabs;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

/**
 * Carrega os campos traduzíveis como arrays no formulário e impede a publicação
 * de um classificado sem título e descrição preenchidos nos 4 idiomas.
 *
 * Usado por CreateListing e EditListing.
 */
trait HandlesListingTranslations
{
    /** Campos obrigatórios em todos os idiomas para publicar. */
    protected array $requiredOnPublish = ['title', 'description'];

    /** Preenche o formulário (Edit) com o array completo de traduções. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->getRecord()->getTranslatableAttributes() as $attribute) {
            $data[$attribute] = $this->getRecord()->getTranslations($attribute);
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->guardComplete($data);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->guardComplete($data);

        return $data;
    }

    /** Bloqueia o save quando publicado sem os 4 idiomas. */
    protected function guardComplete(array $data): void
    {
        if (($data['status'] ?? null) !== 'published') {
            return;
        }

        foreach ($this->requiredOnPublish as $field) {
            foreach (array_keys(LocaleTabs::LOCALES) as $locale) {
                if (blank($data[$field][$locale] ?? null)) {
                    Notification::make()
                        ->danger()
                        ->title('Tradução incompleta')
                        ->body('Para publicar, preencha título e descrição nos 4 idiomas (PT, EN, ES, IT). Salve como rascunho enquanto completa.')
                        ->persistent()
                        ->send();

                    throw new Halt();
                }
            }
        }
    }
}
