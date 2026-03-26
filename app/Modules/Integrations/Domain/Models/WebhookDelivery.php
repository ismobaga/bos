<?php

namespace App\Modules\Integrations\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDelivery extends Model
{
    protected $table = 'webhook_deliveries';

    protected $fillable = [
        'webhook_endpoint_id',
        'event_key',
        'payload',
        'http_status',
        'response_body',
        'status',
        'attempt_count',
        'next_retry_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'next_retry_at' => 'datetime',
    ];

    public function endpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }
}
