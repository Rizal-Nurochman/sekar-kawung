<x-admin.layouts.app title="Opsi - Konfigurasi" heading="Opsi" subheading="Pilihan jawaban untuk aktivitas.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Opsi'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Opsi"
        :count="$options->total()"
        count-label="opsi"
        create-label="Tambah opsi"
        :create-route="route('admin.config.options.create')"
    >
        @if ($options->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada opsi"
                description="Opsi dipakai aktivitas yang jawabannya bervariasi, misalnya moda transportasi atau jenis bahan bakar."
            >
                <x-slot:icon>
                    <x-lucide-circle-dot />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.options.create')" variant="primary" size="sm">Tambah opsi</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Deskripsi</th>
                            <th scope="col">Satuan</th>
                            <th scope="col">Faktor emisi</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($options as $option)
                            <tr>
                                <td class="font-medium text-pine-900">{{ $option->description }}</td>
                                <td class="text-[13px] text-ink-soft">{{ $option->activityUnit?->unit_name ?? '-' }}</td>
                                <td class="text-[13px]">
                                    @if ($option->emissionFactor)
                                        <span class="tabular-nums text-ink-soft">{{ \App\Support\Number::emission($option->emissionFactor->ef_value) }}</span>
                                        <span class="text-ink-muted">kg CO2e / {{ $option->activityUnit?->unit_name ?? 'satuan' }}</span>
                                    @else
                                        <span class="text-ink-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$option->description"
                                        :edit-route="route('admin.config.options.edit', $option)"
                                        :delete-route="route('admin.config.options.destroy', $option)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $options->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
