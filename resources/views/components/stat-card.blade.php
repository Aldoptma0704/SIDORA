@props([
    'title' => '',
    'value' => '0',
    'icon' => 'fas fa-chart-bar',
    'color' => 'blue', // blue, amber, emerald, rose, slate
    'description' => null,
])

@php
    $colorMaps = [
        'blue' => [
            'iconBg' => 'bg-blue-50 text-blue-600 border-blue-100',
            'value' => 'text-gray-900',
        ],
        'amber' => [
            'iconBg' => 'bg-amber-50 text-amber-600 border-amber-100',
            'value' => 'text-gray-900',
        ],
        'emerald' => [
            'iconBg' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'value' => 'text-gray-900',
        ],
        'rose' => [
            'iconBg' => 'bg-rose-50 text-rose-600 border-rose-100',
            'value' => 'text-gray-900',
        ],
        'slate' => [
            'iconBg' => 'bg-slate-100 text-slate-600 border-slate-200',
            'value' => 'text-gray-900',
        ],
    ];

    $style = $colorMaps[$color] ?? $colorMaps['blue'];
@endphp

<div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 flex flex-col justify-between">
    <div class="flex items-center justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider truncate">{{ $title }}</p>
            <p class="text-2xl font-bold {{ $style['value'] }} mt-1.5">{{ $value }}</p>
        </div>
        <div class="w-11 h-11 rounded-xl flex items-center justify-center border flex-shrink-0 {{ $style['iconBg'] }}">
            <i class="{{ $icon }} text-lg"></i>
        </div>
    </div>
    @if($description)
        <p class="text-xs text-gray-500 mt-3 pt-2.5 border-t border-gray-100 flex items-center gap-1.5">
            {{ $description }}
        </p>
    @endif
</div>
