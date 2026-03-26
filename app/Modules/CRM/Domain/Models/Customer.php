<?php

namespace App\Modules\CRM\Domain\Models;

use App\Models\User;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'crm_customers';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'email',
        'phone',
        'status',
        'billing_address_json',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'billing_address_json' => 'array',
        'metadata' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'customer_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'customer_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'crm_customer_tag', 'customer_id', 'tag_id');
    }

    public function scopeTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
