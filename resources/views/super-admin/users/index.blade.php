<x-super-admin-layout title="User Management">
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">User Management</h2>
            <p class="mt-1 text-sm text-slate-500">Every login account across the platform, including pharmacy owners and administrators.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('super-admin.users.index') }}"
                  class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex-1">
                    <x-text-input name="search" type="search" class="block w-full"
                                  :value="$search" placeholder="Search by name, email or pharmacy" />
                </div>
                <div>
                    <select name="role" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-44">
                        <option value="">All roles</option>
                        <option value="admin" @selected($role === 'admin')>Administrator</option>
                        <option value="user" @selected($role === 'user')>Pharmacy user</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-40">
                        <option value="">All statuses</option>
                        <option value="active" @selected($status === 'active')>Active</option>
                        <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-primary-button>Filter</x-primary-button>
                    @if ($search || $role || $status)
                        <a href="{{ route('super-admin.users.index') }}"
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
                                <th class="px-5 py-3">User</th>
                                <th class="px-5 py-3">Pharmacy</th>
                                <th class="px-5 py-3">Role</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($users as $user)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $user->pharmacy?->name ?? '—' }}</td>
                                    <td class="px-5 py-4">
                                        @if ($user->isAdmin())
                                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/20">Administrator</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-slate-500/20">Pharmacy user</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4"><x-status-badge :active="$user->is_active" /></td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('super-admin.users.edit', $user) }}"
                                               class="font-semibold text-indigo-600 transition-colors hover:text-indigo-800">Edit</a>

                                            @unless (auth()->user()->is($user))
                                                <form method="POST" action="{{ route('super-admin.users.status', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="is_active" value="{{ $user->is_active ? 0 : 1 }}">
                                                    <button type="submit"
                                                            class="font-semibold transition-colors {{ $user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                                        {{ $user->is_active ? 'Disable' : 'Enable' }}
                                                    </button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">No users match your filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-super-admin-layout>
