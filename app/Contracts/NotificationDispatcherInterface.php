<?php

namespace App\Contracts;

use App\Modules\Notifications\Application\DTOs\NotificationMessageData;

interface NotificationDispatcherInterface
{
    public function send(NotificationMessageData $data): void;
}
