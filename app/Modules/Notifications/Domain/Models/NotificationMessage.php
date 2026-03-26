<?php

namespace App\Modules\Notifications\Domain\Models;

use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationMessage extends Model
{
    protected $table = 'notification_messages';

    protected $fillable = [
        'tenant_id',
        'channel',
        'to',
        'subject',
        'body',
        'status',
        'metadata',
        'reference',
        'sent_at',
        'failed_at',
        'error',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
