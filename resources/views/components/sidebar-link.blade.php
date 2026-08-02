@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-white bg-indigo-600 shadow-sm transition duration-150 ease-in-out'
            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none focus:bg-slate-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
