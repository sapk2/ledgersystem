<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $distributor->name }}
            </h2>
            <div class="flex items-center gap-3 print:hidden">
                <a href="{{ route('purchases.create', ['distributor' => $distributor->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('New Bill') }}
                </a>
                <a href="{{ route('payments.create', ['distributor' => $distributor->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Record Payment') }}
                </a>
                <a href="{{ route('distributors.edit', $distributor) }}"
                   class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Edit') }}
                </a>
                <button onclick="window.print()"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Print Ledger') }}
                </button>
            </div>
        </div>
    </x-slot>

    @php
        $rows = $ledger['rows'];
        $opening = (float) $ledger['opening_balance_float'];
        $closing = (float) $ledger['closing_balance_float'];
        $periodFrom = $from ? \Illuminate\Support\Carbon::parse($from)->format('d-m-Y') : 'Beginning';
        $periodTo = $to ? \Illuminate\Support\Carbon::parse($to)->format('d-m-Y') : 'Today';
    @endphp

    <style>
        .ledger-table { border-collapse: collapse; width: 100%; font-size: 13px; }
        .ledger-table th,
        .ledger-table td {
            border: 1px solid #111827;
            padding: 6px 10px;
            text-align: right;
            vertical-align: top;
        }
        .ledger-table thead th {
            background: #f3f4f6;
            border-bottom: 2px solid #111827;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .ledger-table th:nth-child(-n+3),
        .ledger-table td:nth-child(-n+3) { text-align: left; }
        .ledger-table th:last-child,
        .ledger-table td:last-child { border-left: 2px solid #111827; }
        .ledger-table td:nth-child(1),
        .ledger-table td:nth-child(2),
        .ledger-table td:nth-child(4),
        .ledger-table td:nth-child(5),
        .ledger-table td:nth-child(6) { white-space: nowrap; }
        .ledger-table tbody tr:nth-child(even) { background: #f9fafb; }
        .ledger-table tfoot td { background: #f9fafb; border-top: 2px solid #111827; }
        .ledger-keep { break-inside: avoid; page-break-inside: avoid; }

        @media print {
            @page { size: A4 portrait; margin: 12mm 10mm; }
            body { background: white !important; }
            nav, header, .print\:hidden { display: none !important; }
            .ledger-page { padding: 0 !important; }
            .ledger-page-inner { max-width: none !important; margin: 0 !important; padding: 0 !important; }
            .ledger-sheet { border-radius: 0 !important; box-shadow: none !important; }
            .ledger-sheet-body { padding: 0 !important; }
            .ledger-sheet { font-family: Georgia, 'Times New Roman', serif; font-size: 12px; }
            .ledger-table { font-size: 11.5px; }
            .ledger-table th,
            .ledger-table td { padding: 4px 8px; }
            .ledger-table thead { display: table-header-group; }
            .ledger-table tfoot { display: table-row-group; }
            .ledger-table tr { break-inside: avoid; page-break-inside: avoid; }
            .ledger-table thead th { background: #fff !important; }
            .ledger-table tbody tr:nth-child(even) { background: transparent !important; }
            .ledger-table tfoot td { background: #fff !important; }
            .ledger-header { break-after: avoid; page-break-after: avoid; }
        }
    </style>

    <div class="py-10 ledger-page">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 ledger-page-inner">
            @if (session('status'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-sm text-green-700 rounded-md print:hidden">
                    {{ session('status') }}
                </div>
            @endif

            <div class="print:hidden mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">Ageing Due</h3>
                    <span class="text-xs text-gray-500">outstanding split by age of bill</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div class="rounded-md border border-gray-200 p-3">
                        <p class="text-xs text-gray-500">Up to 10 days</p>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ number_format($aging['0_10'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 p-3">
                        <p class="text-xs text-gray-500">11–30 days</p>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ number_format($aging['11_30'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 p-3">
                        <p class="text-xs text-gray-500">31–60 days</p>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ number_format($aging['31_60'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-amber-200 bg-amber-50 p-3">
                        <p class="text-xs text-amber-600">61–90 days</p>
                        <p class="mt-1 text-lg font-bold text-amber-700">{{ number_format($aging['61_90'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-red-200 bg-red-50 p-3">
                        <p class="text-xs text-red-600">90+ days</p>
                        <p class="mt-1 text-lg font-bold text-red-700">{{ number_format($aging['90_plus'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-gray-900 bg-gray-900 p-3">
                        <p class="text-xs text-gray-300">Total Due</p>
                        <p class="mt-1 text-lg font-bold text-white">{{ number_format($aging['total'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="print:hidden mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('distributors.ledger', $distributor) }}" class="flex flex-wrap items-end gap-3">
                    <div>
                        <x-input-label for="from" value="{{ __('From') }}" />
                        <x-text-input id="from" name="from" type="date" value="{{ $from }}" class="mt-1 block" />
                    </div>
                    <div>
                        <x-input-label for="to" value="{{ __('To') }}" />
                        <x-text-input id="to" name="to" type="date" value="{{ $to }}" class="mt-1 block" />
                    </div>
                    <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    @if ($from || $to)
                        <a href="{{ route('distributors.ledger', $distributor) }}" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('Clear') }}
                        </a>
                    @endif
                    <div class="ms-auto flex gap-6 text-sm">
                        <span class="text-gray-600">Total Dr: <strong class="text-gray-900">{{ $ledger['total_dr'] }}</strong></span>
                        <span class="text-gray-600">Total Cr: <strong class="text-gray-900">{{ $ledger['total_cr'] }}</strong></span>
                        <span class="text-gray-600">Closing Balance: <strong class="{{ $closing > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $ledger['closing_balance'] }}</strong></span>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg ledger-sheet">
                <div class="p-8 sm:p-10 ledger-sheet-body">
                    {{-- Pharmacy letterhead --}}
                    <div class="ledger-header">
                        @include('partials.pharmacy-letterhead')
                    </div>

                    {{-- Distributor and document details --}}
                    <div class="mt-6 ledger-keep flex flex-wrap items-start justify-between gap-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Distributor</p>
                            <p class="mt-1 text-xl font-bold leading-tight text-gray-900">{{ $distributor->name }}</p>
                            <p class="mt-1 text-sm text-gray-700">
                                {{ $distributor->address ? $distributor->address . ', ' : '' }}{{ $distributor->city ?: '—' }}
                            </p>
                            <p class="text-sm text-gray-700">
                                @if ($distributor->phone)
                                    <span>Contact: {{ $distributor->phone }}</span>
                                @endif
                                @if ($distributor->pan_no)
                                    <span class="hidden print:inline"> &nbsp;•&nbsp; PAN No: {{ $distributor->pan_no }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500">Distributor Ledger</p>
                            <p class="mt-1 text-sm text-gray-700">Currency: {{ config('ledger.currency') }}</p>
                            <p class="text-sm text-gray-700">Printed on: {{ now()->format('d-m-Y') }}</p>
                        </div>
                    </div>

                    {{-- Period --}}
                    <div class="mt-5 ledger-keep flex flex-wrap items-center justify-between gap-3 border-y border-gray-300 py-2">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold text-gray-900">Ledger Period:</span>
                            {{ $periodFrom }} to {{ $periodTo }}
                        </p>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold text-gray-900">Opening Balance:</span>
                            {{ number_format($opening, 2) }}
                        </p>
                    </div>

                    {{-- Ledger --}}
                    <div class="mt-5">
                        <table class="ledger-table">
                            <thead>
                                <tr>
                                    <th class="w-24">Date</th>
                                    <th class="w-32">Voucher No</th>
                                    <th>Description</th>
                                    <th class="w-28">Dr (Purchase)</th>
                                    <th class="w-28">Cr (Payment)</th>
                                    <th class="w-28">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($opening != 0)
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>To Balance b/d</td>
                                        <td>{{ number_format($opening, 2) }}</td>
                                        <td></td>
                                        <td class="font-semibold">{{ number_format($opening, 2) }}</td>
                                    </tr>
                                @endif

                                @forelse ($rows as $entry)
                                    <tr>
                                        <td>{{ $entry->entry_date->format('d-m-Y') }}</td>
                                        <td>{{ $entry->voucher_no }}</td>
                                        <td>{{ $entry->description }}</td>
                                        <td>{{ $entry->dr_amount > 0 ? number_format((float) $entry->dr_amount, 2) : '' }}</td>
                                        <td>{{ $entry->cr_amount > 0 ? number_format((float) $entry->cr_amount, 2) : '' }}</td>
                                        <td class="font-semibold">{{ number_format($entry->running_balance, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-gray-500">
                                            No entries {{ ($opening == 0) ? 'yet' : 'in this period' }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right font-bold">Total</td>
                                    <td class="font-bold">{{ $ledger['total_dr'] }}</td>
                                    <td class="font-bold">{{ $ledger['total_cr'] }}</td>
                                    <td class="font-bold {{ $closing > 0 ? 'text-red-700' : '' }}">{{ $ledger['closing_balance'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-semibold">Closing Balance c/d</td>
                                    <td class="font-bold">{{ $ledger['closing_balance'] }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Signatures --}}
                    <div class="mt-12 grid grid-cols-2 gap-8 sm:gap-16 ledger-keep">
                        <div>
                            <div class="h-12"></div>
                            <div class="border-t border-gray-800 pt-1 text-center text-sm font-semibold text-gray-700">Prepared By</div>
                            <div class="text-center text-xs text-gray-500">{{ $currentPharmacy?->name ?? config('ledger.pharmacy_name') }}</div>
                        </div>
                        <div>
                            <div class="h-12"></div>
                            <div class="border-t border-gray-800 pt-1 text-center text-sm font-semibold text-gray-700">Authorised Signatory</div>
                            <div class="text-center text-xs text-gray-500">For {{ $currentPharmacy?->name ?? config('ledger.pharmacy_name') }}</div>
                        </div>
                    </div>

                    <p class="mt-10 border-t border-gray-200 pt-3 text-center text-xs text-gray-400">
                        This is a computer generated ledger statement of {{ $currentPharmacy?->name ?? config('ledger.pharmacy_name') }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
