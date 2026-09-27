<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Purchase extends Model
{
    use BelongsToPharmacy, HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'distributor_id',
        'bill_no',
        'bill_date',
        'discount',
        'vat',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'discount' => 'decimal:2',
            'vat' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Sum of all line item amounts before discount/VAT.
     */
    public function subtotal(): float
    {
        return round((float) $this->items->sum(fn ($item) => $item->amount), 2);
    }

    /**
     * VAT amount applied after discount.
     */
    public function vatAmount(): float
    {
        return round(((float) $this->subtotal() - (float) $this->discount) * ((float) $this->vat / 100), 2);
    }

    /**
     * Net payable = subtotal - discount + VAT.
     */
    public function netAmount(): float
    {
        return round((float) $this->subtotal() - (float) $this->discount + $this->vatAmount(), 2);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function ledgerEntry(): MorphOne
    {
        return $this->morphOne(DistributorLedger::class, 'reference');
    }
}
