@props([
    'label',
    'editRoute' => null,
    'editLabel' => 'Ubah',
    'deleteRoute' => null,
    'deleteLabel' => 'Hapus',
    'deleteConfirm' => 'Hapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'extra' => [],
])

@php
    $extraLinks = collect($extra)->filter(fn ($link) => ! empty($link['route']));
    $hasActions = $editRoute || $deleteRoute || $extraLinks->isNotEmpty();
@endphp

@if ($hasActions)
    <div class="flex items-center justify-end gap-0.5" x-data="{ open: false }" x-on:keydown.escape.window="open = false">
        @if ($editRoute)
            <x-admin.ui.icon-button :href="$editRoute" :label="$editLabel.' '.$label">
                <x-lucide-pencil class="size-3.5" />
            </x-admin.ui.icon-button>
        @endif

        @if ($deleteRoute || $extraLinks->isNotEmpty())
            <div class="relative">
                <x-admin.ui.icon-button label="Aksi lain untuk {{ $label }}" x-on:click="open = !open" x-bind:aria-expanded="open" aria-haspopup="menu">
                    <x-lucide-ellipsis />
                </x-admin.ui.icon-button>

                <div
                    x-cloak
                    x-show="open"
                    x-on:click.outside="open = false"
                    x-transition.origin.top.right
                    class="absolute right-0 top-full z-30 mt-1 w-44 rounded-control border border-line bg-white p-1 shadow-lg shadow-pine-900/10"
                    role="menu"
                >
                    @foreach ($extraLinks as $link)
                        <a href="{{ $link['route'] }}" class="flex items-center gap-2 rounded-[0.4rem] px-2.5 py-1.5 text-[13px] font-medium text-pine-900 transition-colors hover:bg-sage-50" role="menuitem">
                            <x-dynamic-component :component="'lucide-'.($link['icon'] ?? 'arrow-right')" class="size-3.5 text-ink-muted" />
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    @if ($deleteRoute)
                        <form method="POST" action="{{ $deleteRoute }}" onsubmit="return confirm('{{ $deleteConfirm }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center gap-2 rounded-[0.4rem] px-2.5 py-1.5 text-left text-[13px] font-medium text-red-700 transition-colors hover:bg-red-50" role="menuitem">
                                <x-lucide-trash-2 class="size-3.5" />
                                {{ $deleteLabel }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif
