<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\ResetPassword as BaseResetPassword;
use Filament\Schemas\Components\Component;

class ResetPassword extends BaseResetPassword
{
    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->helperText('A senha necessita de pelo menos 8 caracteres.')
            ->validationMessages([
                'min' => 'A senha necessita de pelo menos 8 caracteres.',
            ]);
    }
}
