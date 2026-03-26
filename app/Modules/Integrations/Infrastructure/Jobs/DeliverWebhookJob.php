<?php

namespace App\Modules\Integrations\Infrastructure\Jobs;

use App\Modules\Integrations\Domain\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(
        public readonly WebhookDelivery $delivery,
    ) {}

    public function handle(): void
    {
        $endpoint = $this->delivery->endpoint;

        $payload = $this->delivery->payload;
        $signature = hash_hmac('sha256', json_encode($payload), $endpoint->secret);

        $this->delivery->increment('attempt_count');

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Crommix-Signature' => "sha256={$signature}",
                'X-Crommix-Event' => $this->delivery->event_key,
            ])
            ->timeout(15)
            ->post($endpoint->url, $payload);

            $this->delivery->update([
                'http_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 1000),
                'status' => $response->successful() ? 'delivered' : 'failed',
                'next_retry_at' => $response->successful() ? null : now()->addMinutes(30),
            ]);
        } catch (\Throwable $e) {
            $this->delivery->update([
                'status' => 'failed',
                'next_retry_at' => now()->addMinutes(30),
            ]);

            throw $e;
        }
    }
}
