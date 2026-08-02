<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Purchase Bill') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-sm text-red-700 rounded-md">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('purchases.store') }}"
                      x-data="purchaseForm(@js($medicines))">
                    @csrf

                    <datalist id="medicines-list">
                        @foreach ($medicines as $medicine)
                            <option value="{{ $medicine }}"></option>
                        @endforeach
                    </datalist>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="distributor_id" value="{{ __('Distributor') }}" />
                            <select id="distributor_id" name="distributor_id" required
                                x-model.number="distributorId"
                                @change="generateBillNo()"
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

                        <div>
                            <x-input-label for="bill_no" value="{{ __('Bill No (auto)') }}" />
                            <x-text-input id="bill_no" name="bill_no" x-model="billNo" readonly
                                class="mt-1 block w-full bg-gray-50 text-gray-500" />
                            <x-input-error :messages="$errors->get('bill_no')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="bill_date" value="{{ __('Bill Date') }}" />
                            <x-text-input id="bill_date" name="bill_date" type="date" value="{{ old('bill_date', now()->toDateString()) }}" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('bill_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="discount" value="{{ __('Discount (Rs.)') }}" />
                            <x-text-input id="discount" name="discount" type="number" min="0" step="any" value="{{ old('discount', 0) }}"
                                x-model.number="discount" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('discount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="vat" value="{{ __('VAT (%)') }}" />
                            <x-text-input id="vat" name="vat" type="number" min="0" max="100" step="any" value="{{ old('vat', 0) }}"
                                x-model.number="vat" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('vat')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" value="{{ __('Notes') }}" />
                            <x-text-input id="notes" name="notes" value="{{ old('notes') }}" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Medicine Items</h3>
                            <button type="button" @click="addItem()"
                                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                + Add Item
                            </button>
                        </div>

                        <div class="overflow-x-auto border border-gray-200 rounded-md">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Name</th>
                                        <th class="px-3 py-2 w-24 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Batch</th>
                                        <th class="px-3 py-2 w-28 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Expiry</th>
                                        <th class="px-3 py-2 w-24 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-3 py-2 w-32 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Rate</th>
                                        <th class="px-3 py-2 w-32 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-3 py-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr>
                                            <td class="px-3 py-2">
                                                <input type="text"
                                                    :name="`items[${index}][medicine_name]`"
                                                    x-model="item.medicine_name"
                                                    list="medicines-list"
                                                    placeholder="Type or select medicine"
                                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                                    required>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="text"
                                                    :name="`items[${index}][batch_no]`"
                                                    x-model="item.batch_no"
                                                    placeholder="Batch"
                                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="date"
                                                    :name="`items[${index}][expiry_date]`"
                                                    x-model="item.expiry_date"
                                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" min="0.01" step="any"
                                                    :name="`items[${index}][quantity]`"
                                                    x-model.number="item.quantity"
                                                    @input="updateAmount(item)"
                                                    class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                                    required>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" min="0" step="any"
                                                    :name="`items[${index}][rate]`"
                                                    x-model.number="item.rate"
                                                    @input="updateAmount(item)"
                                                    class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                                    required>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-right font-semibold text-gray-900">
                                                <span x-text="formatMoney(item.amount)">0.00</span>
                                                <input type="hidden" :name="`items[${index}][amount]`" :value="item.amount">
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                    class="text-red-500 hover:text-red-700 font-bold">×</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="5" class="px-3 py-2 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">Sub Total</td>
                                        <td class="px-3 py-2 text-right text-sm font-bold text-gray-900">
                                            <span x-text="formatMoney(subtotal)">0.00</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-3 py-2 text-right text-sm text-gray-600">
                                            Discount (Rs.) <span class="text-gray-400">—</span>
                                            <span x-text="formatMoney(discount)">0.00</span>
                                        </td>
                                        <td class="px-3 py-2 text-right text-sm text-gray-600">
                                            VAT <span x-text="vat + '%'">0%</span>:
                                            <span x-text="formatMoney(vatAmount)">0.00</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-3 py-2 text-right text-sm font-bold text-gray-700 uppercase tracking-wider">Net Total</td>
                                        <td class="px-3 py-2 text-right text-base font-bold text-gray-900">
                                            <span x-text="formatMoney(netTotal)">0.00</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('purchases.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button>{{ __('Save Bill & Update Ledger') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function purchaseForm(medicines) {
            return {
                medicines,
                distributorId: @js((int) old('distributor_id', request('distributor'))),
                billNo: @js(old('bill_no', '')),
                discount: 0,
                vat: 0,
                items: [{ medicine_name: '', batch_no: '', expiry_date: '', quantity: 1, rate: 0, amount: 0 }],
                async generateBillNo() {
                    if (!this.distributorId) {
                        this.billNo = '';
                        return;
                    }
                    const response = await fetch(`/purchases/bill-number?distributor_id=${this.distributorId}`);
                    const data = await response.json();
                    this.billNo = data.bill_no;
                },
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + this.lineAmount(item), 0);
                },
                get vatAmount() {
                    return (this.subtotal - (parseFloat(this.discount) || 0)) * ((parseFloat(this.vat) || 0) / 100);
                },
                get netTotal() {
                    return this.subtotal - (parseFloat(this.discount) || 0) + this.vatAmount;
                },
                lineAmount(item) {
                    return (parseFloat(item.quantity) || 0) * (parseFloat(item.rate) || 0);
                },
                updateAmount(item) {
                    item.amount = this.lineAmount(item);
                },
                addItem() {
                    this.items.push({ medicine_name: '', batch_no: '', expiry_date: '', quantity: 1, rate: 0, amount: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                formatMoney(value) {
                    return Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
            };
        }
    </script>
</x-app-layout>
