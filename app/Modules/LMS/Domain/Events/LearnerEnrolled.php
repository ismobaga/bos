<?php

namespace App\Modules\LMS\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LearnerEnrolled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $courseId,
        public readonly int $userId,
    ) {}
}
