<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

trait ConfirmsCreation
{
    protected function getCreateFormAction(): Action
    {
        return $this->confirmCreationAction(
            parent::getCreateFormAction()
                ->submit(null)
                ->action(fn () => $this->submitCreation()),
        );
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return $this->confirmCreationAction(
            parent::getCreateAnotherFormAction()
                ->action(fn () => $this->submitCreation(another: true)),
        );
    }

    private function submitCreation(bool $another = false): void
    {
        // A confirmação não deve encobrir mensagens de validação do formulário.
        $this->unmountAction(canCancelParentActions: false);
        $this->create(another: $another);
    }

    protected function getCreatedNotification(): ?Notification
    {
        $modelLabel = static::getResource()::getModelLabel();

        return Notification::make()
            ->success()
            ->title("{$modelLabel} criado com sucesso!")
            ->body('O '.Str::lower($modelLabel).' foi salvo no painel administrativo.')
            ->persistent()
            ->safeViews('filament.notifications.creation-success')
            ->view('filament.notifications.creation-success');
    }

    private function confirmCreationAction(Action $action): Action
    {
        return $action
            ->requiresConfirmation()
            ->modalHeading('Confirmar criação')
            ->modalDescription('Deseja criar este registro com as informações preenchidas?')
            ->modalSubmitActionLabel('Sim, criar registro')
            ->modalCancelActionLabel('Cancelar');
    }
}
