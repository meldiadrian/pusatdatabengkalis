<?php

namespace App\Filament\Pages\Auth;

use App\Rules\RecaptchaRule;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function getHeading(): Htmlable|string
    {
        return 'Pusat Data Kabupaten Bengkalis';
    }

    /**
     * Override form login bawaan Filament untuk menyisipkan
     * widget Google reCAPTCHA v2 Checkbox setelah field "Remember me".
     */
    // public function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             $this->getEmailFormComponent(),
    //             $this->getPasswordFormComponent(),
    //             $this->getRememberFormComponent(),

    //             ViewField::make('recaptcha')
    //                 ->view('filament.forms.components.recaptcha')
    //                 ->dehydrated(true)
    //                 ->required()
    //                 ->rule(new RecaptchaRule()),
    //         ]);
    // }

    // /**
    //  * Override agar captcha di-reset ketika login gagal
    //  * (kredensial salah), sehingga user harus centang ulang.
    //  */
    // protected function throwFailureValidationException(): never
    // {
    //     $this->dispatch('reset-recaptcha');

    //     throw ValidationException::withMessages([
    //         'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
    //     ]);
    // }
}
