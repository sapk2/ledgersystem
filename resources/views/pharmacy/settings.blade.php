<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Pharmacy Details</h2>
            <p class="mt-1 text-sm text-gray-600">These details are printed on every ledger and invoice.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('pharmacy.settings.update') }}"
                  class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" value="Pharmacy name" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $pharmacy?->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="owner_name" value="Owner / contact person" />
                        <x-text-input id="owner_name" name="owner_name" type="text" class="block mt-1 w-full" :value="old('owner_name', $pharmacy?->owner_name)" />
                        <x-input-error :messages="$errors->get('owner_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" value="Phone" />
                        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $pharmacy?->phone)" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-input-label for="address" value="Address" />
                        <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $pharmacy?->address)" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="city" value="City" />
                        <x-text-input id="city" name="city" type="text" class="block mt-1 w-full" :value="old('city', $pharmacy?->city)" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="district" value="District" />
                        <x-text-input id="district" name="district" type="text" class="block mt-1 w-full" :value="old('district', $pharmacy?->district)" />
                        <x-input-error :messages="$errors->get('district')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="province" value="Province" />
                        <x-text-input id="province" name="province" type="text" class="block mt-1 w-full" :value="old('province', $pharmacy?->province)" />
                        <x-input-error :messages="$errors->get('province')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="country" value="Country" />
                        <x-text-input id="country" name="country" type="text" class="block mt-1 w-full" :value="old('country', $pharmacy?->country)" />
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end border-t border-slate-200 pt-6">
                    <x-primary-button>Save Details</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
