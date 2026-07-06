<?php

namespace App\Filament\Concerns;

/**
 * Garante que o corretor é sempre o dono do próprio evento (admin pode
 * escolher outro corretor no formulário). Aplicado às páginas Create e Edit
 * do EventResource — mesmo padrão usado em GuardsListingPublication.
 */
trait EnforcesEventOwnership
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()->isAdmin() || blank($data['user_id'] ?? null)) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Campo é oculto para o corretor, mas "oculto" no Filament não impede
        // o valor de ser enviado no payload — reforça no update também.
        if (! auth()->user()->isAdmin()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
