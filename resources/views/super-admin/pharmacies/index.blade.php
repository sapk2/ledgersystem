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

    <div class="py-8"
         x-data="{
             open: false,
             name: '',
             action: '',
             ask(name, action) {
                 this.name = name;
                 this.action = action;
                 this.open = true;
             },
             close() {
                 this.open = false;
             },
         }"
         @keydown.escape.window="close()">
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
                                <tr class="transition-colors hover:bg-slate-50">
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
                                               class="font-semibold text-indigo-600 transition-colors hover:text-indigo-800">Manage</a>
                                            <a href="{{ route('super-admin.pharmacies.edit', $pharmacy) }}"
                                               class="font-semibold text-slate-600 transition-colors hover:text-slate-900">Edit</a>
                                            <button type="button"
                                                    data-name="{{ $pharmacy->name }}"
                                                    data-action="{{ route('super-admin.pharmacies.destroy', $pharmacy) }}"
                                                    @click="ask($el.dataset.name, $el.dataset.action)"
                                                    class="font-semibold text-red-500 transition-colors hover:text-red-700">
                                                Delete
                                            </button>
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

            {{-- Delete confirmation --}}
            <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
                 role="dialog" aria-modal="true" aria-labelledby="delete-pharmacy-title">
                <div x-show="open"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     @click="close()"
                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                <div x-show="open"
                     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative mx-auto w-full max-w-md overflow-hidden rounded-xl bg-white shadow-xl sm:my-8">
                    <div class="flex items-start gap-4 px-6 py-5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <h3 id="delete-pharmacy-title" class="text-base font-semibold text-slate-900">Delete pharmacy</h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Are you sure you want to delete
                                <span class="font-semibold text-slate-900" x-text="name"></span>?
                                This action cannot be undone.
                            </p>
                            <p class="mt-2 text-xs text-slate-500">Historical payment and ledger records will be preserved.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                        <button type="button" @click="close()"
                                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                            Cancel
                        </button>
                        <form :action="action" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Confirm Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.super-admin>
