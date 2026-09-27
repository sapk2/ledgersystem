<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoice {{ $purchase->bill_no }}
            </h2>
            <div class="flex items-center gap-3 print:hidden">
                <a href="{{ route('distributors.ledger', $purchase->distributor) }}"
                   class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('View Ledger') }}
                </a>
                <button onclick="window.print()"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Print Invoice') }}
                </button>
            </div>
        </div>
    </x-slot>

    <style>
        @media print {
            body { background: white !important; }
            nav, header, .print\:hidden { display: none !important; }
            .invoice-sheet { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; }
        }
        .invoice-table { border-collapse: collapse; width: 100%; font-size: 13px; }
        .invoice-table th, .invoice-table td {
            border: 1px solid #111;
            padding: 5px 8px;
            text-align: right;
        }
        .invoice-table th { background: #f3f4f6; font-weight: 700; }
        .invoice-table th:nth-child(1),
        .invoice-table th:nth-child(2),
        .invoice-table th:nth-child(3),
        .invoice-table th:nth-child(4) { text-align: left; }
        .invoice-table td:nth-child(1),
        .invoice-table td:nth-child(2),
        .invoice-table td:nth-child(3),
        .invoice-table td:nth-child(4) { text-align: left; }
        .invoice-table td:nth-child(1) { width: 40px; text-align: center; }
        .invoice-table td:nth-child(5) { text-align: center; }
        .invoice-table tfoot td, .invoice-table tfoot th { border-top: 2px solid #111; }
        @media print {
            .invoice-table th { background: #fff !important; }
            .invoice-sheet { font-family: Georgia, 'Times New Roman', serif; }
        }
    </style>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 sm:p-10 invoice-sheet">
                {{-- Supplier / distributor header --}}
                <div class="flex justify-between items-start border-b-4 border-double border-gray-800 pb-6">
                    <div>
                        <h1 class="text-3xl font-bold uppercase tracking-tight text-gray-900">{{ $purchase->distributor->name }}</h1>
                        @if ($purchase->distributor->address)
                            <p class="mt-1 text-sm text-gray-600">{{ $purchase->distributor->address }}</p>
                        @endif
                        @if ($purchase->distributor->city)
                            <p class="text-sm text-gray-600">{{ $purchase->distributor->city }}</p>
                        @endif
                        @if ($purchase->distributor->phone)
                            <p class="text-sm text-gray-600">Contact: {{ $purchase->distributor->phone }}</p>
                        @endif
                        @if ($purchase->distributor->pan_no)
                            <p class="text-sm text-gray-600">PAN No: {{ $purchase->distributor->pan_no }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <h2 class="text-2xl font-bold uppercase tracking-widest text-indigo-700">Invoice</h2>
                        <p class="mt-2 text-sm text-gray-700">
                            <span class="font-semibold">Invoice No:</span> {{ $purchase->bill_no }}<br>
                            <span class="font-semibold">Date:</span> {{ $purchase->bill_date->format('d-m-Y') }}
                        </p>
                    </div>
                </div>

                {{-- Buyer (pharmacy) block --}}
                <div class="mt-6 flex justify-between items-start">
                    <div class="text-sm text-gray-700">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Billed To</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $currentPharmacy?->name ?? config('ledger.pharmacy_name') }}</p>
                        @if ($currentPharmacy)
                            <p>{{ $currentPharmacy->addressLines() ? implode(', ', $currentPharmacy->addressLines()) : $currentPharmacy->address }}</p>
                        @elseif (config('ledger.pharmacy_address'))
                            <p>{{ config('ledger.pharmacy_address') }}</p>
                        @endif
                        @if ($currentPharmacy?->phone)
                            <p>Contact: {{ $currentPharmacy->phone }}</p>
                        @elseif (config('ledger.pharmacy_phone'))
                            <p>Contact: {{ config('ledger.pharmacy_phone') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Items --}}
                <div class="mt-6 overflow-x-auto">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Item Name</th>
                                <th>Batch</th>
                                <th>Expiry</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->medicine_name }}</td>
                                    <td>{{ $item->batch_no ?: '—' }}</td>
                                    <td>{{ $item->expiry_date?->format('m-Y') ?: '—' }}</td>
                                    <td>{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                                    <td>{{ number_format((float) $item->rate, 2) }}</td>
                                    <td>{{ number_format((float) $item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-right">Sub Total</th>
                                <td>{{ number_format($purchase->subtotal(), 2) }}</td>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">Discount</th>
                                <td>{{ number_format((float) $purchase->discount, 2) }}</td>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">VAT ({{ rtrim(rtrim(number_format((float) $purchase->vat, 2), '0'), '.') }}%)</th>
                                <td>{{ number_format($purchase->vatAmount(), 2) }}</td>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">Net Amount ({{ config('ledger.currency') }})</th>
                                <td class="font-bold">{{ number_format((float) $purchase->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Amount in words --}}
                <p class="mt-4 text-sm text-gray-700">
                    <span class="font-semibold">Amount in Words:</span>
                    {{ numberToWords((float) $purchase->total_amount) }}
                </p>

                @if ($purchase->notes)
                    <div class="mt-4 rounded border border-gray-300 p-3 text-sm text-gray-700">
                        <span class="font-semibold">Notes:</span> {{ $purchase->notes }}
                    </div>
                @endif

                <div class="mt-10 flex justify-end">
                    <div class="text-center">
                        <div class="border-t border-gray-800 pt-1 text-sm text-gray-600">Authorised Signature</div>
                    </div>
                </div>

                {{-- Ageing due --}}
                <div class="mt-10 border-t-2 border-dashed border-gray-400 pt-6">
                    <h3 class="text-center text-base font-bold uppercase tracking-wide text-gray-900">Ageing Due — {{ $purchase->distributor->name }}</h3>
                    <table class="invoice-table mt-3">
                        <thead>
                            <tr>
                                <th>Up to 10 days</th>
                                <th>11–30 days</th>
                                <th>31–60 days</th>
                                <th>61–90 days</th>
                                <th>90+ days</th>
                                <th>Total Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($aging['0_10'], 2) }}</td>
                                <td>{{ number_format($aging['11_30'], 2) }}</td>
                                <td>{{ number_format($aging['31_60'], 2) }}</td>
                                <td>{{ number_format($aging['61_90'], 2) }}</td>
                                <td>{{ number_format($aging['90_plus'], 2) }}</td>
                                <td class="font-bold">{{ number_format($aging['total'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="mt-3 text-center text-xs text-gray-500">
                        As of {{ now()->format('d-m-Y') }}. Outstanding balance split by age of bill (oldest bills settled first).
                    </p>
                </div>

                <p class="mt-8 text-center text-xs text-gray-400">
                    This is a computer generated invoice. {{ $currentPharmacy?->name ?? config('ledger.pharmacy_name') }}
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
