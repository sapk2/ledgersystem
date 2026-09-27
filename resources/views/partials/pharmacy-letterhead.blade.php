@php
    $printPharmacy = $currentPharmacy ?? null;
@endphp
<div class="text-center">
    <h1 class="text-3xl font-bold uppercase tracking-tight text-gray-900">{{ $printPharmacy?->name ?? config('ledger.pharmacy_name') }}</h1>
    @if ($printPharmacy)
        <p class="mt-1.5 text-sm text-gray-600">{{ $printPharmacy->addressLines() ? implode(', ', $printPharmacy->addressLines()) : ($printPharmacy->address ?: config('ledger.pharmacy_address')) }}</p>
    @elseif (config('ledger.pharmacy_address'))
        <p class="mt-1.5 text-sm text-gray-600">{{ config('ledger.pharmacy_address') }}</p>
    @endif
    <p class="text-sm text-gray-600">
        @if ($printPharmacy?->phone)
            <span>Contact: {{ $printPharmacy->phone }}</span>
        @elseif (config('ledger.pharmacy_phone'))
            <span>Contact: {{ config('ledger.pharmacy_phone') }}</span>
        @endif
        @if ($printPharmacy?->email)
            <span class="hidden print:inline"> &nbsp;•&nbsp; {{ $printPharmacy->email }}</span>
        @elseif (config('ledger.pharmacy_email'))
            <span class="hidden print:inline"> &nbsp;•&nbsp; {{ config('ledger.pharmacy_email') }}</span>
        @endif
    </p>
    <div class="mt-4 border-b-4 border-double border-gray-800"></div>
</div>
