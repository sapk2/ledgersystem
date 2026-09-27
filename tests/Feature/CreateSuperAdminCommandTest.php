<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the super admin account can be provisioned from the console', function () {
    $this->artisan('super-admin:create', [
        '--email' => 'owner@example.com',
        '--password' => 'a-strong-password',
        '--name' => 'Platform Owner',
    ])->assertSuccessful();

    $admin = User::where('email', 'owner@example.com')->firstOrFail();

    expect($admin->isadmin())->toBeTrue()
        ->and($admin->pharmacy_id)->toBeNull()
        ->and($admin->email_verified_at)->not->toBeNull()
        ->and(Hash::check('a-strong-password', $admin->password))->toBeTrue();
});

test('provisioning is idempotent and never attaches a pharmacy', function () {
    $this->artisan('super-admin:create', ['--email' => 'owner@example.com', '--password' => 'first-password'])->assertSuccessful();
    $this->artisan('super-admin:create', ['--email' => 'owner@example.com', '--password' => 'second-password'])->assertSuccessful();

    expect(User::where('email', 'owner@example.com')->count())->toBe(1);

    $admin = User::where('email', 'owner@example.com')->firstOrFail();

    expect(Hash::check('second-password', $admin->password))->toBeTrue();
});

test('provisioning rejects weak or invalid credentials', function () {
    $this->artisan('super-admin:create', ['--email' => 'nope', '--password' => 'short'])
        ->assertFailed();

    expect(User::count())->toBe(0);
});

test('provisioning promotes an existing pharmacy user and detaches it', function () {
    $user = pharmacyUser();

    $this->artisan('super-admin:create', ['--email' => $user->email, '--password' => 'a-strong-password'])
        ->assertSuccessful();

    $user->refresh();

    expect($user->isadmin())->toBeTrue()
        ->and($user->pharmacy_id)->toBeNull();
});
