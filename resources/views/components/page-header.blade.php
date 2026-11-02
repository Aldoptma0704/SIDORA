@props([
    'title' => '',
    'description' => '',
    'icon' => null,
])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sm:p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($icon)
                <div class="w-11 h-11 bg-primary-50 rounded-xl flex items-center justify-center text-primary-600 border border-primary-100 flex-shrink-0">
                    <i class="{{ $icon }} text-xl"></i>
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $title }}</h1>
                @if($description)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $description }}</p>
                @endif
            </div>
        </div>

        @if(isset($actions) && $actions->isNotEmpty())
            <div class="flex items-center gap-3 flex-wrap">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
