@props(['active' => true])

@if ($active)
    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
        Active
    </span>
@else
    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">
        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
        Inactive
    </span>
@endif
