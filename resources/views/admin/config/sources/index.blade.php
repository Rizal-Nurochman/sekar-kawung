<x-admin.layouts.app title="Sumber - Konfigurasi" heading="Sumber" subheading="Referensi ilmiah di balik faktor emisi.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Sumber'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Sumber"
        :count="$sources->total()"
        count-label="sumber"
        create-label="Tambah sumber"
        :create-route="route('admin.config.sources.create')"
    >
        @if ($sources->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada sumber"
                description="Sumber tampil sebagai sitasi di halaman laporan publik. Tambahkan referensi sebelum mengisi faktor emisi."
            >
                <x-slot:icon>
                    <x-lucide-book-open />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.sources.create')" variant="primary" size="sm">Tambah sumber</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Judul</th>
                            <th scope="col">Tautan</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sources as $source)
                            <tr>
                                <td class="font-medium text-pine-900">{{ $source->title }}</td>
                                <td class="max-w-[24rem] text-[13px] text-ink-soft">
                                    @if ($source->link_reference)
                                        <a href="{{ $source->link_reference }}" target="_blank" rel="noopener" class="inline-flex max-w-full items-center gap-1 truncate hover:underline">
                                            <span class="truncate">{{ $source->link_reference }}</span>
                                            <x-lucide-external-link class="size-3 shrink-0 text-ink-muted" />
                                        </a>
                                    @else
                                        <span class="text-ink-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$source->title"
                                        :edit-route="route('admin.config.sources.edit', $source)"
                                        :delete-route="route('admin.config.sources.destroy', $source)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $sources->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
