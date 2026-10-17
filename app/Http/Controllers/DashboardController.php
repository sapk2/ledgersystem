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
        $balances = [];
        $dueByDistributor = [];

        foreach ($distributors as $distributor) {
            $totals = $ledgerTotals->get($distributor->id);
            $balance = round(
                (float) $distributor->opening_balance
                + (float) ($totals?->total_dr ?? 0)
                - (float) ($totals?->total_cr ?? 0),
                2
            );

            $balances[$distributor->id] = $balance;

            if ($balance <= 0) {
                continue;
            }

            $outstanding += $balance;

            $dueByDistributor[] = [
                'id' => $distributor->id,
                'name' => $distributor->name,
                'amount' => $balance,
            ];

            foreach (array_intersect_key($ledger->agingBuckets($distributor, $today), $aging) as $key => $value) {
                $aging[$key] += $value;
            }
        }

        // Pie chart data: the largest outstanding dues, with the tail grouped so
        // the chart stays readable no matter how many distributors there are.
        $dueByDistributor = collect($dueByDistributor)->sortByDesc('amount')->values();
        $dueDistribution = $dueByDistributor->take(7);
        $otherDues = (float) $dueByDistributor->slice(7)->sum('amount');

        if ($otherDues > 0) {
            $dueDistribution->push([
                'id' => null,
                'name' => 'Other distributors',
                'amount' => round($otherDues, 2),
                'others' => true,
            ]);
        }

        return view('dashboard', [
            'distributorCount' => $distributors->count(),
            'purchaseTotal' => (float) Purchase::sum('total_amount'),
            'purchaseCount' => Purchase::count(),
            'paymentTotal' => (float) Payment::sum('amount'),
            'outstanding' => round($outstanding, 2),
            'aging' => array_map(fn (float $value) => round($value, 2), $aging),
            'dueDistribution' => $dueDistribution,
            'recentPurchases' => Purchase::with('distributor')->latest()->limit(6)->get(),
            'searchDistributors' => $distributors->map(fn ($distributor) => [
                'id' => $distributor->id,
                'name' => $distributor->name,
                'city' => $distributor->city,
                'phone' => $distributor->phone,
                'pan_no' => $distributor->pan_no,
                'balance' => $balances[$distributor->id],
                'due' => max($balances[$distributor->id], 0),
                'search' => mb_strtolower(implode(' ', array_filter([
                    $distributor->name,
                    $distributor->city,
                    $distributor->address,
                    $distributor->phone,
                    $distributor->pan_no,
                ]))),
                'summary_url' => route('distributors.ledger-summary', $distributor),
                'ledger_url' => route('distributors.ledger', $distributor),
            ])->values(),
            'topDistributors' => $distributors
                ->map(fn ($distributor) => [
                    'id' => $distributor->id,
                    'name' => $distributor->name,
                    'balance' => $balances[$distributor->id],
                ])
                ->sortByDesc('balance')
                ->take(5),
        ]);
    }
}
