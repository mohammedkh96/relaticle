<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phoneNumber;
    public $message;
    public $isTemplate;
    public $templateName;
    public $templateParams;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $phoneNumber,
        string $message = '',
        bool $isTemplate = false,
        ?string $templateName = null,
        array $templateParams = []
    ) {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->isTemplate = $isTemplate;
        $this->templateName = $templateName;
        $this->templateParams = $templateParams;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        try {
            if ($this->isTemplate && $this->templateName) {
                $whatsAppService->sendTemplate($this->phoneNumber, $this->templateName, $this->templateParams);
            } else {
                $whatsAppService->sendMessage($this->phoneNumber, $this->message);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp message to {$this->phoneNumber}: " . $e->getMessage());
            // Optionally release the job back to the queue to retry later
            // $this->release(10); 
        }
    }
}
