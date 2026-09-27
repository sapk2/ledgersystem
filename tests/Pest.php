<?php

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * A signed-in pharmacy user. Authenticated up front so records created by the
 * test are attached to the user's pharmacy by the tenancy model events.
 *
 * @param  array<string, mixed>  $attributes
 */
function pharmacyUser(array $attributes = []): User
{
    $user = User::factory()->withPharmacy()->create($attributes);

    test()->actingAs($user);

    return $user;
}

/**
 * A platform admin, optionally signed in.
 *
 * @param  array<string, mixed>  $attributes
 */
function admin(array $attributes = [], bool $acting = false): User
{
    $user = User::factory()->admin()->create($attributes);

    if ($acting) {
        test()->actingAs($user);
    }

    return $user;
}

/**
 * A second, unrelated pharmacy used for isolation assertions.
 *
 * @param  array<string, mixed>  $attributes
 */
function otherPharmacy(array $attributes = []): Pharmacy
{
    return Pharmacy::factory()->create($attributes);
}
