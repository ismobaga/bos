<?php

namespace App\Modules\Helpdesk\Domain\Models;

use App\Models\User;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $table = 'helpdesk_tickets';

    protected $fillable = [
        'tenant_id',
        'subject',
        'description',
        'status',
        'priority',
        'reporter_id',
        'assignee_id',
        'created_by',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class, 'ticket_id');
    }

    public function scopeTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
