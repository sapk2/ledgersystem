<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Distributors</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $distributorCount }}</p>
                        </div>
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('distributors.index') }}" class="mt-4 block border-t border-gray-100 pt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Open ledger →
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Purchases</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($purchaseTotal, 2) }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $purchaseCount }} bills</p>
                        </div>
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="mt-4 block border-t border-gray-100 pt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        View bills →
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Payments</p>
                            <p class="mt-1 text-3xl font-bold text-green-600">{{ number_format($paymentTotal, 2) }}</p>
                        </div>
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('payments.index') }}" class="mt-4 block border-t border-gray-100 pt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Record payment →
                    </a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Outstanding (Due)</p>
                            <p class="mt-1 text-3xl font-bold text-red-600">{{ number_format($outstanding, 2) }}</p>
                        </div>
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <a href="{{ route('purchases.create') }}" class="mt-4 block border-t border-gray-100 pt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        New bill →
                    </a>
                </div>
            </div>

            {{-- Distributor ledger search --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="dashboardLedgerSearch(@js($searchDistributors))">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Search Distributor Ledger</h3>
                        <p class="text-sm text-gray-500">Type a distributor name and click Search to view their ledger instantly.</p>
                    </div>
                </div>
                <div @click.outside="open = false">
                    <div class="flex gap-3">
                        <div class="flex-1 relative">
                            <x-text-input type="text" x-model="query" @input="open = true; selected = null"
                                @keydown.enter.prevent="search()" @keydown.escape="open = false"
                                placeholder="Type distributor name…" class="w-full" />
                            <div x-show="open && query.trim() !== ''" x-cloak
                                class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-64 overflow-auto">
                                <template x-for="d in matches" :key="d.id">
                                    <button type="button" @click="choose(d)"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                                        <span x-text="d.name" class="font-medium"></span>
                                        <span x-text="d.city ? ' — ' + d.city : ''" class="text-gray-400"></span>
                                    </button>
                                </template>
                                <div x-show="matches.length === 0" class="px-4 py-3 text-sm text-gray-500">No matching distributors.</div>
                            </div>
                        </div>
                        <x-primary-button @click="search()">{{ __('Search') }}</x-primary-button>
                    </div>
                    <p x-show="selected" class="mt-2 text-xs text-gray-500">
                        Showing ledger for: <span x-text="selected ? selected.name : ''" class="font-semibold text-gray-700"></span>
                    </p>
                </div>
                <div id="dashboard-ledger" class="mt-4">
                    <div x-show="!loading && !loaded" class="py-8 text-center text-gray-500">
                        Search a distributor above to view their ledger here.
                    </div>
                    <div x-show="loading" class="py-8 text-center text-gray-500">Loading ledger…</div>
                </div>
            </div>

            {{-- Aging analysis --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Aging Analysis</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="rounded-md border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">0–10 days</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($aging['0_10'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">11–30 days</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($aging['11_30'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">31–60 days</p>
                        <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($aging['31_60'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm text-amber-600">61–90 days</p>
                        <p class="mt-1 text-xl font-bold text-amber-700">{{ number_format($aging['61_90'], 2) }}</p>
                    </div>
                    <div class="rounded-md border border-red-200 bg-red-50 p-4">
                        <p class="text-sm text-red-600">90+ days</p>
                        <p class="mt-1 text-xl font-bold text-red-700">{{ number_format($aging['90_plus'], 2) }}</p>
                    </div>
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Bills</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Bill No</th>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Distributor</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($recentPurchases as $purchase)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('purchases.show', $purchase) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $purchase->bill_no }}</a>
                                        </td>
                                        <td class="px-3 py-2 text-gray-600">{{ $purchase->distributor->name }}</td>
                                        <td class="px-3 py-2 text-right font-medium">{{ number_format((float) $purchase->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-6 text-center text-gray-500">No bills yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Highest Balances</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Distributor</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($topDistributors as $distributor)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('distributors.ledger', $distributor['id']) }}" class="font-medium text-indigo-600 hover:text-indigo-800">{{ $distributor['name'] }}</a>
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium {{ $distributor['balance'] > 0 ? 'text-red-600' : '' }}">{{ number_format($distributor['balance'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-6 text-center text-gray-500">No distributors yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function dashboardLedgerSearch(distributors) {
        return {
            distributors,
            query: '',
            selected: null,
            open: false,
            loading: false,
            loaded: false,
            get matches() {
                const q = this.query.trim().toLowerCase();
                if (!q) return [];
                return this.distributors
                    .filter((d) => `${d.name} ${d.city || ''}`.toLowerCase().includes(q))
                    .slice(0, 8);
            },
            choose(d) {
                this.selected = d;
                this.open = false;
                this.query = d.name;
            },
            async search() {
                const d = this.selected || this.matches[0];
                const target = document.getElementById('dashboard-ledger');
                if (!d) {
                    target.innerHTML = '<p class="py-4 text-center text-sm text-gray-500">No matching distributor. Try another name.</p>';
                    this.loading = false;
                    return;
                }
                this.selected = d;
                this.query = d.name;
                this.open = false;
                this.loading = true;
                this.loaded = true;
                try {
                    const res = await fetch(d.summary_url);
                    target.innerHTML = res.ok
                        ? await res.text()
                        : '<p class="py-4 text-center text-sm text-red-600">Could not load the ledger. Please try again.</p>';
                } catch (e) {
                    target.innerHTML = '<p class="py-4 text-center text-sm text-red-600">Could not load the ledger.</p>';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
