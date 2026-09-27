<?php

use App\Models\Pharmacy;
use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->withPharmacy()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->withPharmacy()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can not authenticate when their pharmacy is inactive', function () {
    $user = User::factory()->withPharmacy()->create();
    $user->pharmacy->update(['is_active' => false]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('an admin uses the same login page and is sent to the administration area', function () {
    $user = User::factory()->admin()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('super-admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('users can logout', function () {
    $user = User::factory()->withPharmacy()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('a user without a pharmacy can not sign in', function () {
    $user = User::factory()->create(['pharmacy_id' => null, 'role' => User::ROLE_USER]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
});

test('a pharmacy user is signed out when their pharmacy becomes inactive', function () {
    $user = User::factory()->withPharmacy()->create();
    Pharmacy::withoutEvents(fn () => $user->pharmacy->update(['is_active' => false]));

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
