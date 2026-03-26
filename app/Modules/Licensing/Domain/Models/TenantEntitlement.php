<?php

namespace App\Modules\Licensing\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantEntitlement extends Model
{
    use HasFactory;
    protected $fillable = [
        'tenant_id',
        'key',
        'value_type',
        'value_string',
        'value_integer',
        'value_boolean',
        'value_json',
        'source_type',
        'source_id',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'value_boolean' => 'boolean',
        'value_json' => 'array',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Tenancy\Domain\Models\Tenant::class);
    }

    public function getValue(): mixed
    {
        return match ($this->value_type) {
            'string' => $this->value_string,
            'integer' => $this->value_integer,
            'boolean' => $this->value_boolean,
            'json' => $this->value_json,
            default => null,
        };
    }

    public function isEffective(): bool
    {
        $now = now();

        if ($this->effective_from && $now->lt($this->effective_from)) {
            return false;
        }

        if ($this->effective_to && $now->gt($this->effective_to)) {
            return false;
        }

        return true;
    }
}
