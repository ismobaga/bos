<?php

namespace App\Modules\Invoice\Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    protected $table = 'invoice_payments';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'amount_minor',
        'currency',
        'paid_at',
        'method',
        'reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
