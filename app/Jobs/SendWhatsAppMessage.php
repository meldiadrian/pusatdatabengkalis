<?php

namespace App\Jobs;

use Throwable;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [60, 120, 300];

    public function __construct(
        public readonly ?string $phone,
        public readonly string $message
    ) {
        // Validasi awal biar tidak crash saat dispatch
        if (blank($phone)) {
            throw new \InvalidArgumentException('Phone number cannot be null or empty');
        }
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        $whatsAppService->send(
            $this->phone,
            $this->message
        );
    }

    /**
     * Called when all retries are exhausted.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('WhatsApp Queue Failed', [
            'phone'   => $this->phone,
            'message' => $this->message,
            'error'   => $exception->getMessage(),
        ]);
    }
}

// {

//     use Queueable;

//     public $tries = 5; // coba 5x
//     public $backoff = [60, 120, 300]; // delay retry

//     protected $phone;
//     protected $message;

//     public function __construct($phone, $message)
//     {
//         $this->phone = $phone;
//         $this->message = $message;
//     }

//     /**
//      * Execute the job.
//      */
//     public function handle(): void
//     {
//         $response = (new WhatsAppService)->send(
//             $this->phone,
//             $this->message
//         );

//         // if (!$response->success) {

//         // }
//     }
// }
