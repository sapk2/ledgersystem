<?php

use App\Models\AuditLog;
use App\Models\Pharmacy;
use App\Models\Setting;
use App\Models\User;

test('an admin can view the platform settings page', function () {
    admin(acting: true);

    $this->get(route('super-admin.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Platform name')
        ->assertSee('Support email')
        ->assertSee('Default status for new pharmacies');
});

test('an admin can update the platform settings', function () {
    admin(acting: true);

    $this->put(route('super-admin.settings.update'), [
        'platform_name' => 'MediLedger',
        'support_email' => 'help@mediledger.test',
        'default_pharmacy_status' => 'inactive',
    ])->assertRedirect(route('super-admin.settings.edit'));

    expect(Setting::get(Setting::PLATFORM_NAME))->toBe('MediLedger')
        ->and(Setting::get(Setting::SUPPORT_EMAIL))->toBe('help@mediledger.test')
        ->and(Setting::get(Setting::DEFAULT_PHARMACY_STATUS))->toBe('inactive')
        ->and(AuditLog::where('action', AuditLog::SETTINGS_UPDATED)->count())->toBe(1);
});

test('the configured platform name is shown in the administration area', function () {
    admin(acting: true);
    Setting::set(Setting::PLATFORM_NAME, 'MediLedger');

    $this->get(route('super-admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('MediLedger');
});

test('settings validate their input', function () {
    admin(acting: true);

    $this->put(route('super-admin.settings.update'), [
        'platform_name' => '',
        'support_email' => 'not-an-email',
        'default_pharmacy_status' => 'bogus',
    ])->assertSessionHasErrors(['platform_name', 'support_email', 'default_pharmacy_status']);
});

test('the default pharmacy status setting is applied when creating a pharmacy', function () {
    admin(acting: true);
    Setting::set(Setting::DEFAULT_PHARMACY_STATUS, 'inactive');

    $this->post(route('super-admin.pharmacies.store'), [
        'name' => 'Inactive By Default',
        'owner_name' => 'Owner',
        'email' => 'inactive-default@pharmacy.test',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ])->assertRedirect();

    $pharmacy = Pharmacy::where('email', 'inactive-default@pharmacy.test')->firstOrFail();

    expect($pharmacy->is_active)->toBeFalse();
});

test('a pharmacy user is forbidden from the settings area', function () {
    $user = User::factory()->withPharmacy()->create();

    $this->actingAs($user)->get(route('super-admin.settings.edit'))->assertForbidden();
    $this->actingAs($user)->put(route('super-admin.settings.update'), [])->assertForbidden();
});
