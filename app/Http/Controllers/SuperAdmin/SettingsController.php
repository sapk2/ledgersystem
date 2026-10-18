<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateSettingsRequest;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function edit(): View
    {
        return view('super-admin.settings.edit', [
            'platformName' => Setting::get(Setting::PLATFORM_NAME, config('app.name', 'Laravel')),
            'supportEmail' => Setting::get(Setting::SUPPORT_EMAIL),
            'defaultPharmacyStatus' => Setting::get(Setting::DEFAULT_PHARMACY_STATUS, 'active'),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Setting::set(Setting::PLATFORM_NAME, $validated['platform_name']);
        Setting::set(Setting::SUPPORT_EMAIL, $validated['support_email'] ?? null);
        Setting::set(Setting::DEFAULT_PHARMACY_STATUS, $validated['default_pharmacy_status']);

        $this->audit->log(AuditLog::SETTINGS_UPDATED, 'Updated platform settings.');

        return redirect()
            ->route('super-admin.settings.edit')
            ->with('status', 'Platform settings saved.');
    }
}
