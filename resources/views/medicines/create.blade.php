<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Medicine') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('medicines.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="name" value="{{ __('Medicine Name') }}" />
                        <x-text-input id="name" name="name" value="{{ old('name') }}" class="mt-1 block w-full" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="generic_name" value="{{ __('Generic Name') }}" />
                            <x-text-input id="generic_name" name="generic_name" value="{{ old('generic_name') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('generic_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="packing" value="{{ __('Packing') }}" />
                            <x-text-input id="packing" name="packing" value="{{ old('packing') }}" placeholder="e.g. 10×10" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('packing')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('medicines.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>{{ __('Save Medicine') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
