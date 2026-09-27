<x-super-admin-layout title="Edit Pharmacy">
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">Edit Pharmacy</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $pharmacy->name }}</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('super-admin.pharmacies.update', $pharmacy) }}"
                  class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')

                @include('super-admin.pharmacies._form', ['pharmacy' => $pharmacy, 'withPassword' => false])

                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                    <a href="{{ route('super-admin.pharmacies.show', $pharmacy) }}"
                       class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Cancel
                    </a>
                    <x-primary-button>Save Changes</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.super-admin>
