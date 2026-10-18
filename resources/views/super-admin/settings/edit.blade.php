<x-super-admin-layout title="Settings">
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">Settings</h2>
            <p class="mt-1 text-sm text-slate-500">Platform-wide configuration for the administration area.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">General settings</h3>
                    <p class="mt-0.5 text-xs text-slate-500">These values apply to the whole platform.</p>
                </div>

                <form method="POST" action="{{ route('super-admin.settings.update') }}" class="space-y-5 px-5 py-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="platform_name" value="Platform name" />
                        <x-text-input id="platform_name" name="platform_name" type="text" class="block mt-1 w-full"
                                      :value="old('platform_name', $platformName)" required autofocus />
                        <p class="mt-1 text-xs text-slate-500">Shown as the brand name throughout the administration area.</p>
                        <x-input-error :messages="$errors->get('platform_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="support_email" value="Support email" />
                        <x-text-input id="support_email" name="support_email" type="email" class="block mt-1 w-full"
                                      :value="old('support_email', $supportEmail)" />
                        <p class="mt-1 text-xs text-slate-500">Optional contact address for pharmacy administrators.</p>
                        <x-input-error :messages="$errors->get('support_email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="default_pharmacy_status" value="Default status for new pharmacies" />
                        <select id="default_pharmacy_status" name="default_pharmacy_status"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('default_pharmacy_status', $defaultPharmacyStatus) === 'active')>Active</option>
                            <option value="inactive" @selected(old('default_pharmacy_status', $defaultPharmacyStatus) === 'inactive')>Inactive</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Newly created pharmacies start with this status.</p>
                        <x-input-error :messages="$errors->get('default_pharmacy_status')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3 border-t border-slate-200 pt-5">
                        <x-primary-button>Save settings</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-super-admin-layout>
