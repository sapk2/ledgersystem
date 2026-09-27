<?php

namespace App\Providers;

use App\Models\Pharmacy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->shareCurrentPharmacy();
    }

    /**
     * Share the authenticated pharmacy so printed documents and the UI never hard-code its identity.
     */
    private function shareCurrentPharmacy(): void
    {
        View::composer('*', function ($view): void {
            $view->with('currentPharmacy', $this->resolveCurrentPharmacy());
        });
    }

    private function resolveCurrentPharmacy(): ?Pharmacy
    {
        $user = Auth::user();

        return $user?->isAdmin() ? null : $user?->pharmacy;
    }
}
