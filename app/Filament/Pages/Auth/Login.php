<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    public function getHeading(): Htmlable|string
    {
        return 'Pusat Data Kabupaten Bengkalis';
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Login');
    }

    // public function getHeading(): string
    // {
    //     return 'Login Admin';
    // }
}
