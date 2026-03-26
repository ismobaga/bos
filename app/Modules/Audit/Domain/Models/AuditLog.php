<?php

namespace App\Modules\Audit\Domain\Models;

use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'tenant_id',
        'module',
        'action',
        'actor_type',
        'actor_id',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'context',
        'ip_address',
        'user_agent',
        'correlation_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'context' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
