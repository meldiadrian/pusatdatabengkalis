<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Rules\RecaptchaRule;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
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
     * Override form login: ganti field email menjadi username,
     * tambahkan Google reCAPTCHA v2 di bawah field password.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('username')
                    ->label('Username')
                    ->placeholder('Masukkan username Anda')
                    ->required()
                    ->autocomplete('username')
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),

                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),

                // Google reCAPTCHA v2 — widget & validasi server-side
                // ViewField::make('recaptcha_token')
                //     ->view('filament.forms.components.recaptcha')
                //     ->label('')
                //     ->rules([new RecaptchaRule()]),
            ])
            ->statePath('data');
    }

    /**
     * Override authenticate: cari email dari username, lalu teruskan ke auth Laravel.
     * Username dipakai sebagai identifier, password tetap divalidasi secara normal.
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        // Cari user berdasarkan username
        $user = User::where('username', $data['username'])->first();

        if (!$user) {
            // Lempar error seolah login gagal — jangan bocorkan info "user tidak ada"
            $this->throwFailureValidationException();
        }

        // Kembalikan kredensial email+password agar guard Laravel bisa proses normal
        return [
            'email' => $user->email,
            'password' => $data['password'],
        ];
    }

    /**
     * Tampilkan pesan gagal yang mengarah ke field username (bukan email).
     * Reset widget reCAPTCHA agar user bisa mencoba ulang.
     */
    protected function throwFailureValidationException(): never
    {
        // Reset reCAPTCHA widget supaya user harus centang ulang setelah gagal login
        $this->dispatch('reset-recaptcha');

        throw ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
