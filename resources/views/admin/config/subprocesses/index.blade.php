<x-admin.layouts.app title="Subproses - Konfigurasi" heading="Subproses" subheading="Tahapan kerja di dalam satu batas sistem.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Subproses'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Subproses"
        :count="$subprocesses->total()"
        count-label="subproses"
        create-label="Tambah subproses"
        :create-route="route('admin.config.subprocesses.create')"
    >
        @if ($subprocesses->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada subproses"
                description="Subproses mengelompokkan aktivitas menjadi tahap yang bisa dibuka-tutup di kalkulator."
            >
                <x-slot:icon>
                    <x-lucide-list-tree />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.subprocesses.create')" variant="primary" size="sm">Tambah subproses</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Proses</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="w-16 text-right">Urutan</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subprocesses as $subprocess)
                            <tr>
                                <td class="text-[13px] text-ink-soft">{{ $subprocess->process?->description ?? '-' }}</td>
                                <td class="font-medium text-pine-900">{{ $subprocess->description }}</td>
                                <td class="cell-num font-normal text-ink-muted">{{ $subprocess->sort_order }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$subprocess->description"
                                        :edit-route="route('admin.config.subprocesses.edit', $subprocess)"
                                        :delete-route="route('admin.config.subprocesses.destroy', $subprocess)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $subprocesses->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
