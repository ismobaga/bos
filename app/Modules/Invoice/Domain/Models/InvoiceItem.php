<?php

namespace App\Modules\Invoice\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'description',
        'quantity',
        'unit_price_minor',
        'tax_rate',
        'line_total_minor',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function calculateLineTotal(): int
    {
        return (int) ($this->quantity * $this->unit_price_minor);
    }
}
