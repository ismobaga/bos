<?php

namespace App\Modules\LMS\Infrastructure\Listeners;

use App\Contracts\NotificationDispatcherInterface;
use App\Modules\LMS\Domain\Events\LearnerEnrolled;
use App\Modules\LMS\Domain\Models\Course;
use App\Modules\Notifications\Application\DTOs\NotificationMessageData;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEnrollmentWelcomeEmail implements ShouldQueue
{
    public function __construct(
        private readonly NotificationDispatcherInterface $notifications,
    ) {}

    public function handle(LearnerEnrolled $event): void
    {
        $user = User::find($event->userId);
        $course = Course::find($event->courseId);

        if (! $user || ! $course) {
            return;
        }

        $this->notifications->send(NotificationMessageData::make([
            'tenant_id' => $event->tenantId,
            'channel' => 'email',
            'to' => $user->email,
            'subject' => "Welcome to {$course->title}",
            'body' => "Hi {$user->name}, you have been enrolled in {$course->title}. Good luck!",
            'template_key' => 'lms.enrollment.welcome',
            'metadata' => [
                'course_id' => $event->courseId,
                'user_id' => $event->userId,
            ],
        ]));
    }
}
