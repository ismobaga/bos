<?php

namespace App\Modules\Identity\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdentityAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'email',
        'subject',
        'raw_claims',
        'last_login_at',
    ];

    protected $casts = [
        'raw_claims' => 'array',
        'last_login_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
