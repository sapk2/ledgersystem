<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use App\Models\Distributor;
use App\Models\DistributorLedger;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistributorController extends Controller
{
    /**
     * A–Z indexed list of distributors (search and letter filter are client-side).
     */
    public function index(Request $request): View
    {
        $term = $request->string('q')->trim()->toString();
        $letter = $request->string('letter')->upper()->toString();

        $distributors = Distributor::with('purchases', 'payments')
            ->orderBy('name')
            ->get();

        $ledgerTotals = DistributorLedger::query()
            ->select('distributor_id')
            ->selectRaw('SUM(dr_amount) as total_dr')
            ->selectRaw('SUM(cr_amount) as total_cr')
            ->groupBy('distributor_id')
            ->get()
            ->keyBy('distributor_id');

        $withBalance = $distributors->map(function (Distributor $distributor) use ($ledgerTotals) {
            $distributor->balance = (float) $distributor->opening_balance
                + (float) ($ledgerTotals->get($distributor->id)?->total_dr ?? 0)
                - (float) ($ledgerTotals->get($distributor->id)?->total_cr ?? 0);

            return $distributor;
        });

        return view('distributors.index', [
            'activeLetter' => $letter,
            'term' => $term,
            'searchList' => $withBalance->map(function (Distributor $distributor) {
                return [
                    'name' => $distributor->name,
                    'city' => $distributor->city,
                    'phone' => $distributor->phone,
                    'balance' => (float) $distributor->balance,
                    'letter' => strtoupper(substr($distributor->name, 0, 1)),
                    'ledger_url' => route('distributors.ledger', $distributor),
                ];
            })->values(),
        ]);
    }

    public function create(): View
    {
        return view('distributors.create');
    }

    public function store(StoreDistributorRequest $request): RedirectResponse
    {
        $distributor = Distributor::create($request->validated());

        return redirect()->route('distributors.ledger', $distributor)
            ->with('status', 'Distributor created.');
    }

    /**
     * Distributor ledger page (paper format).
     */
    public function show(Request $request, Distributor $distributor, LedgerService $ledger): View
    {
        $from = $request->filled('from') ? Carbon::parse($request->string('from')) : null;
        $to = $request->filled('to') ? Carbon::parse($request->string('to')) : null;

        return view('distributors.ledger', [
            'distributor' => $distributor,
            'ledger' => $ledger->getLedger($distributor, $from, $to),
            'aging' => $ledger->agingBuckets($distributor, Carbon::today()),
            'from' => $from?->toDateString(),
            'to' => $to?->toDateString(),
        ]);
    }

    /**
     * Compact ledger summary fragment, rendered into the dashboard search panel.
     */
    public function ledgerSummary(Distributor $distributor, LedgerService $ledger): View
    {
        return view('distributors.ledger-summary', [
            'distributor' => $distributor,
            'ledger' => $ledger->getLedger($distributor),
            'aging' => $ledger->agingBuckets($distributor, Carbon::today()),
        ]);
    }

    public function edit(Distributor $distributor): View
    {
        return view('distributors.edit', ['distributor' => $distributor]);
    }

    public function update(UpdateDistributorRequest $request, Distributor $distributor): RedirectResponse
    {
        $distributor->update($request->validated());

        return redirect()->route('distributors.ledger', $distributor)
            ->with('status', 'Distributor updated.');
    }

    public function destroy(Distributor $distributor): RedirectResponse
    {
        $distributor->delete();

        return redirect()->route('distributors.index')
            ->with('status', 'Distributor deleted.');
    }
}
