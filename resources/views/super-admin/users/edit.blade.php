<x-super-admin-layout title="Edit User">
    <x-slot name="header">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold leading-tight text-slate-900">{{ $user->name }}</h2>
                    <x-status-badge :active="$user->is_active" />
                </div>
                <p class="mt-1 text-sm text-slate-500">{{ $user->email }}</p>
            </div>
            <a href="{{ route('super-admin.users.index') }}"
               class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Back to users
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-4">
                            <h3 class="text-sm font-semibold text-slate-900">Account details</h3>
                            <p class="mt-0.5 text-xs text-slate-500">Change the identity, role and access status for this login.</p>
                        </div>

                        <form method="POST" action="{{ route('super-admin.users.update', $user) }}"
                              class="space-y-5 px-5 py-5"
                              x-data="{ role: '{{ old('role', $user->role) }}' }">
                            @csrf
                            @method('PUT')

                            <div>
                                <x-input-label for="name" value="Full name" />
                                <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $user->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" value="Email address" />
                                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="role" value="Role" />
                                <select id="role" name="role" @change="role = $event.target.value"
                                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="user" @selected(old('role', $user->role) === 'user')>Pharmacy user</option>
                                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div x-show="role === 'user'" x-cloak>
                                <x-input-label for="pharmacy_id" value="Assigned pharmacy" />
                                <select id="pharmacy_id" name="pharmacy_id"
                                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">— Select a pharmacy —</option>
                                    @foreach ($pharmacies as $pharmacy)
                                        <option value="{{ $pharmacy->id }}" @selected((int) old('pharmacy_id', $user->pharmacy_id) === $pharmacy->id)>{{ $pharmacy->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pharmacy_id')" class="mt-2" />
                            </div>

                            <div>
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))
                                           class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-slate-700">Account is active</span>
                                </label>
                                <p class="mt-1 text-xs text-slate-500">Disabled accounts cannot sign in.</p>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>

                            @if (auth()->user()->is($user))
                                <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800">
                                    You are editing your own account. Your role and access status cannot be changed here.
                                </p>
                            @endif

                            <div class="flex items-center gap-3 border-t border-slate-200 pt-5">
                                <x-primary-button>Save changes</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-5 py-4">
                            <h3 class="text-sm font-semibold text-slate-900">Reset password</h3>
                            <p class="mt-0.5 text-xs text-slate-500">Set a new password for this account.</p>
                        </div>
                        <form method="POST" action="{{ route('super-admin.users.password', $user) }}" class="space-y-4 px-5 py-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <x-input-label for="new_password" value="New password" />
                                <x-text-input id="new_password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="new_password_confirmation" value="Confirm new password" />
                                <x-text-input id="new_password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                            </div>
                            <x-primary-button>Reset Password</x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
