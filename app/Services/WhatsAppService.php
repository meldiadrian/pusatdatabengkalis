<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function send(string $phone, string $message): bool
    {
        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->post(
                    config('services.whatsapp.url') . '/message/send-text',
                    [
                        'session'  => config('services.whatsapp.token'),
                        'to'       => $phone,
                        'text'     => $message,
                        'is_group' => false,
                    ]
                );

            if (! $response->successful()) {
                Log::error('WhatsApp API Error', [
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new Exception(
                    'WhatsApp API Error: ' . $response->body()
                );
            }

            return true;
        } catch (RequestException $e) {
            Log::error('WhatsApp Request Exception', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '8')) {
            return '62' . $phone;
        }

        return $phone;
    }
}
// {
//     public function send($phone, $message)
//     {
//         try {
//             // contoh API call
//             // Http::post(...);

//             //------
//             $response = Http::withoutVerifying()->post(config('services.whatsapp.url') . '/message/send-text', [
//                 'session' => config('services.whatsapp.token'),
//                 'to' => $phone,
//                 'text' => $message,
//                 'is_group' => false,
//             ]);
//             if (!$response->successful()) {
//                 SendWhatsAppMessage::dispatch(
//                     $phone,
//                     $message
//                 );
//             }

//             //------

//             return true;
//         } catch (\Exception $e) {
//             throw $e;
//         }
//     }
// }
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
