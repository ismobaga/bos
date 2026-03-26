<?php

namespace App\Modules\Invoice\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceReminder extends Model
{
    protected $table = 'invoice_reminders';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'channel',
        'status',
        'scheduled_at',
        'sent_at',
        'template_key',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
