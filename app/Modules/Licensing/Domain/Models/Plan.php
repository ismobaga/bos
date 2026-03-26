<?php

namespace App\Modules\Licensing\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    protected $fillable = [
        'key',
        'name',
        'billing_period',
        'price_minor',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(ProductModule::class, 'plan_modules', 'plan_id', 'module_id');
    }
}
