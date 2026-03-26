<?php

namespace App\Modules\LMS\Http\Controllers;

use App\Contracts\LicensingManagerInterface;
use App\Modules\LMS\Domain\Events\LearnerEnrolled;
use App\Modules\LMS\Domain\Models\Course;
use App\Modules\LMS\Domain\Models\Enrollment;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController
{
    public function __construct(
        private readonly LicensingManagerInterface $licensing,
    ) {}

    public function store(Request $request, Course $course): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($course->tenant_id === $ctx->id(), 403);

        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'in:learner,instructor,assistant',
        ]);

        $existing = Enrollment::where([
            'tenant_id' => $ctx->id(),
            'course_id' => $course->id,
            'user_id' => $validated['user_id'],
        ])->first();

        abort_if($existing, 422, 'User is already enrolled in this course.');

        $enrollment = Enrollment::create([
            'tenant_id' => $ctx->id(),
            'course_id' => $course->id,
            'user_id' => $validated['user_id'],
            'role' => $validated['role'] ?? 'learner',
            'status' => 'enrolled',
            'enrolled_at' => now(),
        ]);

        $this->licensing->consume($ctx->id(), 'lms.learners.limit');

        LearnerEnrolled::dispatch($ctx->id(), $course->id, $validated['user_id']);

        return response()->json($enrollment, 201);
    }
}
