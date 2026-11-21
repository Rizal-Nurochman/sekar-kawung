@props([
    'id',
    'label',
    'hint' => null,
    'unit' => null,
    'type' => 'text',
])

<div>
    <div class="mb-2 flex items-center justify-between">
        <label for="{{ $id }}" class="text-sm font-semibold text-slate-700">{{ $label }}</label>

        @if ($hint)
            <span class="text-xs text-slate-400">{{ $hint }}</span>
        @endif
    </div>

    @if ($unit)
        <div class="relative">
            <input
                id="{{ $id }}"
                type="{{ $type }}"
                {{ $attributes->merge(['class' => 'w-full rounded-xl border border-gray-200 bg-slate-50 py-3 pl-4 pr-16 text-sm font-semibold text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-500']) }}
            >

            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-400">{{ $unit }}</span>
        </div>
    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            {{ $attributes->merge(['class' => 'w-full rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-500']) }}
        >
    @endif
</div>