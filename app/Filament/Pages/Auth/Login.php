<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        $component = parent::getEmailFormComponent();

        $component->validationMessages([
            'required' => 'حقل البريد الإلكتروني مطلوب.',
            'email' => 'يرجى إدخال بريد إلكتروني صحيح.',
        ]);

        return $component;
    }

    protected function getPasswordFormComponent(): Component
    {
        $component = parent::getPasswordFormComponent();

        $component->validationMessages([
            'required' => 'حقل كلمة المرور مطلوب.',
        ]);

        return $component;
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ]);
    }
}
