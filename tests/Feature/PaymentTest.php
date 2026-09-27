<?php

use App\Models\Distributor;
use App\Models\DistributorLedger;
use App\Models\Payment;

it('records a payment and creates a credit ledger entry', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('payments.store'), [
            'distributor_id' => $distributor->id,
            'amount' => 750.50,
            'payment_date' => '2026-04-05',
            'reference' => 'CHQ-1234',
        ])
        ->assertRedirect(route('payments.index'));

    $payment = Payment::where('reference', 'CHQ-1234')->firstOrFail();

    expect((float) $payment->amount)->toBe(750.5);

    $entry = DistributorLedger::where('reference_type', Payment::class)
        ->where('reference_id', $payment->id)
        ->firstOrFail();

    expect($entry->type)->toBe('PAYMENT')
        ->and((float) $entry->cr_amount)->toBe(750.5)
        ->and((float) $entry->dr_amount)->toBe(0.0)
        ->and($entry->voucher_no)->toBe('CHQ-1234')
        ->and($entry->entry_date->toDateString())->toBe('2026-04-05');
});

it('validates payment amount is required and positive', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $this->actingAs($user)
        ->post(route('payments.store'), [
            'distributor_id' => $distributor->id,
            'amount' => 0,
            'payment_date' => '2026-04-05',
        ])
        ->assertSessionHasErrors('amount');
});

it('deleting a payment removes its ledger entry', function () {
    $user = pharmacyUser();
    $distributor = Distributor::factory()->create();

    $payment = $distributor->payments()->create([
        'amount' => 500,
        'payment_date' => '2026-03-01',
        'reference' => 'CHQ-999',
    ]);

    $payment->ledgerEntry()->create([
        'distributor_id' => $distributor->id,
        'entry_date' => '2026-03-01',
        'voucher_no' => 'CHQ-999',
        'description' => 'Payment - CHQ-999',
        'type' => 'PAYMENT',
        'dr_amount' => 0,
        'cr_amount' => 500,
    ]);

    $this->actingAs($user)
        ->delete(route('payments.destroy', $payment))
        ->assertRedirect(route('payments.index'));

    expect(Payment::find($payment->id))->toBeNull()
        ->and(DistributorLedger::where('reference_type', Payment::class)->where('reference_id', $payment->id)->count())->toBe(0);
});
