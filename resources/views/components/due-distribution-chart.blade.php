@props(['slices'])

@php
    $palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'];

    $slices = collect($slices)->map(fn (array $slice, int $index) => [
        'label' => $slice['name'],
        'amount' => (float) $slice['amount'],
        'color' => ! empty($slice['others']) ? '#94a3b8' : $palette[$index % count($palette)],
    ])->values();

    $total = (float) $slices->sum('amount');

    // Build the pie geometry once, in a 200x200 viewBox, starting at 12 o'clock.
    $cx = 100.0;
    $cy = 100.0;
    $r = 100.0;
    $angle = -90.0;
    $segments = [];

    foreach ($slices as $slice) {
        $fraction = $total > 0 ? $slice['amount'] / $total : 0.0;
        $sweep = $fraction * 360.0;
        $path = null;

        // A full circle can't be drawn with a single arc segment.
        if ($sweep > 0 && $fraction < 0.9999) {
            $x0 = $cx + $r * cos(deg2rad($angle));
            $y0 = $cy + $r * sin(deg2rad($angle));
            $x1 = $cx + $r * cos(deg2rad($angle + $sweep));
            $y1 = $cy + $r * sin(deg2rad($angle + $sweep));
            $largeArc = $sweep > 180 ? 1 : 0;

            $path = 'M '.$cx.' '.$cy
                .' L '.round($x0, 3).' '.round($y0, 3)
                .' A '.$r.' '.$r.' 0 '.$largeArc.' 1 '.round($x1, 3).' '.round($y1, 3)
                .' Z';
        }

        $segments[] = [
            'label' => $slice['label'],
            'amount' => $slice['amount'],
            'color' => $slice['color'],
            'percent' => $fraction * 100,
            'path' => $path,
        ];

        $angle += $sweep;
    }
@endphp

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg p-6']) }}
     x-data="{ active: null }">
    <div class="flex items-center gap-3 mb-4">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
            </svg>
        </div>
        <div class="min-w-0">
            <h3 class="text-lg font-semibold text-gray-900">Outstanding Dues by Distributor</h3>
            <p class="text-sm text-gray-500">Share of the total amount still owed to each supplier.</p>
        </div>
    </div>

    <a href="{{ route('distributors.index') }}" class="mb-4 block text-sm font-medium text-indigo-600 hover:text-indigo-800">
        View all distributors →
    </a>

    @if ($total > 0)
        <div class="grid grid-cols-1 items-center gap-8 md:grid-cols-2">
            <div class="relative mx-auto aspect-square w-full max-w-xs">
                <svg viewBox="0 0 200 200" class="h-full w-full" role="img"
                     aria-label="Pie chart of outstanding dues by distributor">
                    @foreach ($segments as $index => $segment)
                        @if ($segment['path'])
                            <path d="{{ $segment['path'] }}" fill="{{ $segment['color'] }}"
                                  stroke="#ffffff" stroke-width="1.5" class="cursor-pointer transition-opacity duration-150"
                                  x-bind:class="active === null || active === {{ $index }} ? 'opacity-100' : 'opacity-40'"
                                  @mouseenter="active = {{ $index }}" @mouseleave="active = null"
                                  @focus="active = {{ $index }}" @blur="active = null" tabindex="0">
                                <title>{{ $segment['label'] }}: {{ number_format($segment['amount'], 2) }} ({{ number_format($segment['percent'], 1) }}%)</title>
                            </path>
                        @else
                            <circle cx="100" cy="100" r="100" fill="{{ $segment['color'] }}"
                                    class="cursor-pointer transition-opacity duration-150"
                                    x-bind:class="active === null || active === {{ $index }} ? 'opacity-100' : 'opacity-40'"
                                    @mouseenter="active = {{ $index }}" @mouseleave="active = null"
                                    @focus="active = {{ $index }}" @blur="active = null" tabindex="0">
                                <title>{{ $segment['label'] }}: {{ number_format($segment['amount'], 2) }} ({{ number_format($segment['percent'], 1) }}%)</title>
                            </circle>
                        @endif
                    @endforeach
                </svg>
            </div>

            <ul class="space-y-2">
                @foreach ($segments as $index => $segment)
                    <li class="flex items-center gap-3 rounded-md px-2 py-1.5 transition-opacity duration-150"
                        x-bind:class="active === null || active === {{ $index }} ? 'opacity-100' : 'opacity-40'"
                        @mouseenter="active = {{ $index }}" @mouseleave="active = null"
                        @focusin="active = {{ $index }}" @focusout="active = null" tabindex="0">
                        <span class="h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $segment['color'] }}"></span>
                        <span class="min-w-0 flex-1 truncate text-sm text-gray-700">{{ $segment['label'] }}</span>
                        <span class="shrink-0 text-sm font-semibold text-gray-900">{{ number_format($segment['amount'], 2) }}</span>
                        <span class="shrink-0 w-12 text-right text-xs text-gray-500">{{ number_format($segment['percent'], 1) }}%</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="rounded-md border border-dashed border-gray-300 py-10 text-center text-sm text-gray-500">
            <p class="font-medium text-gray-700">No outstanding dues.</p>
            <p class="mt-1">Every distributor account is settled.</p>
        </div>
    @endif
</div>
