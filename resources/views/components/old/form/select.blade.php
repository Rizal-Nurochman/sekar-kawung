@props([
    'id',
    'label',
    'hint' => null,
])

<div>
    <div class="mb-2 flex items-center justify-between">
        <label for="{{ $id }}" class="text-sm font-semibold text-slate-700">{{ $label }}</label>

        @if ($hint)
            <span class="text-xs text-slate-400">{{ $hint }}</span>
        @endif
    </div>

    <select
        id="{{ $id }}"
        {{ $attributes->merge(['class' => 'w-full rounded-xl border border-gray-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 transition-all focus:bg-white focus:outline-none focus:ring-2 focus:ring-sage-500']) }}
    >
        {{ $slot }}
    </select>
</div>