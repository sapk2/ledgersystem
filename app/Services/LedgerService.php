<?php

namespace App\Services;

use App\Models\Distributor;
use App\Models\DistributorLedger;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LedgerService
{
    /**
     * Build a distributor's ledger with running balances.
     *
     * @return array{
     *     opening_balance: string,
     *     opening_balance_float: float,
     *     rows: Collection,
     *     total_dr: string,
     *     total_cr: string,
     *     closing_balance: string,
     *     closing_balance_float: float,
     *     balance_due: string,
     * }
     */
    public function getLedger(Distributor $distributor, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $query = $distributor->ledgerEntries();

        if ($from) {
            $query->where('entry_date', '>=', $from->toDateString());
        }

        if ($to) {
            $query->where('entry_date', '<=', $to->toDateString());
        }

        /** @var Collection<int, DistributorLedger> $entries */
        $entries = $query->orderBy('entry_date')->orderBy('id')->get();

        $openingBalance = (float) $distributor->opening_balance;
        $balance = $openingBalance;
        $totalDr = 0.0;
        $totalCr = 0.0;

        $rows = $entries->map(function ($entry) use (&$balance, &$totalDr, &$totalCr) {
            $dr = (float) $entry->dr_amount;
            $cr = (float) $entry->cr_amount;

            $balance = round($balance + $dr - $cr, 2);
            $totalDr = round($totalDr + $dr, 2);
            $totalCr = round($totalCr + $cr, 2);

            $entry->running_balance = $balance;

            return $entry;
        });

        return [
            'opening_balance' => number_format($openingBalance, 2),
            'opening_balance_float' => round($openingBalance, 2),
            'rows' => $rows,
            'total_dr' => number_format($totalDr, 2),
            'total_cr' => number_format($totalCr, 2),
            'closing_balance' => number_format($balance, 2),
            'closing_balance_float' => $balance,
            'balance_due' => number_format(max($balance, 0), 2),
        ];
    }

    /**
     * Age a distributor's outstanding balance into 0–10, 11–30, 31–60, 61–90 and 90+ day buckets.
     *
     * Payments are applied to the oldest purchase first (FIFO).
     *
     * @return array{0_10: float, 11_30: float, 31_60: float, 61_90: float, 90_plus: float, total: float}
     */
    public function agingBuckets(Distributor $distributor, Carbon $asOf): array
    {
        $buckets = ['0_10' => 0.0, '11_30' => 0.0, '31_60' => 0.0, '61_90' => 0.0, '90_plus' => 0.0];

        $closing = $this->getLedger($distributor)['rows']->reduce(
            fn (float $carry, $entry) => round($carry + (float) $entry->dr_amount - (float) $entry->cr_amount, 2),
            (float) $distributor->opening_balance
        );

        if ($closing <= 0) {
            return [...$buckets, 'total' => 0.0];
        }

        $purchases = $distributor->purchases()
            ->orderBy('bill_date')->orderBy('id')
            ->get(['id', 'bill_date', 'total_amount']);

        $remaining = $purchases->mapWithKeys(fn ($purchase) => [$purchase->id => (float) $purchase->total_amount]);

        $distributor->payments()
            ->orderBy('payment_date')->orderBy('id')
            ->get(['payment_date', 'amount'])
            ->each(function ($payment) use (&$remaining, $purchases) {
                $amount = (float) $payment->amount;

                foreach ($purchases as $purchase) {
                    if ($amount <= 0) {
                        break;
                    }

                    if ($remaining[$purchase->id] > 0) {
                        $applied = min($amount, $remaining[$purchase->id]);
                        $remaining[$purchase->id] -= $applied;
                        $amount -= $applied;
                    }
                }
            });

        $allocated = 0.0;

        foreach ($purchases as $purchase) {
            $outstanding = min($remaining[$purchase->id], $closing);
            if ($outstanding <= 0) {
                continue;
            }

            $ageDays = $purchase->bill_date->diffInDays($asOf);
            $bucket = match (true) {
                $ageDays <= 10 => '0_10',
                $ageDays <= 30 => '11_30',
                $ageDays <= 60 => '31_60',
                $ageDays <= 90 => '61_90',
                default => '90_plus',
            };

            $buckets[$bucket] += $outstanding;
            $allocated += $outstanding;
        }

        if ($allocated < $closing) {
            $buckets['90_plus'] += $closing - $allocated;
            $allocated = $closing;
        }

        return [...$buckets, 'total' => round($allocated, 2)];
    }

    /**
     * Balance carried from all entries before a given date, plus opening balance.
     */
    public function balanceBefore(Distributor $distributor, Carbon $date): float
    {
        $entries = $distributor->ledgerEntries()
            ->where('entry_date', '<', $date->toDateString())
            ->get();

        $balance = (float) $distributor->opening_balance;

        foreach ($entries as $entry) {
            $balance += (float) $entry->dr_amount - (float) $entry->cr_amount;
        }

        return round($balance, 2);
    }
}
