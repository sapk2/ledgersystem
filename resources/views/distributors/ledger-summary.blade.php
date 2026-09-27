<div class="rounded-lg border border-indigo-200 bg-indigo-50/40 p-5 space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h4 class="text-lg font-bold text-gray-900">{{ $distributor->name }}</h4>
            <p class="mt-0.5 text-sm text-gray-600">
                @if ($distributor->address)
                    {{ $distributor->address }},
                @endif
                {{ $distributor->city ?: '—' }}
                @if ($distributor->phone)
                    • {{ $distributor->phone }}
                @endif
                @if ($distributor->pan_no)
                    • PAN: {{ $distributor->pan_no }}
                @endif
            </p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="rounded-md border border-gray-200 bg-white p-3">
            <p class="text-xs text-gray-500">Total Purchases (Dr)</p>
            <p class="mt-1 text-lg font-bold text-gray-900">{{ $ledger['total_dr'] }}</p>
        </div>
        <div class="rounded-md border border-gray-200 bg-white p-3">
            <p class="text-xs text-gray-500">Total Payments (Cr)</p>
            <p class="mt-1 text-lg font-bold text-gray-900">{{ $ledger['total_cr'] }}</p>
        </div>
        <div class="rounded-md border border-gray-200 bg-white p-3">
            <p class="text-xs text-gray-500">Outstanding Balance</p>
            <p class="mt-1 text-lg font-bold {{ (float) $ledger['closing_balance_float'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                {{ $ledger['closing_balance'] }}
            </p>
        </div>
    </div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Ageing Due</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
            <div class="rounded-md border border-gray-200 bg-white p-2 text-center">
                <p class="text-[11px] text-gray-500">0–10 d</p>
                <p class="text-sm font-bold text-gray-900">{{ number_format($aging['0_10'], 2) }}</p>
            </div>
            <div class="rounded-md border border-gray-200 bg-white p-2 text-center">
                <p class="text-[11px] text-gray-500">11–30 d</p>
                <p class="text-sm font-bold text-gray-900">{{ number_format($aging['11_30'], 2) }}</p>
            </div>
            <div class="rounded-md border border-gray-200 bg-white p-2 text-center">
                <p class="text-[11px] text-gray-500">31–60 d</p>
                <p class="text-sm font-bold text-gray-900">{{ number_format($aging['31_60'], 2) }}</p>
            </div>
            <div class="rounded-md border border-amber-200 bg-amber-50 p-2 text-center">
                <p class="text-[11px] text-amber-600">61–90 d</p>
                <p class="text-sm font-bold text-amber-700">{{ number_format($aging['61_90'], 2) }}</p>
            </div>
            <div class="rounded-md border border-red-200 bg-red-50 p-2 text-center">
                <p class="text-[11px] text-red-600">90+ d</p>
                <p class="text-sm font-bold text-red-700">{{ number_format($aging['90_plus'], 2) }}</p>
            </div>
            <div class="rounded-md border border-gray-900 bg-gray-900 p-2 text-center">
                <p class="text-[11px] text-gray-300">Total Due</p>
                <p class="text-sm font-bold text-white">{{ number_format($aging['total'], 2) }}</p>
            </div>
        </div>
    </div>

    <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Recent Ledger Entries</p>
        <div class="overflow-x-auto rounded-md border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Voucher</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Dr</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Cr</th>
                        <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($ledger['rows']->reverse()->take(8) as $entry)
                        <tr>
                            <td class="px-3 py-2 text-gray-700">{{ $entry->entry_date->format('d-m-Y') }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $entry->voucher_no }}</td>
                            <td class="px-3 py-2 text-gray-700">{{ $entry->description }}</td>
                            <td class="px-3 py-2 text-right">{{ $entry->dr_amount > 0 ? number_format((float) $entry->dr_amount, 2) : '' }}</td>
                            <td class="px-3 py-2 text-right">{{ $entry->cr_amount > 0 ? number_format((float) $entry->cr_amount, 2) : '' }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($entry->running_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-gray-500">No ledger entries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
