<?php

namespace App\Providers;

use App\Models\Pharmacy;
use App\Models\Setting;
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
        $this->sharePlatformName();
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

    /**
     * Share the configurable platform name with the administration area.
     */
    private function sharePlatformName(): void
    {
        View::composer(['layouts.super-admin', 'layouts.super-admin-navigation'], function ($view): void {
            $view->with('platformName', Setting::get(Setting::PLATFORM_NAME, config('app.name', 'Laravel')));
        });
    }

    private function resolveCurrentPharmacy(): ?Pharmacy
    {
        $user = Auth::user();

        return $user?->isAdmin() ? null : $user?->pharmacy;
    }
}
