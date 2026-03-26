<?php

namespace App\Modules\AccessControl\Domain\Events;

use App\Modules\AccessControl\Domain\Models\Membership;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MembershipAdded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Membership $membership,
    ) {}
}
