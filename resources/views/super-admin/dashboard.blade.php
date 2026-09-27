<x-super-admin-layout title="Dashboard">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-900">Super Admin Dashboard</h2>
                <p class="mt-1 text-sm text-slate-500">Platform-wide overview of every registered pharmacy.</p>
            </div>
            <a href="{{ route('super-admin.pharmacies.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Pharmacy
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Total Pharmacies</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $pharmacyCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        <span class="font-semibold text-emerald-600">{{ $activePharmacyCount }}</span> active &middot;
                        <span class="font-semibold text-amber-600">{{ $inactivePharmacyCount }}</span> inactive
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Pharmacy Users</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $userCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">Login accounts across all pharmacies</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Distributors</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $distributorCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $purchaseCount }} purchase bills recorded</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Total Payments</p>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Rs. {{ number_format($paymentTotal, 2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">Recorded across every pharmacy</p>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Recently registered pharmacies</h3>
                    <a href="{{ route('super-admin.pharmacies.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">View all</a>
                </div>

                @if ($recentPharmacies->isEmpty())
                    <p class="px-5 py-8 text-center text-sm text-slate-500">No pharmacies have been registered yet.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($recentPharmacies as $pharmacy)
                            <li class="flex items-center justify-between gap-4 px-5 py-4">
                                <div class="min-w-0">
                                    <a href="{{ route('super-admin.pharmacies.show', $pharmacy) }}"
                                       class="truncate text-sm font-semibold text-slate-900 hover:text-indigo-700">{{ $pharmacy->name }}</a>
                                    <p class="truncate text-xs text-slate-500">
                                        {{ $pharmacy->email }}
                                        @if ($pharmacy->city) &middot; {{ $pharmacy->city }} @endif
                                        &middot; {{ $pharmacy->users_count }} user(s)
                                    </p>
                                </div>
                                <x-status-badge :active="$pharmacy->is_active" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-super-admin-layout>
