<?php

namespace App\Models;

use App\Modules\AccessControl\Domain\Models\Membership;
use App\Modules\Identity\Domain\Models\IdentityAccount;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'is_platform_admin',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function identityAccounts(): HasMany
    {
        return $this->hasMany(IdentityAccount::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function tenants()
    {
        return $this->hasManyThrough(
            Tenant::class,
            Membership::class,
            'user_id',
            'id',
            'id',
            'tenant_id'
        );
    }

    public function membershipForTenant(int $tenantId): ?Membership
    {
        return $this->memberships()->where('tenant_id', $tenantId)->first();
    }

    public function isPlatformAdmin(): bool
    {
        return $this->is_platform_admin;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
