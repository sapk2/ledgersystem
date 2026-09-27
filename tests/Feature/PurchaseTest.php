<?php

use App\Models\Distributor;
use App\Models\DistributorLedger;
use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\PurchaseItem;

it('creates a purchase bill with items and a ledger entry', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('purchases.store'), [
            'distributor_id' => $distributor->id,
            'bill_no' => 'INV-4471',
            'bill_date' => '2026-03-10',
            'items' => [
                ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 10, 'rate' => 25.50],
                ['medicine_name' => 'Ibuprofen 400mg', 'quantity' => 5, 'rate' => 40.00],
            ],
        ])
        ->assertRedirect();

    $purchase = $distributor->purchases()->firstOrFail();

    expect($purchase->total_amount)->toBe('455.00')
        ->and($purchase->bill_no)->toBe('INV-4471')
        ->and($purchase->items)->toHaveCount(2)
        ->and((float) PurchaseItem::where('purchase_id', $purchase->id)->sum('amount'))->toBe(455.0);

    $entry = DistributorLedger::where('reference_type', Purchase::class)
        ->where('reference_id', $purchase->id)
        ->firstOrFail();

    expect($entry->type)->toBe('PURCHASE')
        ->and((float) $entry->dr_amount)->toBe(455.0)
        ->and((float) $entry->cr_amount)->toBe(0.0)
        ->and($entry->entry_date->toDateString())->toBe('2026-03-10')
        ->and($entry->voucher_no)->toBe($purchase->bill_no);
});

it('stores the bill number entered by the user', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create(['name' => 'Life Line Medicine Distributors']);

    foreach (['LI/2026/0041', 'LI/2026/0042'] as $index => $billNo) {
        $this->actingAs($user)
            ->post(route('purchases.store'), [
                'distributor_id' => $distributor->id,
                'bill_no' => $billNo,
                'bill_date' => '2026-03-1'.$index,
                'items' => [
                    ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 1, 'rate' => 10],
                ],
            ])
            ->assertRedirect();
    }

    expect($distributor->purchases()->pluck('bill_no')->all())->toBe(['LI/2026/0041', 'LI/2026/0042']);
});

it('requires a bill number when storing a purchase bill', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('purchases.store'), [
            'distributor_id' => $distributor->id,
            'bill_no' => '',
            'bill_date' => '2026-03-10',
            'items' => [
                ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 1, 'rate' => 10],
            ],
        ])
        ->assertSessionHasErrors('bill_no');

    expect($distributor->purchases()->count())->toBe(0);
});

it('rejects a duplicate bill number', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $distributor->purchases()->create([
        'bill_no' => 'INV-1000',
        'bill_date' => '2026-02-01',
        'total_amount' => 100,
    ]);

    $this->actingAs($user)
        ->post(route('purchases.store'), [
            'distributor_id' => $distributor->id,
            'bill_no' => 'INV-1000',
            'bill_date' => '2026-03-10',
            'items' => [
                ['medicine_name' => 'Paracetamol 500mg', 'quantity' => 1, 'rate' => 10],
            ],
        ])
        ->assertSessionHasErrors('bill_no');

    expect($distributor->purchases()->count())->toBe(1);
});

it('applies discount and VAT when storing a purchase bill', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('purchases.store'), [
            'distributor_id' => $distributor->id,
            'bill_no' => 'BL-103',
            'bill_date' => '2026-03-10',
            'discount' => 50,
            'vat' => 13,
            'items' => [
                ['medicine_name' => 'Paracetamol 500mg', 'batch_no' => 'BT101', 'expiry_date' => '2027-12-31', 'quantity' => 10, 'rate' => 100],
                ['medicine_name' => 'Ibuprofen 400mg', 'batch_no' => 'BT202', 'expiry_date' => '2028-06-30', 'quantity' => 5, 'rate' => 40],
            ],
        ])
        ->assertRedirect();

    $purchase = $distributor->purchases()->firstOrFail();

    // Subtotal 1200 - 50 discount = 1150, +13% VAT = 1299.50
    expect($purchase->discount)->toBe('50.00')
        ->and($purchase->vat)->toBe('13.00')
        ->and($purchase->total_amount)->toBe('1299.50')
        ->and($purchase->subtotal())->toBe(1200.0)
        ->and($purchase->vatAmount())->toBe(149.5)
        ->and($purchase->netAmount())->toBe(1299.5)
        ->and($purchase->items->first()->batch_no)->toBe('BT101')
        ->and($purchase->items->first()->expiry_date->toDateString())->toBe('2027-12-31');
});

it('auto-creates medicines from bill items', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('purchases.store'), [
            'distributor_id' => $distributor->id,
            'bill_no' => 'BL-101',
            'bill_date' => '2026-03-11',
            'items' => [
                ['medicine_name' => 'Metformin 500mg', 'quantity' => 3, 'rate' => 50.00],
            ],
        ])
        ->assertRedirect();

    expect(Medicine::where('name', 'Metformin 500mg')->exists())->toBeTrue();
});

it('validates that a purchase requires at least one item', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('purchases.store'), [
            'distributor_id' => $distributor->id,
            'bill_no' => 'BL-102',
            'bill_date' => '2026-03-12',
            'items' => [],
        ])
        ->assertSessionHasErrors('items');
});

it('deleting a purchase removes its ledger entry', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $purchase = $distributor->purchases()->create([
        'bill_no' => 'BL-200',
        'bill_date' => '2026-02-01',
        'total_amount' => 100,
    ]);

    $purchase->ledgerEntry()->create([
        'distributor_id' => $distributor->id,
        'entry_date' => '2026-02-01',
        'voucher_no' => 'BL-200',
        'description' => 'Purchase Bill BL-200',
        'type' => 'PURCHASE',
        'dr_amount' => 100,
        'cr_amount' => 0,
    ]);

    $this->actingAs($user)
        ->delete(route('purchases.destroy', $purchase))
        ->assertRedirect(route('purchases.index'));

    expect(Purchase::find($purchase->id))->toBeNull()
        ->and(DistributorLedger::where('reference_type', Purchase::class)->where('reference_id', $purchase->id)->count())->toBe(0);
});
