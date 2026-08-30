<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;

trait ConfirmsSave
{
    /** Exige confirmação antes de persistir qualquer edição no painel. */
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            // O submit HTML direto ignora o ciclo de modal do Filament. Uma
            // closure transforma o botão em Action e salva só após confirmar.
            ->submit(null)
            ->action(fn (): mixed => $this->save())
            ->requiresConfirmation()
            ->modalHeading('Confirmar alterações')
            ->modalDescription('Deseja guardar as alterações realizadas neste registro?')
            ->modalSubmitActionLabel('Sim, guardar alterações')
            ->modalCancelActionLabel('Cancelar');
    }
}
