<?php

namespace App\Modules\AccessControl\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'scope',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function memberships()
    {
        return $this->hasManyThrough(Membership::class, MembershipRole::class, 'role_id', 'id', 'id', 'membership_id');
    }
}
