<?php

namespace App\Modules\Helpdesk\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusHistory extends Model
{
    protected $table = 'helpdesk_status_histories';

    protected $fillable = [
        'tenant_id',
        'ticket_id',
        'from_status',
        'to_status',
        'changed_by',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
