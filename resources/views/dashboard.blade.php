<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 truncate text-sm text-gray-500">
                    {{ $currentPharmacy?->name ?? config('ledger.pharmacy_name', 'Ledger Book') }}
                    &middot; {{ now()->format('l, j F Y') }}
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap items-center gap-2">
                <a href="{{ route('purchases.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    All Bills
                </a>
                <a href="{{ route('purchases.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-150 ease-in-out hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    New Bill
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Quick actions --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @php
                    $quickActions = [
                        ['label' => 'New Bill', 'hint' => 'Record a distributor purchase', 'url' => route('purchases.create'), 'icon' => 'M12 4.5v15m7.5-7.5h-15', 'tone' => 'indigo'],
                        ['label' => 'Record Payment', 'hint' => 'Pay a distributor', 'url' => route('payments.index'), 'icon' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A1.125 1.125 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z', 'tone' => 'green'],
                        ['label' => 'Add Distributor', 'hint' => 'Open a new supplier ledger', 'url' => route('distributors.create'), 'icon' => 'M12 4.5v15m7.5-7.5h-15', 'tone' => 'amber'],
                        ['label' => 'Add Item', 'hint' => 'Add a medicine catalogue item', 'url' => route('medicines.create'), 'icon' => 'M12 4.5v15m7.5-7.5h-15', 'tone' => 'slate'],
                    ];
                    $tones = [
                        'indigo' => 'bg-indigo-50 text-indigo-600',
                        'green' => 'bg-green-50 text-green-600',
                        'amber' => 'bg-amber-50 text-amber-600',
                        'slate' => 'bg-slate-100 text-slate-700',
                    ];
                @endphp

                @foreach ($quickActions as $action)
                    <a href="{{ $action['url'] }}"
                       class="group flex items-center gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm transition duration-150 ease-in-out hover:border-indigo-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $tones[$action['tone']] }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}" />
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold text-gray-900 group-hover:text-indigo-700">{{ $action['label'] }}</span>
                            <span class="block truncate text-xs text-gray-500">{{ $action['hint'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>

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
                    <a href="{{ route('distributors.index') }}" class="mt-4 block border-t border-gray-100 pt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Review dues →
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
                        <p class="text-sm text-gray-500">Search by name, city, phone or PAN — then press <kbd class="px-1.5 py-0.5 rounded border border-gray-300 bg-gray-100 text-[11px] font-sans">Enter</kbd> to open the ledger.</p>
                    </div>
                </div>
                <div @click.outside="close()">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1 relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <x-text-input id="dashboard-ledger-search" x-ref="search" type="text" x-model="query"
                                @input="onInput()" @focus="open = true" @keydown.arrow-down.prevent="move(1)"
                                @keydown.arrow-up.prevent="move(-1)" @keydown.enter.prevent="onEnter()"
                                @keydown.escape="close()"
                                role="combobox" aria-controls="dashboard-ledger-results" x-bind:aria-expanded="open"
                                autocomplete="off"
                                placeholder="Type a name, city, phone or PAN…"
                                class="w-full pl-9" />
                            <button type="button" x-show="query !== ''" x-cloak @click="clearQuery()"
                                aria-label="Clear search"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div id="dashboard-ledger-results" x-show="open" x-cloak @click.outside="open = false"
                                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden">
                                <template x-if="term && matches.length">
                                    <div class="border-b border-gray-100 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        <span x-text="matchCount"></span>
                                    </div>
                                </template>

                                <ul role="listbox" x-show="term && matches.length" class="max-h-72 overflow-auto divide-y divide-gray-100">
                                    <template x-for="(d, index) in matches" :key="d.id">
                                        <li role="option" x-bind:aria-selected="index === active">
                                            <button type="button" @click="load(d)" @mouseenter="active = index"
                                                class="flex w-full items-center justify-between gap-3 px-4 py-2.5 text-left"
                                                x-bind:class="index === active ? 'bg-indigo-50' : 'hover:bg-gray-50'">
                                                <span class="min-w-0">
                                                    <span class="block truncate text-sm font-medium text-gray-900"
                                                        x-html="highlight(d.name)"></span>
                                                    <span class="block truncate text-xs text-gray-500"
                                                        x-text="[d.city, d.phone, d.pan_no ? 'PAN ' + d.pan_no : ''].filter(Boolean).join(' • ')"></span>
                                                </span>
                                                <span class="shrink-0 text-right">
                                                    <span class="block text-xs text-gray-400">Due</span>
                                                    <span class="block text-sm font-semibold"
                                                        x-bind:class="d.due > 0 ? 'text-red-600' : 'text-green-600'"
                                                        x-text="money(d.due)"></span>
                                                </span>
                                            </button>
                                        </li>
                                    </template>
                                </ul>

                                <div x-show="term && matches.length === 0" class="px-4 py-4 text-sm text-gray-500">
                                    No distributor matches “<span class="font-medium text-gray-700" x-text="term"></span>”.
                                    <a href="{{ route('distributors.index') }}" class="mt-1 block text-indigo-600 hover:text-indigo-800">
                                        Browse the full ledger list →
                                    </a>
                                </div>

                                <div x-show="!term && recent.length" class="p-2">
                                    <p class="px-2 py-1.5 text-xs font-semibold uppercase tracking-wide text-gray-500">Recent</p>
                                    <div class="flex flex-wrap gap-2 px-2 pb-1">
                                        <template x-for="d in recent" :key="'recent-' + d.id">
                                            <button type="button" @click="load(d)"
                                                class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700">
                                                <span x-text="d.name"></span>
                                                <span class="text-gray-400" x-text="d.due > 0 ? money(d.due) : 'settled'"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="!term && !recent.length" class="px-4 py-3 text-sm text-gray-500">
                                    Start typing to search <span x-text="distributors.length"></span> distributors.
                                </div>
                            </div>
                        </div>

                        <x-primary-button type="button" @click="onEnter()" x-bind:disabled="!term"
                            x-bind:class="term ? '' : 'opacity-50 cursor-not-allowed'"
                            class="sm:self-start">{{ __('Search') }}</x-primary-button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500" x-show="!current" x-cloak>
                        Tip: use <kbd class="px-1 py-0.5 rounded border border-gray-300 bg-gray-100 font-sans">↑</kbd>
                        <kbd class="px-1 py-0.5 rounded border border-gray-300 bg-gray-100 font-sans">↓</kbd> to pick a
                        distributor, <kbd class="px-1 py-0.5 rounded border border-gray-300 bg-gray-100 font-sans">Esc</kbd> to close.
                    </p>
                </div>

                <div x-show="current" x-cloak class="mt-5 border-t border-gray-200 pt-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm text-gray-600">
                            Ledger for
                            <span class="font-semibold text-gray-900" x-text="current ? current.name : ''"></span>
                        </p>
                        <div class="flex items-center gap-4">
                            <a x-bind:href="current ? current.ledger_url : '#'" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                Open full ledger →
                            </a>
                            <button type="button" @click="clearResult()" class="text-sm text-gray-500 hover:text-gray-700">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                <div x-show="error" x-cloak
                    class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <span>Could not load the ledger. Please check your connection and try again.</span>
                    <button type="button" @click="search()" class="font-semibold underline">Retry</button>
                </div>

                <div id="dashboard-ledger" class="mt-4">
                    <div x-show="!loading && !loaded" class="rounded-md border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500">
                        Search a distributor above to see their balance, ageing and ledger entries here.
                    </div>
                    <div x-show="loading" class="py-10 text-center text-sm text-gray-500">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                            </svg>
                            Loading ledger…
                        </span>
                    </div>
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
                    <a href="{{ route('purchases.index') }}" class="mb-4 block text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        View all {{ $purchaseCount }} {{ Str::plural('bill', $purchaseCount) }} →
                    </a>
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
                                        <td colspan="3" class="px-3 py-8 text-center text-gray-500">
                                            <p class="font-medium text-gray-700">No bills recorded yet.</p>
                                            <a href="{{ route('purchases.create') }}"
                                               class="mt-2 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out hover:bg-indigo-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                                Create your first bill
                                            </a>
                                        </td>
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
                    <a href="{{ route('distributors.index') }}" class="mb-4 block text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        View all {{ $distributorCount }} {{ Str::plural('distributor', $distributorCount) }} →
                    </a>
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
                                        <td colspan="2" class="px-3 py-6 text-center text-gray-500">
                                            No distributors yet.
                                            <a href="{{ route('distributors.create') }}" class="mt-1 block font-medium text-indigo-600 hover:text-indigo-800">
                                                Add your first distributor →
                                            </a>
                                        </td>
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
            current: null,
            open: false,
            loading: false,
            loaded: false,
            error: false,
            active: 0,
            recent: this.readRecent(),
            limit: 8,
            storageKey: 'dashboard-recent-distributors',

            init() {
                this.$nextTick(() => this.bindShortcut());
            },

            bindShortcut() {
                document.addEventListener('keydown', (event) => {
                    const tag = (event.target.tagName || '').toLowerCase();
                    if (event.key !== '/' || event.ctrlKey || event.metaKey || event.altKey) return;
                    if (['input', 'textarea', 'select'].includes(tag) || event.target.isContentEditable) return;
                    event.preventDefault();
                    this.$refs.search?.focus();
                });
            },

            get term() {
                return this.query.trim().toLowerCase();
            },

            get matches() {
                if (!this.term) return [];
                const term = this.term;

                return this.distributors
                    .filter((d) => d.search.includes(term))
                    .map((d) => {
                        const name = d.name.toLowerCase();
                        const score = name.startsWith(term) ? 0 : name.includes(term) ? 1 : 2;
                        return { d, score };
                    })
                    .sort((a, b) => a.score - b.score || a.d.name.localeCompare(b.d.name))
                    .slice(0, this.limit)
                    .map((row) => row.d);
            },

            get matchCount() {
                const total = this.term
                    ? this.distributors.filter((d) => d.search.includes(this.term)).length
                    : 0;

                if (!total) return '';
                const shown = Math.min(total, this.limit);
                return total === 1 ? '1 distributor' : shown < total ? `${shown} of ${total} distributors` : `${total} distributors`;
            },

            onInput() {
                this.open = true;
                this.active = 0;

                if (this.term && this.matches.length === 1) {
                    this.load(this.matches[0]);
                }
            },

            onEnter() {
                if (!this.term) return;

                if (this.open && this.matches.length) {
                    this.load(this.matches[Math.min(this.active, this.matches.length - 1)]);
                    return;
                }

                this.search();
            },

            move(step) {
                if (!this.matches.length) return;
                this.open = true;
                const next = this.active + step;
                this.active = next < 0 ? this.matches.length - 1 : next % this.matches.length;
            },

            close() {
                this.open = false;
                this.active = 0;
            },

            clearQuery() {
                this.query = '';
                this.close();
                this.$refs.search?.focus();
            },

            clearResult() {
                this.current = null;
                this.loaded = false;
                this.error = false;
                this.target().innerHTML = '';
            },

            search() {
                const first = this.matches[0];

                if (first) {
                    this.load(first);
                    return;
                }

                if (!this.term) return;

                this.error = true;
                this.loaded = false;
                this.current = null;
            },

            async load(distributor) {
                this.current = distributor;
                this.query = distributor.name;
                this.open = false;
                this.error = false;
                this.loaded = true;

                const target = this.target();
                const requestUrl = distributor.summary_url;

                this.loading = true;
                target.setAttribute('aria-busy', 'true');

                try {
                    const response = await fetch(requestUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!response.ok) throw new Error('Request failed');

                    target.innerHTML = await response.text();
                    this.remember(distributor);
                } catch (e) {
                    target.innerHTML = '';
                    this.error = true;
                } finally {
                    this.loading = false;
                    target.removeAttribute('aria-busy');
                }
            },

            remember(distributor) {
                this.recent = [distributor, ...this.recent.filter((d) => d.id !== distributor.id)].slice(0, 5);

                try {
                    localStorage.setItem(
                        this.storageKey,
                        JSON.stringify(this.recent.map((d) => ({ id: d.id, name: d.name, due: d.due })))
                    );
                } catch (e) {
                    // Storage unavailable (private mode) — recent list stays in memory only.
                }
            },

            readRecent() {
                try {
                    const stored = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                    return stored
                        .map((entry) => distributors.find((d) => d.id === entry.id))
                        .filter(Boolean)
                        .slice(0, 5);
                } catch (e) {
                    return [];
                }
            },

            target() {
                return document.getElementById('dashboard-ledger');
            },

            highlight(value) {
                const escaped = this.escape(value);
                const term = this.escape(this.term);

                if (!term) return escaped;

                const index = escaped.toLowerCase().indexOf(term);
                if (index === -1) return escaped;

                return (
                    escaped.slice(0, index)
                    + '<mark class="rounded bg-indigo-100 px-0.5 text-indigo-900">'
                    + escaped.slice(index, index + term.length)
                    + '</mark>'
                    + escaped.slice(index + term.length)
                );
            },

            escape(value) {
                return String(value ?? '').replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;',
                }[char]));
            },

            money(value) {
                return Number(value || 0).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            },
        };
    }
</script>
