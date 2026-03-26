<?php

namespace App\Modules\CRM\Domain\Models;

use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    protected $table = 'crm_tags';

    protected $fillable = [
        'tenant_id',
        'name',
        'color',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'crm_customer_tag', 'tag_id', 'customer_id');
    }
}
