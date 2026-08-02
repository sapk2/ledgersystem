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
    @endphp

    <style>
        @media print {
            body { background: white !important; }
            nav, header, .print\:hidden { display: none !important; }
            .ledger-sheet { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; }
        }
        .ledger-table { border-collapse: collapse; width: 100%; font-size: 13px; }
        .ledger-table th, .ledger-table td {
            border: 1px solid #111;
            padding: 5px 8px;
            text-align: right;
        }
        .ledger-table th { background: #f3f4f6; font-weight: 700; }
        .ledger-table th:nth-child(1),
        .ledger-table th:nth-child(2),
        .ledger-table th:nth-child(3) { text-align: left; }
        .ledger-table td:nth-child(1),
        .ledger-table td:nth-child(2),
        .ledger-table td:nth-child(3) { text-align: left; }
        @media print {
            .ledger-table th { background: #fff !important; }
            .ledger-sheet { font-family: Georgia, 'Times New Roman', serif; }
        }
    </style>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                <div class="p-8 sm:p-10">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold uppercase tracking-wide text-gray-900">Distributor Ledger</h1>
                        <p class="mt-1 text-lg font-semibold text-gray-800">{{ $distributor->name }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $distributor->address ? $distributor->address . ', ' : '' }}{{ $distributor->city }}
                            {{ $distributor->phone ? ' • ' . $distributor->phone : '' }}
                        </p>
                        <p class="mt-1 text-sm text-gray-600">
                            Period: {{ $from ?: 'Beginning' }} — {{ $to ?: 'Today' }}
                        </p>
                    </div>

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
                                    <td>{{ number_format($opening, 2) }}</td>
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
                                    <td colspan="6" class="text-center text-gray-500 py-6">
                                        No entries {{ ($opening == 0) ? 'yet' : 'in this period' }}.
                                    </td>
                                </tr>
                            @endforelse

                            <tr>
                                <td colspan="3" class="font-bold">Total</td>
                                <td class="font-bold">{{ $ledger['total_dr'] }}</td>
                                <td class="font-bold">{{ $ledger['total_cr'] }}</td>
                                <td class="font-bold {{ $closing > 0 ? 'text-red-700' : '' }}">{{ $ledger['closing_balance'] }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="font-semibold">Closing Balance c/d</td>
                                <td class="font-bold">{{ $ledger['closing_balance'] }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-8 grid grid-cols-2 gap-8">
                        <div class="text-center">
                            <div class="border-t border-gray-800 pt-1 text-sm text-gray-600">Prepared By</div>
                        </div>
                        <div class="text-center">
                            <div class="border-t border-gray-800 pt-1 text-sm text-gray-600">Authorised Signatory</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
