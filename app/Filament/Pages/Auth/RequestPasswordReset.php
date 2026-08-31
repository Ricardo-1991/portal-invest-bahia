<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected function getFailureNotification(string $status): ?Notification
    {
        if (in_array($status, [Password::INVALID_USER, Password::RESET_THROTTLED], strict: true)) {
            return $this->genericSentNotification();
        }

        return parent::getFailureNotification($status);
    }

    protected function getSentNotification(string $status): ?Notification
    {
        return $this->genericSentNotification();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        $user = User::query()->where('email', $data['email'])->first();

        if (! $user?->canAccessPanel(Filament::getCurrentOrDefaultPanel())) {
            return ['email' => Str::uuid().'@invalid.local'];
        }

        return ['email' => $user->email];
    }

    private function genericSentNotification(): Notification
    {
        return Notification::make()
            ->title('Solicitação recebida')
            ->body('Se existir uma conta autorizada para este e-mail, enviaremos um link para redefinir a senha.')
            ->success();
    }
}
