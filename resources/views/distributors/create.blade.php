<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Distributor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('distributors.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="name" value="{{ __('Distributor Name') }}" />
                        <x-text-input id="name" name="name" value="{{ old('name') }}" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="phone" value="{{ __('Phone') }}" />
                            <x-text-input id="phone" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="{{ __('Email') }}" />
                            <x-text-input id="email" type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="address" value="{{ __('Address') }}" />
                        <x-text-input id="address" name="address" value="{{ old('address') }}" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="city" value="{{ __('City') }}" />
                            <x-text-input id="city" name="city" value="{{ old('city') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="pan_no" value="{{ __('PAN No') }}" />
                            <x-text-input id="pan_no" name="pan_no" value="{{ old('pan_no') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('pan_no')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="opening_balance" value="{{ __('Opening Balance (Dr)') }}" />
                            <x-text-input id="opening_balance" name="opening_balance" type="number" step="0.01" min="0"
                                value="{{ old('opening_balance', 0) }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('opening_balance')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" value="{{ __('Notes') }}" />
                        <textarea id="notes" name="notes" rows="3"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('distributors.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>{{ __('Save Distributor') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
