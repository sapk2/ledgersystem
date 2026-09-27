<?php

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('an admin signs in through the shared login page and lands on the admin dashboard', function () {
    $admin = admin(['email' => 'admin@example.com']);

    $this->post(route('login'), [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertRedirect(route('super-admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

test('a pharmacy user signs in through the same page and lands on the pharmacy dashboard', function () {
    $user = User::factory()->withPharmacy()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('there is no separate super admin login page', function () {
    $this->get('/super-admin/login')->assertNotFound();
});

test('guests are redirected away from the administration area', function () {
    $this->get(route('super-admin.dashboard'))->assertRedirect(route('login'));
});

test('a pharmacy user is forbidden from the administration area', function () {
    $user = User::factory()->withPharmacy()->create();

    $this->actingAs($user)
        ->get(route('super-admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('super-admin.pharmacies.index'))
        ->assertForbidden();
});

test('a super admin is redirected away from the pharmacy application', function () {
    $admin = admin();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertRedirect(route('super-admin.dashboard'));
});

test('the super admin dashboard counts every pharmacy', function () {
    admin(acting: true);

    Pharmacy::factory()->create(['name' => 'Active One']);
    Pharmacy::factory()->inactive()->create(['name' => 'Inactive One']);

    $this->get(route('super-admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Total Pharmacies')
        ->assertSee('Active One')
        ->assertSee('Inactive One')
        ->assertSee('Inactive');
});

test('a super admin can create a pharmacy together with its login', function () {
    admin(acting: true);

    $this->post(route('super-admin.pharmacies.store'), [
        'name' => 'Gandaki Pharmacy',
        'owner_name' => 'Gandaki Owner',
        'email' => 'gandaki@pharmacy.test',
        'phone' => '9841000003',
        'address' => 'Pokhara-6',
        'city' => 'Pokhara',
        'district' => 'Kaski',
        'province' => 'Gandaki',
        'country' => 'Nepal',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ])->assertRedirect();

    $pharmacy = Pharmacy::where('email', 'gandaki@pharmacy.test')->firstOrFail();

    expect($pharmacy->is_active)->toBeTrue()
        ->and($pharmacy->users)->toHaveCount(1);

    $user = $pharmacy->users->first();

    expect($user->role)->toBe(User::ROLE_USER)
        ->and($user->pharmacy_id)->toBe($pharmacy->id);

    $this->post(route('logout'));

    $this->post(route('login'), [
        'email' => 'gandaki@pharmacy.test',
        'password' => 'secret-password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('creating a pharmacy validates its input', function () {
    admin(acting: true);

    // The tenancy migration backfills a default pharmacy, so compare against the baseline.
    $before = Pharmacy::count();

    $this->post(route('super-admin.pharmacies.store'), [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors(['name', 'email', 'password']);

    expect(Pharmacy::count())->toBe($before);
});

test('a pharmacy email cannot be reused', function () {
    admin(acting: true);
    Pharmacy::factory()->create(['email' => 'taken@pharmacy.test']);

    $this->post(route('super-admin.pharmacies.store'), [
        'name' => 'Copy Cat',
        'owner_name' => 'Copy Cat',
        'email' => 'taken@pharmacy.test',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ])->assertSessionHasErrors('email');
});

test('a super admin can list and search pharmacies', function () {
    admin(acting: true);
    Pharmacy::factory()->create(['name' => 'Kathmandu Medical', 'city' => 'Kathmandu']);
    Pharmacy::factory()->create(['name' => 'Pokhara Pharma', 'city' => 'Pokhara']);

    $this->get(route('super-admin.pharmacies.index'))
        ->assertSuccessful()
        ->assertSee('Kathmandu Medical')
        ->assertSee('Pokhara Pharma');

    $this->get(route('super-admin.pharmacies.index', ['search' => 'Pokhara']))
        ->assertSuccessful()
        ->assertSee('Pokhara Pharma')
        ->assertDontSee('Kathmandu Medical');
});

test('a super admin can edit pharmacy details', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create(['name' => 'Old Name']);

    $this->put(route('super-admin.pharmacies.update', $pharmacy), [
        'name' => 'New Name',
        'owner_name' => 'New Owner',
        'email' => $pharmacy->email,
        'city' => 'Butwal',
    ])->assertRedirect(route('super-admin.pharmacies.show', $pharmacy));

    expect($pharmacy->refresh()->name)->toBe('New Name')
        ->and($pharmacy->city)->toBe('Butwal');
});

test('a super admin can deactivate and reactivate a pharmacy', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create();

    $this->patch(route('super-admin.pharmacies.status', $pharmacy), ['is_active' => false]);

    expect($pharmacy->refresh()->is_active)->toBeFalse();

    $this->patch(route('super-admin.pharmacies.status', $pharmacy), ['is_active' => true]);

    expect($pharmacy->refresh()->is_active)->toBeTrue();
});

test('a deactivated pharmacy can no longer sign in', function () {
    $user = User::factory()->withPharmacy()->create();
    $user->pharmacy->update(['is_active' => false]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('a super admin can reset a pharmacy password', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create();
    $user = $pharmacy->users()->create([
        'name' => 'Owner',
        'email' => 'owner@pharmacy.test',
        'password' => 'old-password',
        'role' => User::ROLE_USER,
    ]);

    $this->put(route('super-admin.pharmacies.password', $pharmacy), [
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('brand-new-password', $user->refresh()->password))->toBeTrue();
});

test('a super admin can see pharmacy details and usage counts', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create(['name' => 'Detail Pharmacy']);
    $pharmacy->users()->create([
        'name' => 'Owner',
        'email' => 'owner@detail.test',
        'password' => 'password',
        'role' => User::ROLE_USER,
    ]);

    $this->get(route('super-admin.pharmacies.show', $pharmacy))
        ->assertSuccessful()
        ->assertSee('Detail Pharmacy')
        ->assertSee('owner@detail.test')
        ->assertSee('Reset password');
});

test('an admin can log out from the administration area', function () {
    $admin = admin();

    $this->actingAs($admin)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
