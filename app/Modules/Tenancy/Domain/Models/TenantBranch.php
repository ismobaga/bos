<?php

namespace App\Modules\Tenancy\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantBranch extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'address_json',
        'status',
    ];

    protected $casts = [
        'address_json' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
