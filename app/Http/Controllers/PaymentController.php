<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Distributor;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::with('distributor')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q')->trim()->toString();

                $query->where(function ($query) use ($term) {
                    $query->where('reference', 'like', "%{$term}%")
                        ->orWhereHas('distributor', fn ($query) => $query->where('name', 'like', "%{$term}%"));
                });
            })
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('payments.index', [
            'payments' => $payments,
        ]);
    }

    public function create(): View
    {
        return view('payments.create', [
            'distributors' => Distributor::orderBy('name')->get(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $payment = DB::transaction(function () use ($validated) {
            $payment = Payment::create([
                'distributor_id' => $validated['distributor_id'],
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'reference' => $validated['reference'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $payment->ledgerEntry()->create([
                'distributor_id' => $payment->distributor_id,
                'entry_date' => $payment->payment_date,
                'voucher_no' => $payment->reference ?? "PMT-{$payment->id}",
                'description' => $payment->reference
                    ? "Payment - {$payment->reference}"
                    : 'Payment received',
                'type' => 'PAYMENT',
                'dr_amount' => 0,
                'cr_amount' => $payment->amount,
            ]);

            return $payment;
        });

        return redirect()->route('payments.index')
            ->with('status', 'Payment recorded and ledger updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->ledgerEntry()?->delete();
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('status', 'Payment deleted.');
    }
}
