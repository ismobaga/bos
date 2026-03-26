<?php

namespace App\Modules\Invoice\Domain\Models;

use App\Models\User;
use App\Modules\CRM\Domain\Models\Customer;
use App\Modules\Tenancy\Domain\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoice_invoices';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'number',
        'status',
        'currency',
        'issue_date',
        'due_date',
        'subtotal_minor',
        'tax_minor',
        'total_minor',
        'amount_paid_minor',
        'balance_due_minor',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(InvoiceReminder::class, 'invoice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && ! in_array($this->status, ['paid', 'cancelled']);
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items->sum('line_total_minor');
        $tax = $this->items->sum(fn ($item) => $item->unit_price_minor * $item->quantity * $item->tax_rate / 100);
        $total = $subtotal + $tax;
        $paid = $this->payments->sum('amount_minor');

        $this->update([
            'subtotal_minor' => $subtotal,
            'tax_minor' => (int) $tax,
            'total_minor' => (int) $total,
            'amount_paid_minor' => $paid,
            'balance_due_minor' => max(0, (int) $total - $paid),
        ]);
    }

    public function scopeTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
