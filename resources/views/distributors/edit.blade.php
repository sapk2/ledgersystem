<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Distributor') }} — {{ $distributor->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('distributors.update', $distributor) }}" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" value="{{ __('Distributor Name') }}" />
                        <x-text-input id="name" name="name" value="{{ old('name', $distributor->name) }}" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="phone" value="{{ __('Phone') }}" />
                            <x-text-input id="phone" name="phone" value="{{ old('phone', $distributor->phone) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="{{ __('Email') }}" />
                            <x-text-input id="email" type="email" name="email" value="{{ old('email', $distributor->email) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" value="{{ __('Address') }}" />
                        <x-text-input id="address" name="address" value="{{ old('address', $distributor->address) }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="city" value="{{ __('City') }}" />
                            <x-text-input id="city" name="city" value="{{ old('city', $distributor->city) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="pan_no" value="{{ __('PAN No') }}" />
                            <x-text-input id="pan_no" name="pan_no" value="{{ old('pan_no', $distributor->pan_no) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('pan_no')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="opening_balance" value="{{ __('Opening Balance (Dr)') }}" />
                            <x-text-input id="opening_balance" name="opening_balance" type="number" step="0.01" min="0"
                                value="{{ old('opening_balance', $distributor->opening_balance) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('opening_balance')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" value="{{ __('Notes') }}" />
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $distributor->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <form method="POST" action="{{ route('distributors.destroy', $distributor) }}" onsubmit="return confirm('Delete this distributor and all its ledger history?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                {{ __('Delete Distributor') }}
                            </button>
                        </form>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('distributors.ledger', $distributor) }}" class="text-sm text-gray-500 hover:text-gray-700">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>{{ __('Update Distributor') }}</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
