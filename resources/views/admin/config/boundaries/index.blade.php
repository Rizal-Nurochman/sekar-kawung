<x-admin.layouts.app title="Boundary - Konfigurasi" heading="Boundary" subheading="Batas sistem di dalam skema.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Boundary'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Boundary"
        :count="$boundaries->total()"
        count-label="boundary"
        create-label="Tambah boundary"
        :create-route="route('admin.config.boundaries.create')"
    >
        @if ($boundaries->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada boundary"
                description="Boundary menandai batas sistem, misalnya kain mentah atau kain batik. Kalkulator memakai urutannya untuk menggabungkan tahap."
            >
                <x-slot:icon>
                    <x-lucide-workflow />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.boundaries.create')" variant="primary" size="sm">Tambah boundary</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Skema</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="w-16 text-right">Urutan</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($boundaries as $boundary)
                            <tr>
                                <td class="text-[13px] text-ink-soft">{{ $boundary->scheme?->description ?? '-' }}</td>
                                <td class="font-medium text-pine-900">{{ $boundary->description }}</td>
                                <td class="cell-num font-normal text-ink-muted">{{ $boundary->sort_order }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$boundary->description"
                                        :edit-route="route('admin.config.boundaries.edit', $boundary)"
                                        :delete-route="route('admin.config.boundaries.destroy', $boundary)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $boundaries->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
