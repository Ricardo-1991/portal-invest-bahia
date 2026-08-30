<?php

namespace App\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

    public function canCreateAnother(): bool
    {
        return false;
    }

    private function submitCreation(): void
    {
        // A confirmação não deve encobrir mensagens de validação do formulário.
        $this->unmountAction(canCancelParentActions: false);

        try {
            // Mantém a página de criação aberta e reinicializa todos os campos
            // somente depois de o registro ter sido salvo com sucesso.
            $this->create(another: true);
        } catch (ValidationException $exception) {
            $this->sendValidationErrorModal($exception);

            throw $exception;
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        $modelLabel = static::getResource()::getModelLabel();

        return Notification::make()
            ->success()
            ->title("{$modelLabel} criado com sucesso!")
            ->body('O '.Str::lower($modelLabel).' foi salvo. O formulário está pronto para um novo cadastro.')
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

    private function sendValidationErrorModal(ValidationException $exception): void
    {
        $message = collect($exception->errors())
            ->flatten()
            ->first(fn ($message) => filled($message));

        Notification::make()
            ->danger()
            ->title('Não foi possível concluir o cadastro')
            ->body($message ?: 'Revise os campos destacados e tente novamente.')
            ->persistent()
            ->safeViews('filament.notifications.creation-success')
            ->view('filament.notifications.creation-success')
            ->send();
    }
}
