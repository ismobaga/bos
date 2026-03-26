<?php

namespace App\Modules\LMS\Http\Controllers;

use App\Modules\LMS\Application\Actions\CreateCourseAction;
use App\Modules\LMS\Domain\Models\Course;
use App\Modules\Tenancy\Domain\ValueObjects\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController
{
    public function index(Request $request): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $courses = Course::tenant($ctx->id())
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($courses);
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $this->authorizeTenantAccess($course);

        return response()->json($course->load('lessons'));
    }

    public function store(Request $request, CreateCourseAction $action): JsonResponse
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:draft,published,archived',
        ]);

        $course = $action->execute($ctx->id(), $request->user()->id, $validated);

        return response()->json($course, 201);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $this->authorizeTenantAccess($course);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:draft,published,archived',
        ]);

        $course->update(array_merge($validated, ['updated_by' => $request->user()->id]));

        return response()->json($course);
    }

    public function destroy(Request $request, Course $course): JsonResponse
    {
        $this->authorizeTenantAccess($course);
        $course->delete();

        return response()->json(null, 204);
    }

    private function authorizeTenantAccess(Course $course): void
    {
        /** @var TenantContext $ctx */
        $ctx = app(TenantContext::class);
        abort_unless($course->tenant_id === $ctx->id(), 403);
    }
}
