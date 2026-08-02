<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Distributor Ledger — A–Z Index') }}
            </h2>
            <a href="{{ route('distributors.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Add Distributor') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-6"
                 x-data="ledgerIndex(@js($searchList), @js($term), @js($activeLetter))">
                <div class="flex gap-3 items-center" @click.outside="showSuggestions = false">
                    <div class="flex-1 relative">
                        <x-text-input type="text" name="q" x-model="query" @input="showSuggestions = true"
                            @keydown.escape="showSuggestions = false"
                            placeholder="Search distributor… e.g. Parat Pharma" class="w-full" />

                        <div x-show="showSuggestions && query.trim() !== ''" x-cloak
                            class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg max-h-72 overflow-auto">
                            <template x-for="d in suggestions" :key="d.name">
                                <a :href="d.ledger_url" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span x-text="d.name" class="font-medium"></span>
                                    <span x-text="d.city ? ' — ' + d.city : ''" class="text-gray-400"></span>
                                </a>
                            </template>
                            <div x-show="suggestions.length === 0" class="px-4 py-3 text-sm text-gray-500">
                                No distributors match "<span x-text="query"></span>".
                            </div>
                        </div>
                    </div>
                    <span x-show="query.trim() !== ''" class="text-xs text-gray-500 whitespace-nowrap"
                        x-text="filtered.length + ' account' + (filtered.length === 1 ? '' : 's')"></span>
                    <a href="#" @click.prevent="query = ''; activeLetter = ''"
                       x-show="query !== '' || activeLetter !== ''" class="text-sm text-gray-500 hover:text-gray-700">
                        {{ __('Clear') }}
                    </a>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <button type="button" @click="activeLetter = ''"
                        class="px-3 py-1 rounded-md text-sm font-medium"
                        :class="activeLetter === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                        All
                    </button>
                    <template x-for="letter in alphabet" :key="letter">
                        <button type="button" @click="activeLetter = activeLetter === letter ? '' : letter"
                            class="px-3 py-1 rounded-md text-sm font-medium"
                            :class="activeLetter === letter ? 'bg-indigo-600 text-white' : (letters.includes(letter) ? 'bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-gray-50 text-gray-300')">
                            <span x-text="letter"></span>
                        </button>
                    </template>
                </div>

                <template x-for="(distributors, letter) in grouped" :key="letter">
                    <div>
                        <div class="border-b-2 border-gray-800 pb-1 mb-3">
                            <h3 class="text-lg font-bold text-gray-900 tracking-wide" x-text="letter"></h3>
                        </div>
                        <div class="overflow-x-auto rounded-md border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Distributor</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">City</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Balance</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <template x-for="d in distributors" :key="d.name">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2.5 text-sm font-medium text-gray-900">
                                                <a :href="d.ledger_url" class="hover:text-indigo-600" x-text="d.name"></a>
                                            </td>
                                            <td class="px-4 py-2.5 text-sm text-gray-500" x-text="d.city || '—'"></td>
                                            <td class="px-4 py-2.5 text-sm text-gray-500" x-text="d.phone || '—'"></td>
                                            <td class="px-4 py-2.5 text-sm text-right font-semibold"
                                                :class="d.balance > 0 ? 'text-red-600' : 'text-gray-900'">
                                                <span x-text="formatMoney(d.balance)"></span>
                                            </td>
                                            <td class="px-4 py-2.5 text-right text-sm">
                                                <a :href="d.ledger_url" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                                    Ledger →
                                                </a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <div x-show="filtered.length === 0" class="py-12 text-center text-gray-500">
                    <p class="text-lg" x-text="query.trim() === '' && activeLetter === '' ? 'No distributors yet.' : 'No distributors found.'"></p>
                    <p class="mt-2 text-sm" x-show="query.trim() === '' && activeLetter === ''">
                        <a href="{{ route('distributors.create') }}" class="text-indigo-600 hover:text-indigo-800">
                            Add your first distributor
                        </a> to start the ledger.
                    </p>
                    <p class="mt-2 text-sm" x-show="query.trim() !== '' || activeLetter !== ''">Try a different search or letter.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ledgerIndex(items, initialQuery, initialLetter) {
            return {
                items,
                query: initialQuery || '',
                activeLetter: initialLetter || '',
                showSuggestions: false,
                alphabet: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split(''),
                get filtered() {
                    const q = this.query.trim().toLowerCase();
                    return this.items.filter((d) => {
                        const haystack = `${d.name} ${d.city || ''} ${d.phone || ''}`.toLowerCase();
                        return (!q || haystack.includes(q)) && (!this.activeLetter || d.letter === this.activeLetter);
                    });
                },
                get suggestions() {
                    return this.filtered.slice(0, 8);
                },
                get grouped() {
                    return this.filtered.reduce((groups, d) => {
                        (groups[d.letter] = groups[d.letter] || []).push(d);
                        return groups;
                    }, {});
                },
                get letters() {
                    return [...new Set(this.items.map((d) => d.letter))];
                },
                formatMoney(value) {
                    return Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
            };
        }
    </script>
</x-app-layout>
