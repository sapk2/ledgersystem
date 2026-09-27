<?php

namespace App\Models\Scopes;

use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Restricts a query to the pharmacy of the currently authenticated pharmacy user.
 *
 * Super Admins intentionally see every pharmacy, and no restriction is applied
 * outside of an HTTP session (console commands, queued jobs, seeders).
 */
class PharmacyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $user = Auth::user();

        if (! $user || $user->isAdmin() || ! $user->pharmacy_id) {
            return;
        }

        $builder->where($model->qualifyColumn('pharmacy_id'), $user->pharmacy_id);
    }

    /**
     * Limit the query to a specific pharmacy, e.g. for a Super Admin.
     */
    public function scopeForPharmacy(Builder $builder, int|Pharmacy|null $pharmacy): Builder
    {
        if ($pharmacy === null) {
            return $builder;
        }

        return $builder->where(
            $builder->getModel()->qualifyColumn('pharmacy_id'),
            $pharmacy instanceof Pharmacy ? $pharmacy->getKey() : $pharmacy,
        );
    }
}
