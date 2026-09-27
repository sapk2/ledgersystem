<?php

namespace App\Models\Concerns;

use App\Models\Pharmacy;
use App\Models\Scopes\PharmacyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Binds a model to the pharmacy of the currently authenticated pharmacy user.
 *
 * Reads are filtered by a global scope, writes receive the owner pharmacy
 * automatically, and Super Admin queries use `forPharmacy()`.
 */
trait BelongsToPharmacy
{
    public static function bootBelongsToPharmacy(): void
    {
        static::addGlobalScope(new PharmacyScope);

        static::creating(function ($model): void {
            if ($model->pharmacy_id !== null) {
                return;
            }

            if ($pharmacyId = Auth::user()?->pharmacy_id) {
                $model->pharmacy_id = $pharmacyId;
            }
        });
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function scopeForPharmacy(Builder $query, int|Pharmacy|null $pharmacy): Builder
    {
        return (new PharmacyScope)->scopeForPharmacy($query, $pharmacy);
    }
}
