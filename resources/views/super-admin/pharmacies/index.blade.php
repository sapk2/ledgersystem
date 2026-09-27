<x-super-admin-layout title="Pharmacies">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-slate-900">Pharmacies</h2>
                <p class="mt-1 text-sm text-slate-500">Manage pharmacy accounts, logins and access status.</p>
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

            <form method="GET" action="{{ route('super-admin.pharmacies.index') }}"
                  class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex-1">
                    <x-text-input name="search" type="search" class="block w-full"
                                  :value="$search" placeholder="Search by name, owner, email or city" />
                </div>
                <div>
                    <select name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-44">
                        <option value="">All statuses</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-primary-button>Filter</x-primary-button>
                    @if ($search || $status)
                        <a href="{{ route('super-admin.pharmacies.index') }}"
                           class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Pharmacy</th>
                                <th class="px-5 py-3">Login</th>
                                <th class="px-5 py-3">Location</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($pharmacies as $pharmacy)
                                <tr>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('super-admin.pharmacies.show', $pharmacy) }}"
                                           class="font-semibold text-slate-900 hover:text-indigo-700">{{ $pharmacy->name }}</a>
                                        <p class="text-xs text-slate-500">{{ $pharmacy->owner_name ?? '—' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $pharmacy->email }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $pharmacy->city ?? '—' }}</td>
                                    <td class="px-5 py-4"><x-status-badge :active="$pharmacy->is_active" /></td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('super-admin.pharmacies.show', $pharmacy) }}"
                                               class="font-semibold text-indigo-600 hover:text-indigo-800">Manage</a>
                                            <a href="{{ route('super-admin.pharmacies.edit', $pharmacy) }}"
                                               class="font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                        No pharmacies found.
                                        <a href="{{ route('super-admin.pharmacies.create') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Create the first one.</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pharmacies->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
                        {{ $pharmacies->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.super-admin>
