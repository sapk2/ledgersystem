<?php

use App\Models\Distributor;
use App\Models\User;

it('searches distributors by name', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);
    Distributor::factory()->create(['name' => 'City Drug House']);

    $this->getJson(route('distributors.search', ['q' => 'Apex']))
        ->assertSuccessful()
        ->assertJsonPath('q', 'Apex')
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Apex Medicine Center');
});

it('searches distributors by city', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center', 'city' => 'Lalitpur']);
    Distributor::factory()->create(['name' => 'City Drug House', 'city' => 'Kathmandu']);

    $this->getJson(route('distributors.search', ['q' => 'Lalitpur']))
        ->assertSuccessful()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Apex Medicine Center');
});

it('searches distributors by address', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center', 'address' => 'Pulchowk Road']);
    Distributor::factory()->create(['name' => 'City Drug House', 'address' => 'New Road']);

    $this->getJson(route('distributors.search', ['q' => 'Pulchowk']))
        ->assertSuccessful()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Apex Medicine Center');
});

it('searches distributors by phone', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center', 'phone' => '9851023456']);
    Distributor::factory()->create(['name' => 'City Drug House', 'phone' => '9800000000']);

    $this->getJson(route('distributors.search', ['q' => '9851023456']))
        ->assertSuccessful()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Apex Medicine Center');
});

it('searches distributors by pan number', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center', 'pan_no' => '504567890']);
    Distributor::factory()->create(['name' => 'City Drug House', 'pan_no' => '600000000']);

    $this->getJson(route('distributors.search', ['q' => '504567890']))
        ->assertSuccessful()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Apex Medicine Center');
});

it('returns an empty result set when nothing matches', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $this->getJson(route('distributors.search', ['q' => 'zzzz-no-such-distributor']))
        ->assertSuccessful()
        ->assertJsonCount(0, 'results');
});

it('returns an empty result set for a blank query', function () {
    pharmacyUser();
    Distributor::factory()->count(3)->create();

    $this->getJson(route('distributors.search', ['q' => '   ']))
        ->assertSuccessful()
        ->assertJsonCount(0, 'results');
});

it('includes the ledger and summary urls along with the outstanding due', function () {
    pharmacyUser();
    $distributor = Distributor::factory()->create([
        'name' => 'Apex Medicine Center',
        'opening_balance' => 1500,
    ]);

    $this->getJson(route('distributors.search', ['q' => 'Apex']))
        ->assertSuccessful()
        ->assertJsonPath('results.0.id', $distributor->id)
        ->assertJsonPath('results.0.due', 1500)
        ->assertJsonPath('results.0.ledger_url', route('distributors.ledger', $distributor))
        ->assertJsonPath('results.0.summary_url', route('distributors.ledger-summary', $distributor));
});

it('orders name matches before other field matches', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Zeta Traders', 'city' => 'Apex Nagar']);
    Distributor::factory()->create(['name' => 'Apex Medicine Center', 'city' => 'Kathmandu']);

    $this->getJson(route('distributors.search', ['q' => 'Apex']))
        ->assertSuccessful()
        ->assertJsonPath('results.0.name', 'Apex Medicine Center')
        ->assertJsonPath('results.1.name', 'Zeta Traders');
});

it('caps the result set at ten distributors', function () {
    pharmacyUser();
    Distributor::factory()->count(15)->create(['name' => 'Apex Distributor']);

    $this->getJson(route('distributors.search', ['q' => 'Apex']))
        ->assertSuccessful()
        ->assertJsonCount(10, 'results');
});

it('does not leak distributors from another pharmacy', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $other = otherPharmacy();
    Distributor::factory()->create([
        'pharmacy_id' => $other->id,
        'name' => 'Apex Secret Distributor',
    ]);

    $this->getJson(route('distributors.search', ['q' => 'Apex']))
        ->assertSuccessful()
        ->assertJsonCount(1, 'results')
        ->assertJsonPath('results.0.name', 'Apex Medicine Center');
});

it('is protected against sql injection in the search term', function () {
    pharmacyUser();
    Distributor::factory()->create(['name' => 'Apex Medicine Center']);

    $this->getJson(route('distributors.search', ['q' => "' OR 1=1 --"]))
        ->assertSuccessful()
        ->assertJsonCount(0, 'results');

    $this->getJson(route('distributors.search', ['q' => "%' UNION SELECT * FROM users --"]))
        ->assertSuccessful()
        ->assertJsonCount(0, 'results');

    expect(Distributor::count())->toBe(1);
});

it('requires authentication', function () {
    $this->get(route('distributors.search', ['q' => 'Apex']))
        ->assertRedirect(route('login'));
});

it('sends a platform admin to the administration area', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('distributors.search', ['q' => 'Apex']))
        ->assertRedirect(route('super-admin.dashboard'));
});
