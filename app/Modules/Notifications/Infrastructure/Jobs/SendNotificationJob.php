<?php

namespace App\Modules\Notifications\Infrastructure\Jobs;

use App\Modules\Notifications\Domain\Models\NotificationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly NotificationMessage $message,
    ) {}

    public function handle(): void
    {
        try {
            // Route to appropriate channel driver
            match ($this->message->channel) {
                'email' => $this->sendEmail(),
                'whatsapp' => $this->sendWhatsapp(),
                'sms' => $this->sendSms(),
                default => throw new \RuntimeException("Unsupported channel: {$this->message->channel}"),
            };

            $this->message->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->message->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function sendEmail(): void
    {
        // Implementation delegated to email channel driver
    }

    private function sendWhatsapp(): void
    {
        // Implementation delegated to WhatsApp channel driver
    }

    private function sendSms(): void
    {
        // Implementation delegated to SMS channel driver
    }
}
