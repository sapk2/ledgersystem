<x-super-admin-layout title="Audit Logs">
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">System / Audit Logs</h2>
            <p class="mt-1 text-sm text-slate-500">A chronological record of administrative actions taken across the platform.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" action="{{ route('super-admin.audit-logs.index') }}"
                  class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex-1">
                    <x-text-input name="search" type="search" class="block w-full"
                                  :value="$search" placeholder="Search the log description" />
                </div>
                <div>
                    <select name="action" class="block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64">
                        <option value="">All actions</option>
                        @foreach ($actions as $option)
                            <option value="{{ $option }}" @selected($action === $option)>{{ \App\Models\AuditLog::label($option) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <x-primary-button>Filter</x-primary-button>
                    @if ($search || $action)
                        <a href="{{ route('super-admin.audit-logs.index') }}"
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
                                <th class="px-5 py-3">When</th>
                                <th class="px-5 py-3">Administrator</th>
                                <th class="px-5 py-3">Action</th>
                                <th class="px-5 py-3">Description</th>
                                <th class="px-5 py-3">IP address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($logs as $log)
                                <tr class="transition-colors hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                        {{ $log->created_at?->format('M j, Y') }}
                                        <p class="text-xs text-slate-400">{{ $log->created_at?->format('H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900">{{ $log->user?->name ?? 'System' }}</p>
                                        <p class="text-xs text-slate-500">{{ $log->user?->email ?? '—' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-500/20">
                                            {{ \App\Models\AuditLog::label($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $log->description }}</td>
                                    <td class="px-5 py-4 text-slate-500">{{ $log->ip_address ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">No activity has been recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($logs->hasPages())
                    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-super-admin-layout>
