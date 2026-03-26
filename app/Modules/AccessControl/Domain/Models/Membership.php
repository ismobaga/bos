<?php

namespace App\Modules\AccessControl\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'status',
        'joined_at',
        'invited_by',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Tenancy\Domain\Models\Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'invited_by');
    }

    public function membershipRoles(): HasMany
    {
        return $this->hasMany(MembershipRole::class);
    }

    public function roles()
    {
        return $this->hasManyThrough(Role::class, MembershipRole::class, 'membership_id', 'id', 'id', 'role_id');
    }

    public function hasPermission(string $permissionKey): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn ($q) => $q->where('key', $permissionKey))
            ->exists();
    }
}
