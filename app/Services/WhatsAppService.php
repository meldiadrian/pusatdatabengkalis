<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function send($phone, $message)
    {
        try {
            // contoh API call
            // Http::post(...);

            //------
            $response = Http::withoutVerifying()->post(config('services.whatsapp.url') . '/message/send-text', [
                'session' => config('services.whatsapp.token'),
                'to' => $phone,
                'text' => $message,
                'is_group' => false,
            ]);
            if (!$response->successful()) {
                SendWhatsAppMessage::dispatch(
                    $phone,
                    $message
                );
            }

            //------

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
    // public static function send(string $message, ?string $target = null)
    // {
    //     $token = config('services.whatsapp.token');
    //     $url   = config('services.whatsapp.url');

    //     // Jika target tidak di-pass manual, ambil dari tabel users
    //     // if (! $target) {
    //     //     $target = self::getTargetsFromUsers();
    //     // }

    //     // if (! $token || ! $target) {
    //     //     Log::warning('WhatsApp: token atau target kosong, notifikasi tidak dikirim.');
    //     //     return;
    //     // }

    //     try {
    //         dispatch(function () use ($token, $url, $target, $message) {
    //             Http::post($url . '/message/send-text', [
    //                 "session"  => $token,
    //                 "to" => $this->normalizePhone($target), // fonnte support multiple: "628xx,628yy"
    //                 "text" => $message,
    //             ]);
    //         })->afterResponse();
    //     } catch (\Throwable $e) {
    //         Log::error('WhatsApp send error: ' . $e->getMessage());
    //     }
    // }

    // /**
    //  * Ambil semua no_hp dari tabel users yang tidak kosong.
    //  * Bisa filter berdasarkan role tertentu jika perlu.
    //  */
    // private static function getTargetsFromUsers(): ?string
    // {
    //     $numbers = User::query()
    //         ->whereNotNull('no_hp')
    //         ->where('no_hp', '!=', '')
    //         // ->whereHasRole(User::ROLE_ADMIN) // uncomment jika hanya admin/validator
    //         ->pluck('no_hp')
    //         ->map(fn($no) => self::normalizePhone($no))
    //         ->filter()
    //         ->unique()
    //         ->implode(',');

    //     return $numbers ?: null;
    // }

    // /**
    //  * Normalisasi nomor: 08xx → 628xx
    //  */
    // function normalizePhone($no)
    // {
    //     $no = preg_replace('/\D/', '', $no); // hapus non-digit

    //     if (str_starts_with($no, '0')) {
    //         $no = '62' . substr($no, 1);
    //     } elseif (str_starts_with($no, '8')) {
    //         $no = '62' . $no;
    //     }

    //     return $no;
    // }
