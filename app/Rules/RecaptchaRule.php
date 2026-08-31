<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Custom validation rule untuk memverifikasi token Google reCAPTCHA v2
 * ke endpoint Google secara server-side.
 *
 * Strategi: Fail-closed — jika API Google unreachable, login ditolak.
 */
class RecaptchaRule implements ValidationRule
{
    /**
     * Jalankan validasi terhadap token reCAPTCHA.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Token kosong berarti user belum mencentang captcha
        if (empty($value)) {
            $fail('Verifikasi captcha diperlukan. Silakan centang "Saya bukan robot".');
            return;
        }

        $secret = config('captcha.secret');

        // Pastikan secret key sudah dikonfigurasi
        if (empty($secret)) {
            Log::critical('reCAPTCHA secret key belum dikonfigurasi di config/captcha.php');
            $fail('Konfigurasi captcha belum lengkap. Hubungi administrator.');
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->connectTimeout(3)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => $secret,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (! $response->successful()) {
                Log::error('reCAPTCHA API returned non-200 status', [
                    'status' => $response->status(),
                    'ip'     => request()->ip(),
                ]);
                $fail('Verifikasi captcha gagal. Silakan coba lagi.');
                return;
            }

            $body = $response->json();

            if (empty($body['success'])) {
                Log::warning('reCAPTCHA verification failed', [
                    'ip'          => request()->ip(),
                    'error-codes' => $body['error-codes'] ?? [],
                ]);
                $fail('Verifikasi captcha gagal. Silakan coba lagi.');
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('reCAPTCHA API connection timeout', [
                'message' => $e->getMessage(),
                'ip'      => request()->ip(),
            ]);
            $fail('Layanan verifikasi captcha tidak tersedia. Silakan coba beberapa saat lagi.');
        } catch (\Exception $e) {
            Log::error('reCAPTCHA unexpected error', [
                'message' => $e->getMessage(),
                'ip'      => request()->ip(),
            ]);
            // Fail-closed: tolak login jika ada error tak terduga
            $fail('Terjadi kesalahan pada verifikasi captcha. Silakan coba lagi.');
        }
    }
}
