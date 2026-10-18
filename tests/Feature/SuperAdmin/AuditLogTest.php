<?php

use App\Models\AuditLog;
use App\Models\Pharmacy;
use App\Models\User;

test('creating a pharmacy records an audit entry', function () {
    $admin = admin(acting: true);

    $this->post(route('super-admin.pharmacies.store'), [
        'name' => 'Audited Pharmacy',
        'owner_name' => 'Audited Owner',
        'email' => 'audited@pharmacy.test',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ])->assertRedirect();

    $log = AuditLog::first();

    expect($log)->not->toBeNull()
        ->and($log->action)->toBe(AuditLog::PHARMACY_CREATED)
        ->and($log->user_id)->toBe($admin->id)
        ->and($log->description)->toContain('Audited Pharmacy');
});

test('updating and deleting a pharmacy records audit entries', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create(['name' => 'Tracked Pharmacy']);

    $this->put(route('super-admin.pharmacies.update', $pharmacy), [
        'name' => 'Renamed Pharmacy',
        'owner_name' => 'Owner',
        'email' => $pharmacy->email,
    ])->assertRedirect();

    $this->delete(route('super-admin.pharmacies.destroy', $pharmacy))->assertRedirect();

    expect(AuditLog::where('action', AuditLog::PHARMACY_UPDATED)->count())->toBe(1)
        ->and(AuditLog::where('action', AuditLog::PHARMACY_DELETED)->count())->toBe(1);
});

test('reset passwords are recorded without storing the secret', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create(['name' => 'Password Pharmacy']);

    $this->put(route('super-admin.pharmacies.password', $pharmacy), [
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertSessionHasNoErrors();

    $log = AuditLog::where('action', AuditLog::PHARMACY_PASSWORD)->first();

    expect($log)->not->toBeNull()
        ->and($log->description)->not->toContain('brand-new-password');
});

test('the audit log records the administrator and request metadata', function () {
    $admin = admin(acting: true);
    Pharmacy::factory()->create();

    $this->post(route('super-admin.pharmacies.store'), [
        'name' => 'Meta Pharmacy',
        'owner_name' => 'Meta Owner',
        'email' => 'meta@pharmacy.test',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);

    $log = AuditLog::first();

    expect($log->user->is($admin))->toBeTrue()
        ->and($log->ip_address)->not->toBeNull()
        ->and($log->created_at)->not->toBeNull();
});

test('an admin can browse the audit log', function () {
    admin(acting: true);

    AuditLog::factory()->create([
        'action' => AuditLog::PHARMACY_CREATED,
        'description' => 'Created the pharmacy Alpha.',
    ]);
    AuditLog::factory()->create([
        'action' => AuditLog::USER_STATUS,
        'description' => 'Disabled the account for Beta.',
    ]);

    $this->get(route('super-admin.audit-logs.index'))
        ->assertSuccessful()
        ->assertSee('Created the pharmacy Alpha.')
        ->assertSee('Disabled the account for Beta.')
        ->assertSee('Pharmacy created')
        ->assertSee('User status changed');
});

test('audit log entries can be filtered by action and search term', function () {
    admin(acting: true);

    AuditLog::factory()->create(['action' => AuditLog::PHARMACY_CREATED, 'description' => 'Created Gamma.']);
    AuditLog::factory()->create(['action' => AuditLog::USER_PASSWORD, 'description' => 'Reset Delta password.']);

    $this->get(route('super-admin.audit-logs.index', ['action' => AuditLog::PHARMACY_CREATED]))
        ->assertSuccessful()
        ->assertSee('Created Gamma.')
        ->assertDontSee('Reset Delta password.');

    $this->get(route('super-admin.audit-logs.index', ['search' => 'Delta']))
        ->assertSuccessful()
        ->assertSee('Reset Delta password.')
        ->assertDontSee('Created Gamma.');
});

test('a pharmacy user is forbidden from the audit log', function () {
    $user = User::factory()->withPharmacy()->create();

    $this->actingAs($user)->get(route('super-admin.audit-logs.index'))->assertForbidden();
});
