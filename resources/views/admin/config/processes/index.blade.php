<x-admin.layouts.app title="Proses - Konfigurasi" heading="Proses" subheading="Tahapan kerja di dalam satu batas sistem.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Proses'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Proses"
        :count="$processes->total()"
        count-label="proses"
        create-label="Tambah proses"
        :create-route="route('admin.config.processes.create')"
    >
        @if ($processes->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada proses"
                description="Proses mengelompokkan aktivitas menjadi tahap yang bisa dibuka-tutup di kalkulator."
            >
                <x-slot:icon>
                    <x-lucide-list-tree />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.processes.create')" variant="primary" size="sm">Tambah proses</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Produk</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="w-16 text-right">Urutan</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($processes as $process)
                            <tr>
                                <td class="text-[13px] text-ink-soft">{{ $process->product?->description ?? '-' }}</td>
                                <td class="font-medium text-pine-900">{{ $process->description }}</td>
                                <td class="cell-num font-normal text-ink-muted">{{ $process->sort_order }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$process->description"
                                        :edit-route="route('admin.config.processes.edit', $process)"
                                        :delete-route="route('admin.config.processes.destroy', $process)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $processes->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
