<?php

use App\Models\Distributor;
use App\Services\LedgerService;
use Carbon\Carbon;

it('computes a running balance from the opening balance', function () {
    $distributor = Distributor::factory()->create(['opening_balance' => 1000]);

    $purchase = $distributor->purchases()->create([
        'bill_no' => 'BL-001', 'bill_date' => '2026-01-01', 'total_amount' => 500,
    ]);
    $purchase->ledgerEntry()->create([
        'distributor_id' => $distributor->id, 'entry_date' => '2026-01-01',
        'voucher_no' => 'BL-001', 'description' => 'Purchase Bill BL-001',
        'type' => 'PURCHASE', 'dr_amount' => 500, 'cr_amount' => 0,
    ]);

    $payment = $distributor->payments()->create([
        'amount' => 300, 'payment_date' => '2026-01-10', 'reference' => 'CHQ-1',
    ]);
    $payment->ledgerEntry()->create([
        'distributor_id' => $distributor->id, 'entry_date' => '2026-01-10',
        'voucher_no' => 'CHQ-1', 'description' => 'Payment - CHQ-1',
        'type' => 'PAYMENT', 'dr_amount' => 0, 'cr_amount' => 300,
    ]);

    $ledger = app(LedgerService::class)->getLedger($distributor);

    expect($ledger['opening_balance'])->toBe('1,000.00')
        ->and($ledger['total_dr'])->toBe('500.00')
        ->and($ledger['total_cr'])->toBe('300.00')
        ->and($ledger['closing_balance'])->toBe('1,200.00');

    $balances = $ledger['rows']->pluck('running_balance')->all();

    expect($balances)->toBe([1500.0, 1200.0]);
});

it('orders ledger entries by date ascending', function () {
    $distributor = Distributor::factory()->create();

    foreach ([['2026-03-01', 100], ['2026-01-01', 200]] as [$date, $amount]) {
        $purchase = $distributor->purchases()->create([
            'bill_no' => 'BL-'.$date, 'bill_date' => $date, 'total_amount' => $amount,
        ]);
        $purchase->ledgerEntry()->create([
            'distributor_id' => $distributor->id, 'entry_date' => $date,
            'voucher_no' => 'BL-'.$date, 'description' => 'Purchase',
            'type' => 'PURCHASE', 'dr_amount' => $amount, 'cr_amount' => 0,
        ]);
    }

    $ledger = app(LedgerService::class)->getLedger($distributor);

    expect($ledger['rows']->pluck('voucher_no')->all())->toBe(['BL-2026-01-01', 'BL-2026-03-01'])
        ->and($ledger['rows']->pluck('running_balance')->all())->toBe([200.0, 300.0]);
});

it('filters the ledger by date range', function () {
    $distributor = Distributor::factory()->create();

    foreach (['2026-01-01' => 100, '2026-05-01' => 200] as $date => $amount) {
        $purchase = $distributor->purchases()->create([
            'bill_no' => 'BL-'.$date, 'bill_date' => $date, 'total_amount' => $amount,
        ]);
        $purchase->ledgerEntry()->create([
            'distributor_id' => $distributor->id, 'entry_date' => $date,
            'voucher_no' => 'BL-'.$date, 'description' => 'Purchase',
            'type' => 'PURCHASE', 'dr_amount' => $amount, 'cr_amount' => 0,
        ]);
    }

    $ledger = app(LedgerService::class)->getLedger($distributor, Carbon::parse('2026-04-01'), Carbon::parse('2026-12-31'));

    expect($ledger['rows'])->toHaveCount(1)
        ->and($ledger['rows']->first()->voucher_no)->toBe('BL-2026-05-01');
});

it('buckets outstanding balances by age using FIFO payments', function () {
    $distributor = Distributor::factory()->create();

    $old = $distributor->purchases()->create([
        'bill_no' => 'BL-OLD', 'bill_date' => '2026-01-01', 'total_amount' => 1000,
    ]);
    $old->ledgerEntry()->create([
        'distributor_id' => $distributor->id, 'entry_date' => '2026-01-01',
        'voucher_no' => 'BL-OLD', 'description' => 'Purchase', 'type' => 'PURCHASE',
        'dr_amount' => 1000, 'cr_amount' => 0,
    ]);

    $recent = $distributor->purchases()->create([
        'bill_no' => 'BL-RECENT', 'bill_date' => '2026-07-20', 'total_amount' => 500,
    ]);
    $recent->ledgerEntry()->create([
        'distributor_id' => $distributor->id, 'entry_date' => '2026-07-20',
        'voucher_no' => 'BL-RECENT', 'description' => 'Purchase', 'type' => 'PURCHASE',
        'dr_amount' => 500, 'cr_amount' => 0,
    ]);

    $payment = $distributor->payments()->create([
        'amount' => 600, 'payment_date' => '2026-07-25', 'reference' => 'CHQ-X',
    ]);
    $payment->ledgerEntry()->create([
        'distributor_id' => $distributor->id, 'entry_date' => '2026-07-25',
        'voucher_no' => 'CHQ-X', 'description' => 'Payment', 'type' => 'PAYMENT',
        'dr_amount' => 0, 'cr_amount' => 600,
    ]);

    $asOf = Carbon::parse('2026-08-01');
    $buckets = app(LedgerService::class)->agingBuckets($distributor, $asOf);

    expect($buckets['90_plus'])->toBe(400.0)   // 1000 - 600 paid off the oldest bill
        ->and($buckets['11_30'])->toBe(500.0)   // recent bill (12 days old) unpaid
        ->and($buckets['total'])->toBe(900.0);
});

it('ages outstanding balances into 0-10, 11-30, 31-60, 61-90 and 90+ day buckets', function () {
    $distributor = Distributor::factory()->create();

    $bills = [
        ['2026-07-28', 100],  // 4 days old  → 0-10
        ['2026-07-10', 200],  // 22 days old → 11-30
        ['2026-06-15', 300],  // 47 days old → 31-60
        ['2026-05-20', 400],  // 73 days old → 61-90
        ['2026-01-05', 500],  // 208 days old → 90+
    ];

    foreach ($bills as [$date, $amount]) {
        $purchase = $distributor->purchases()->create([
            'bill_no' => 'BL-'.$date, 'bill_date' => $date, 'total_amount' => $amount,
        ]);
        $purchase->ledgerEntry()->create([
            'distributor_id' => $distributor->id, 'entry_date' => $date,
            'voucher_no' => 'BL-'.$date, 'description' => 'Purchase', 'type' => 'PURCHASE',
            'dr_amount' => $amount, 'cr_amount' => 0,
        ]);
    }

    $buckets = app(LedgerService::class)->agingBuckets($distributor, Carbon::parse('2026-08-01'));

    expect($buckets['0_10'])->toBe(100.0)
        ->and($buckets['11_30'])->toBe(200.0)
        ->and($buckets['31_60'])->toBe(300.0)
        ->and($buckets['61_90'])->toBe(400.0)
        ->and($buckets['90_plus'])->toBe(500.0)
        ->and($buckets['total'])->toBe(1500.0);
});
