<?php

namespace App\Modules\Notifications\Application\Services;

use App\Contracts\NotificationDispatcherInterface;
use App\Modules\Notifications\Application\DTOs\NotificationMessageData;
use App\Modules\Notifications\Domain\Models\NotificationMessage;
use App\Modules\Notifications\Infrastructure\Jobs\SendNotificationJob;

class NotificationDispatcher implements NotificationDispatcherInterface
{
    public function send(NotificationMessageData $data): void
    {
        $message = NotificationMessage::create([
            'tenant_id' => $data->tenantId,
            'channel' => $data->channel,
            'to' => $data->to,
            'subject' => $data->subject,
            'body' => $data->body,
            'status' => 'pending',
            'metadata' => $data->metadata,
        ]);

        SendNotificationJob::dispatch($message);
    }
}
