<?php

namespace App\Modules\Licensing\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCounter extends Model
{
    use HasFactory;
    protected $table = 'usage_counters';

    protected $fillable = [
        'tenant_id',
        'key',
        'period_start',
        'period_end',
        'used_value',
        'reset_strategy',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Tenancy\Domain\Models\Tenant::class);
    }
}
