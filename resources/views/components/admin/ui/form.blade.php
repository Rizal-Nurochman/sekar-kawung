@props([
    'action',
    'method' => 'POST',
    'backRoute',
    'submitLabel' => 'Simpan',
    'cancelLabel' => 'Batal',
])

<form method="POST" action="{{ $action }}" {{ $attributes->merge(['class' => 'pb-20']) }}>
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif

    <x-admin.ui.errors class="mb-5" />

    <div class="max-w-2xl space-y-5">
        {{ $slot }}
    </div>

    <div class="fixed inset-x-0 bottom-0 z-20 border-t border-line bg-white/95 backdrop-blur lg:left-60">
        <div class="mx-auto flex w-full max-w-6xl items-center justify-end gap-2 px-4 py-3 sm:px-6">
            <x-admin.ui.button :href="$backRoute">{{ $cancelLabel }}</x-admin.ui.button>
            <x-admin.ui.button type="submit" variant="primary">
                <x-lucide-check />
                {{ $submitLabel }}
            </x-admin.ui.button>
        </div>
    </div>
</form>
