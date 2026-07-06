<?php

namespace App\Filament\Concerns;

use App\Filament\Support\LocaleTabs;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

/**
 * Impede publicar um classificado sem título e descrição preenchidos em pelo
 * menos um idioma (o corretor publica apenas no idioma dele; o site resolve
 * o idioma exibido para o visitante). Aplicado às páginas Create e Edit do
 * ListingResource.
 */
trait GuardsListingPublication
{
    /** Campos que precisam estar preenchidos juntos, no mesmo idioma, para publicar. */
    protected array $requiredOnPublish = ['title', 'description'];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Garante a posse: corretor sempre é dono do próprio anúncio; admin pode
        // ter escolhido outro corretor no formulário.
        if (! auth()->user()->isAdmin() || blank($data['user_id'] ?? null)) {
            $data['user_id'] = auth()->id();
        }

        $this->guardComplete($data);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mesma garantia de posse do create: corretor não pode reatribuir o
        // próprio anúncio para outro user_id (o campo é oculto para ele, mas
        // "oculto" no Filament não impede o valor de ser enviado no payload).
        if (! auth()->user()->isAdmin()) {
            $data['user_id'] = auth()->id();
        }

        $this->guardComplete($data);

        return $data;
    }

    protected function guardComplete(array $data): void
    {
        if (($data['status'] ?? null) !== 'published') {
            return;
        }

        $hasCompleteLocale = collect(array_keys(LocaleTabs::LOCALES))
            ->contains(fn (string $locale) => collect($this->requiredOnPublish)
                ->every(fn (string $field) => filled($data[$field][$locale] ?? null)));

        if ($hasCompleteLocale) {
            return;
        }

        Notification::make()
            ->danger()
            ->title('Conteúdo incompleto')
            ->body('Para publicar, preencha título e descrição em pelo menos um idioma. Salve como rascunho enquanto completa.')
            ->persistent()
            ->send();

        throw new Halt();
    }
}
