<?php

namespace App\Modules\LMS\Application\Actions;

use App\Modules\LMS\Domain\Models\Course;
use Illuminate\Support\Str;

class CreateCourseAction
{
    public function execute(int $tenantId, int $userId, array $data): Course
    {
        return Course::create([
            'tenant_id' => $tenantId,
            'title' => $data['title'],
            'slug' => $this->generateSlug($tenantId, $data['title']),
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }

    private function generateSlug(int $tenantId, string $title): string
    {
        $slug = Str::slug($title);
        $count = Course::where('tenant_id', $tenantId)->where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
