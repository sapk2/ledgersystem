<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Payment extends Model
{
    use BelongsToPharmacy, HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'distributor_id',
        'amount',
        'payment_date',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function ledgerEntry(): MorphOne
    {
        return $this->morphOne(DistributorLedger::class, 'reference');
    }
}
