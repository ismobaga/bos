<?php

namespace App\Modules\LMS\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $table = 'lms_lessons';

    protected $fillable = [
        'tenant_id',
        'course_id',
        'title',
        'content',
        'position',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }
}
