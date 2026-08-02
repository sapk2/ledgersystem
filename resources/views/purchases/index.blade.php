<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Bills') }}
            </h2>
            <a href="{{ route('purchases.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('New Bill') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-sm text-green-700 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('purchases.index') }}" class="flex gap-3 items-center mb-5">
                    <div class="flex-1">
                        <x-text-input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search by bill no or distributor…" class="w-full" />
                    </div>
                    <x-primary-button>{{ __('Search') }}</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Bill No</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Distributor</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2.5 text-sm font-medium text-gray-900">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="hover:text-indigo-600">
                                            {{ $purchase->bill_no }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-gray-500">{{ $purchase->bill_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2.5 text-sm text-gray-700">
                                        <a href="{{ route('distributors.ledger', $purchase->distributor) }}" class="hover:text-indigo-600">
                                            {{ $purchase->distributor->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-gray-500 text-right">{{ $purchase->items_count ?? '' }}</td>
                                    <td class="px-4 py-2.5 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format((float) $purchase->total_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-sm space-x-3">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
                                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="inline" onsubmit="return confirm('Delete this bill and its ledger entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-gray-500">
                                        No bills yet. <a href="{{ route('purchases.create') }}" class="text-indigo-600 hover:text-indigo-800">Create the first bill</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
