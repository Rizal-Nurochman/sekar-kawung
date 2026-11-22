@props([
    'label',
    'name',
    'hint' => null,
    'required' => false,
])

@php($error = $errors->first($name))

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
        <label for="{{ $name }}" class="text-[13px] font-medium text-pine-900">
            {{ $label }}@if ($required)<span class="text-red-600" aria-hidden="true"> *</span>@endif
        </label>
        @if ($hint)
            <span class="text-xs leading-5 text-ink-muted">{{ $hint }}</span>
        @endif
    </div>

    {{ $slot }}

    @if ($error)
        <p id="{{ $name }}-error" class="text-[13px] leading-5 text-red-700">{{ $error }}</p>
    @endif
</div>
