<x-super-admin-layout :title="$pharmacy->name">
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold leading-tight text-slate-900">{{ $pharmacy->name }}</h2>
                    <x-status-badge :active="$pharmacy->is_active" />
                </div>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $pharmacy->owner_name ?? '—' }}
                    @if ($pharmacy->email) &middot; {{ $pharmacy->email }} @endif
                    @if ($pharmacy->phone) &middot; {{ $pharmacy->phone }} @endif
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('super-admin.pharmacies.edit', $pharmacy) }}"
                   class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    Edit Details
                </a>

                <form method="POST" action="{{ route('super-admin.pharmacies.status', $pharmacy) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" value="{{ $pharmacy->is_active ? 0 : 1 }}">
                    <button type="submit"
                            class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm transition {{ $pharmacy->is_active ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                        {{ $pharmacy->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Distributors</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $counts['distributors'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Purchase Bills</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $counts['purchases'] }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-500">Payments</p>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Rs. {{ number_format($paymentTotal, 2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $counts['payments'] }} entries</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h3 class="text-sm font-semibold text-slate-900">Pharmacy profile</h3>
                        <p class="mt-0.5 text-xs text-slate-500">These details appear on printed ledgers and invoices.</p>
                    </div>
                    <dl class="divide-y divide-slate-100 text-sm">
                        <div class="flex justify-between gap-4 px-5 py-3">
                            <dt class="text-slate-500">Address</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $pharmacy->address ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 px-5 py-3">
                            <dt class="text-slate-500">City</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $pharmacy->city ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 px-5 py-3">
                            <dt class="text-slate-500">District</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $pharmacy->district ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 px-5 py-3">
                            <dt class="text-slate-500">Province</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $pharmacy->province ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 px-5 py-3">
                            <dt class="text-slate-500">Country</dt>
                            <dd class="text-right font-medium text-slate-900">{{ $pharmacy->country ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-4">
                            <h3 class="text-sm font-semibold text-slate-900">Login accounts</h3>
                        </div>
                        <ul class="divide-y divide-slate-100 text-sm">
                            @foreach ($users as $user)
                                <li class="flex items-center justify-between gap-4 px-5 py-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-slate-900">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                    <span class="shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $user->role }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-4">
                            <h3 class="text-sm font-semibold text-slate-900">Reset password</h3>
                            <p class="mt-0.5 text-xs text-slate-500">Sets a new password for every user of this pharmacy.</p>
                        </div>
                        <form method="POST" action="{{ route('super-admin.pharmacies.password', $pharmacy) }}" class="space-y-4 px-5 py-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <x-input-label for="new_password" value="New password" />
                                <x-text-input id="new_password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="new_password_confirmation" value="Confirm new password" />
                                <x-text-input id="new_password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                            </div>
                            <x-primary-button>Reset Password</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.super-admin>
