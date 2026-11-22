@props([
    'label',
    'name',
    'value',
    'checked' => false,
])

<label class="flex cursor-pointer items-start gap-3 rounded-control border px-3 py-2.5 transition-colors {{ $checked ? 'border-pine-900/25 bg-sage-50' : 'border-line-strong bg-white hover:border-pine-900/25' }}">
    <input
        type="checkbox"
        name="{{ $name }}[]"
        value="{{ $value }}"
        @checked($checked)
        class="mt-0.5 size-4 shrink-0 rounded border-line-strong accent-pine-900"
    >
    <span class="min-w-0">
        <span class="block text-[13.5px] font-medium text-pine-900">{{ $label }}</span>
        @isset($meta)
            <span class="mt-0.5 block text-xs leading-5 text-ink-muted">{{ $meta }}</span>
        @endisset
    </span>
</label>
