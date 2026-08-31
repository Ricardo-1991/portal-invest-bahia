<?php

namespace App\Notifications;

use Filament\Auth\Notifications\ResetPassword as FilamentResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends FilamentResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Redefinição de senha | Portal Invest Bahia')
            ->view([
                'html' => 'mail.auth.reset-password',
                'text' => 'mail.auth.reset-password-text',
            ], [
                'name' => $notifiable->name,
                'url' => $this->url,
                'expiresInMinutes' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]);
    }
}
