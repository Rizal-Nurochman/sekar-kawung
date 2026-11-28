<x-admin.layouts.app title="Skema - Konfigurasi" heading="Skema" subheading="Jalur produksi yang dihitung, misalnya Gedog Tuban.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Skema'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Skema"
        :count="$schemes->total()"
        count-label="skema"
        create-label="Tambah skema"
        :create-route="route('admin.config.schemes.create')"
    >
        @if ($schemes->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada skema"
                description="Skema adalah titik awal semua perhitungan. Buat satu skema untuk setiap jalur produksi."
            >
                <x-slot:icon>
                    <x-lucide-layers />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.schemes.create')" variant="primary" size="sm">Tambah skema</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col" class="w-16">Urutan</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schemes as $scheme)
                            <tr>
                                <td class="tabular-nums text-ink-muted">{{ $scheme->sort_order }}</td>
                                <td class="font-medium text-pine-900">{{ $scheme->description }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$scheme->description"
                                        :edit-route="route('admin.config.schemes.edit', $scheme)"
                                        :delete-route="route('admin.config.schemes.destroy', $scheme)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $schemes->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
