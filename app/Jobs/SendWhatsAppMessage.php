<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
//use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\WhatsAppService;

class SendWhatsAppMessage implements ShouldQueue
{

    use Queueable;

    public $tries = 5; // coba 5x
    public $backoff = [60, 120, 300]; // delay retry

    protected $phone;
    protected $message;

    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = (new WhatsAppService)->send(
            $this->phone,
            $this->message
        );

        // if (!$response->success) {

        // }
    }
}
