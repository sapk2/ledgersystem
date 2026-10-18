<?php

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('an admin can list and search every user account', function () {
    admin(acting: true);

    $pharmacy = Pharmacy::factory()->create(['name' => 'Sunrise Pharmacy']);
    $pharmacy->users()->create([
        'name' => 'Alice Sharma',
        'email' => 'alice@sunrise.test',
        'password' => 'password',
        'role' => User::ROLE_USER,
    ]);
    User::factory()->create(['name' => 'Bob Thapa', 'email' => 'bob@example.test']);

    $this->get(route('super-admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Alice Sharma')
        ->assertSee('alice@sunrise.test')
        ->assertSee('Bob Thapa')
        ->assertSee('Sunrise Pharmacy');

    $this->get(route('super-admin.users.index', ['search' => 'Alice']))
        ->assertSuccessful()
        ->assertSee('Alice Sharma')
        ->assertDontSee('Bob Thapa');

    $this->get(route('super-admin.users.index', ['search' => 'Sunrise']))
        ->assertSuccessful()
        ->assertSee('Alice Sharma')
        ->assertDontSee('Bob Thapa');
});

test('an admin can filter users by role and status', function () {
    admin(acting: true);

    User::factory()->create(['name' => 'Regular Active']);
    User::factory()->inactive()->create(['name' => 'Regular Disabled']);
    User::factory()->admin()->create(['name' => 'Root Admin']);

    $this->get(route('super-admin.users.index', ['role' => 'user']))
        ->assertSuccessful()
        ->assertSee('Regular Active')
        ->assertDontSee('Root Admin');

    $this->get(route('super-admin.users.index', ['status' => 'inactive']))
        ->assertSuccessful()
        ->assertSee('Regular Disabled')
        ->assertDontSee('Regular Active');
});

test('an admin can update a user account and move it to another pharmacy', function () {
    admin(acting: true);
    $pharmacy = Pharmacy::factory()->create();
    $user = User::factory()->withPharmacy()->create(['name' => 'Old Name']);

    $this->put(route('super-admin.users.update', $user), [
        'name' => 'New Name',
        'email' => 'new@example.test',
        'role' => User::ROLE_USER,
        'pharmacy_id' => $pharmacy->id,
        'is_active' => 1,
    ])->assertRedirect(route('super-admin.users.index'));

    $user->refresh();

    expect($user->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.test')
        ->and($user->pharmacy_id)->toBe($pharmacy->id)
        ->and($user->is_active)->toBeTrue();
});

test('a pharmacy user must be assigned to a pharmacy', function () {
    admin(acting: true);
    $user = User::factory()->withPharmacy()->create();

    $this->put(route('super-admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'role' => User::ROLE_USER,
        'pharmacy_id' => '',
        'is_active' => 1,
    ])->assertSessionHasErrors('pharmacy_id');
});

test('a user email cannot be reused', function () {
    admin(acting: true);
    User::factory()->create(['email' => 'taken@example.test']);
    $user = User::factory()->withPharmacy()->create();

    $this->put(route('super-admin.users.update', $user), [
        'name' => $user->name,
        'email' => 'taken@example.test',
        'role' => User::ROLE_USER,
        'pharmacy_id' => $user->pharmacy_id,
        'is_active' => 1,
    ])->assertSessionHasErrors('email');
});

test('an admin can disable and enable a user account', function () {
    admin(acting: true);
    $user = User::factory()->withPharmacy()->create();

    $this->patch(route('super-admin.users.status', $user), ['is_active' => false])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->is_active)->toBeFalse();

    $this->patch(route('super-admin.users.status', $user), ['is_active' => true])
        ->assertSessionHasNoErrors();

    expect($user->refresh()->is_active)->toBeTrue();
});

test('a disabled user can no longer sign in', function () {
    $user = User::factory()->withPharmacy()->inactive()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an admin cannot change the status of their own account', function () {
    $admin = admin(acting: true);

    $this->patch(route('super-admin.users.status', $admin), ['is_active' => false])
        ->assertSessionHasErrors('is_active');

    expect($admin->refresh()->is_active)->toBeTrue();
});

test('an admin cannot change their own role', function () {
    $admin = admin(acting: true);

    $this->put(route('super-admin.users.update', $admin), [
        'name' => $admin->name,
        'email' => $admin->email,
        'role' => User::ROLE_USER,
        'pharmacy_id' => Pharmacy::factory()->create()->id,
    ])->assertRedirect(route('super-admin.users.index'));

    expect($admin->refresh()->role)->toBe(User::ROLE_ADMIN);
});

test('the last active administrator cannot be disabled', function () {
    $admin = admin(acting: true);
    $other = admin();

    // Simulate the acting admin already being disabled so only one active admin remains.
    $admin->forceFill(['is_active' => false])->save();

    $this->patch(route('super-admin.users.status', $other), ['is_active' => false])
        ->assertSessionHasErrors('is_active');

    expect($other->refresh()->is_active)->toBeTrue();
});

test('an admin can reset a user password', function () {
    admin(acting: true);
    $user = User::factory()->withPharmacy()->create(['password' => 'old-password']);

    $this->put(route('super-admin.users.password', $user), [
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('brand-new-password', $user->refresh()->password))->toBeTrue();
});

test('a pharmacy user is forbidden from user management', function () {
    $user = User::factory()->withPharmacy()->create();
    $target = User::factory()->withPharmacy()->create();

    $this->actingAs($user)->get(route('super-admin.users.index'))->assertForbidden();
    $this->actingAs($user)->get(route('super-admin.users.edit', $target))->assertForbidden();
    $this->actingAs($user)->put(route('super-admin.users.update', $target), [])->assertForbidden();
    $this->actingAs($user)->patch(route('super-admin.users.status', $target), ['is_active' => false])->assertForbidden();
    $this->actingAs($user)->put(route('super-admin.users.password', $target), [])->assertForbidden();
});
