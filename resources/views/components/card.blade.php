@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden']) }}>
    @if($title || isset($headerActions))
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-4 bg-white">
            <div>
                @if($title)
                    <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-5 sm:p-6' : '' }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endif
</div>
