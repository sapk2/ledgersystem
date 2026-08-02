<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('payments.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="distributor_id" value="{{ __('Distributor') }}" />
                        <select id="distributor_id" name="distributor_id" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select distributor…</option>
                            @foreach ($distributors as $distributor)
                                <option value="{{ $distributor->id }}"
                                    {{ (string) old('distributor_id', request('distributor')) === (string) $distributor->id ? 'selected' : '' }}>
                                    {{ $distributor->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('distributor_id')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="amount" value="{{ __('Amount (Cr)') }}" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01"
                                value="{{ old('amount') }}" class="mt-1 block w-full" required autofocus />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="payment_date" value="{{ __('Payment Date') }}" />
                            <x-text-input id="payment_date" name="payment_date" type="date"
                                value="{{ old('payment_date', now()->toDateString()) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="reference" value="{{ __('Reference (optional)') }}" />
                        <x-text-input id="reference" name="reference" value="{{ old('reference') }}"
                            placeholder="e.g. Cheque no / UTR / Cash" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="{{ __('Notes') }}" />
                        <textarea id="notes" name="notes" rows="2"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('payments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>{{ __('Save Payment') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
