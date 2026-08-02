<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\DistributorLedger;
use App\Models\Payment;
use App\Models\Purchase;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(LedgerService $ledger): View
    {
        $today = Carbon::today();

        $distributors = Distributor::withCount('purchases')->orderBy('name')->get();

        $ledgerTotals = DistributorLedger::query()
            ->select('distributor_id')
            ->selectRaw('SUM(dr_amount) as total_dr')
            ->selectRaw('SUM(cr_amount) as total_cr')
            ->groupBy('distributor_id')
            ->get()
            ->keyBy('distributor_id');

        $outstanding = 0.0;
        $aging = ['0_10' => 0.0, '11_30' => 0.0, '31_60' => 0.0, '61_90' => 0.0, '90_plus' => 0.0];

        foreach ($distributors as $distributor) {
            $totals = $ledgerTotals->get($distributor->id);
            $balance = (float) $distributor->opening_balance
                + (float) ($totals?->total_dr ?? 0)
                - (float) ($totals?->total_cr ?? 0);

            if ($balance <= 0) {
                continue;
            }

            $outstanding += $balance;

            foreach (array_intersect_key($ledger->agingBuckets($distributor, $today), $aging) as $key => $value) {
                $aging[$key] += $value;
            }
        }

        return view('dashboard', [
            'distributorCount' => $distributors->count(),
            'purchaseTotal' => (float) Purchase::sum('total_amount'),
            'purchaseCount' => Purchase::count(),
            'paymentTotal' => (float) Payment::sum('amount'),
            'outstanding' => round($outstanding, 2),
            'aging' => array_map(fn (float $value) => round($value, 2), $aging),
            'recentPurchases' => Purchase::with('distributor')->latest()->limit(6)->get(),
            'searchDistributors' => $distributors->map(fn ($distributor) => [
                'id' => $distributor->id,
                'name' => $distributor->name,
                'city' => $distributor->city,
                'summary_url' => route('distributors.ledger-summary', $distributor),
            ])->values(),
            'topDistributors' => $distributors
                ->map(fn ($distributor) => [
                    'id' => $distributor->id,
                    'name' => $distributor->name,
                    'balance' => (float) $distributor->opening_balance
                        + (float) ($ledgerTotals->get($distributor->id)?->total_dr ?? 0)
                        - (float) ($ledgerTotals->get($distributor->id)?->total_cr ?? 0),
                ])
                ->sortByDesc('balance')
                ->take(5),
        ]);
    }
}
