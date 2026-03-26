<?php

namespace App\Modules\Licensing\Domain\Events;

use App\Modules\Licensing\Domain\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Subscription $subscription,
        public readonly ?int $previousPlanId = null,
    ) {}
}
