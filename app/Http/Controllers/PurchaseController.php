<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Distributor;
use App\Models\Medicine;
use App\Models\Purchase;
use App\Services\LedgerService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $purchases = Purchase::with('distributor')->withCount('items')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q')->trim()->toString();

                $query->where(function ($query) use ($term) {
                    $query->where('bill_no', 'like', "%{$term}%")
                        ->orWhereHas('distributor', fn ($query) => $query->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderByDesc('bill_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('purchases.index', [
            'purchases' => $purchases,
        ]);
    }

    public function create(): View
    {
        return view('purchases.create', [
            'distributors' => Distributor::orderBy('name')->get(),
            'medicines' => Medicine::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $purchase = DB::transaction(function () use ($validated) {
            $distributor = Distributor::findOrFail($validated['distributor_id']);

            $subtotal = collect($validated['items'])->reduce(
                fn (float $carry, array $item) => $carry + (float) $item['quantity'] * (float) $item['rate'],
                0.0
            );
            $discount = (float) ($validated['discount'] ?? 0);
            $vatPercent = (float) ($validated['vat'] ?? 0);
            $vatAmount = round(($subtotal - $discount) * ($vatPercent / 100), 2);
            $total = round($subtotal - $discount + $vatAmount, 2);

            $purchase = Purchase::create([
                'distributor_id' => $distributor->id,
                'bill_no' => $validated['bill_no'],
                'bill_date' => $validated['bill_date'],
                'discount' => $discount,
                'vat' => $vatPercent,
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $medicine = Medicine::firstOrCreate(['name' => trim($item['medicine_name'])]);

                $purchase->items()->create([
                    'medicine_id' => $medicine->id,
                    'medicine_name' => $medicine->name,
                    'batch_no' => $item['batch_no'] ?? null,
                    'expiry_date' => ! empty($item['expiry_date']) ? $item['expiry_date'] : null,
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => round((float) $item['quantity'] * (float) $item['rate'], 2),
                ]);
            }

            $purchase->ledgerEntry()->create([
                'distributor_id' => $purchase->distributor_id,
                'entry_date' => $purchase->bill_date,
                'voucher_no' => $purchase->bill_no,
                'description' => "Purchase Bill {$purchase->bill_no}",
                'type' => 'PURCHASE',
                'dr_amount' => $total,
                'cr_amount' => 0,
            ]);

            return $purchase;
        });

        return redirect()->route('purchases.show', $purchase)
            ->with('status', 'Bill saved and ledger updated.');
    }

    public function show(Purchase $purchase, LedgerService $ledger): View
    {
        $purchase->load('distributor', 'items.medicine');

        abort_if($purchase->distributor === null, 404);

        return view('purchases.show', [
            'purchase' => $purchase,
            'aging' => $ledger->agingBuckets($purchase->distributor, Carbon::today()),
        ]);
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $purchase->ledgerEntry()?->delete();
        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('status', 'Bill deleted.');
    }
}
