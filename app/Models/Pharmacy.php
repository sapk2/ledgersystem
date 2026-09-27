<?php

namespace App\Models;

use Database\Factories\PharmacyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'owner_name',
    'email',
    'phone',
    'address',
    'city',
    'district',
    'province',
    'country',
    'is_active',
])]
class Pharmacy extends Model
{
    /** @use HasFactory<PharmacyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function distributors(): HasMany
    {
        return $this->hasMany(Distributor::class);
    }

    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(DistributorLedger::class);
    }

    /**
     * The pharmacy of the authenticated pharmacy user, if any.
     */
    public static function current(): ?self
    {
        return auth()->user()?->pharmacy;
    }

    /**
     * Address components as printable lines, skipping empty values.
     *
     * @return list<string>
     */
    public function addressLines(): array
    {
        return array_values(array_filter([
            $this->address,
            $this->city,
            $this->district,
            $this->province,
            $this->country,
        ]));
    }
}
