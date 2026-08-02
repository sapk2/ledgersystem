<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Payments') }}
            </h2>
            <a href="{{ route('payments.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Record Payment') }}
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
                <form method="GET" action="{{ route('payments.index') }}" class="flex gap-3 items-center mb-5">
                    <div class="flex-1">
                        <x-text-input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search by reference or distributor…" class="w-full" />
                    </div>
                    <x-primary-button>{{ __('Search') }}</x-primary-button>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Distributor</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2.5 text-sm text-gray-500">{{ $payment->payment_date->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2.5 text-sm text-gray-700">
                                        <a href="{{ route('distributors.ledger', $payment->distributor) }}" class="hover:text-indigo-600">
                                            {{ $payment->distributor->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-gray-500">{{ $payment->reference ?: '—' }}</td>
                                    <td class="px-4 py-2.5 text-sm font-semibold text-gray-900 text-right">
                                        {{ number_format((float) $payment->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-sm">
                                        <form method="POST" action="{{ route('payments.destroy', $payment) }}" class="inline" onsubmit="return confirm('Delete this payment and its ledger entry?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                        No payments recorded yet. <a href="{{ route('payments.create') }}" class="text-indigo-600 hover:text-indigo-800">Record one</a>.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
