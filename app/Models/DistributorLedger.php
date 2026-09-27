<?php

namespace App\Models;

use App\Models\Concerns\BelongsToPharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DistributorLedger extends Model
{
    use BelongsToPharmacy, HasFactory;

    protected $table = 'distributor_ledger';

    protected $fillable = [
        'pharmacy_id',
        'distributor_id',
        'entry_date',
        'voucher_no',
        'description',
        'type',
        'dr_amount',
        'cr_amount',
        'reference_type',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'dr_amount' => 'decimal:2',
            'cr_amount' => 'decimal:2',
        ];
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
