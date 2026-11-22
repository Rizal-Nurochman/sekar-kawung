<x-admin.layouts.app title="Aktivitas - Konfigurasi" heading="Aktivitas" subheading="Baris inventaris yang diisi kuantitasnya di kalkulator.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Aktivitas'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Aktivitas"
        :count="$activities->total()"
        count-label="aktivitas"
        create-label="Tambah aktivitas"
        :create-route="route('admin.config.activities.create')"
    >
        @if ($activities->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada aktivitas"
                description="Aktivitas adalah baris yang diisi admin di kalkulator. Setiap aktivitas butuh satuan dan faktor emisi, atau daftar opsi."
            >
                <x-slot:icon>
                    <x-lucide-list-checks />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.activities.create')" variant="primary" size="sm">Tambah aktivitas</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Aktivitas</th>
                            <th scope="col">Subproses</th>
                            <th scope="col">Faktor emisi</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td class="font-medium text-pine-900">{{ $activity->description }}</td>
                                <td class="max-w-[16rem] text-[13px] text-ink-soft">{{ $activity->subprocess?->description ?? '-' }}</td>
                                <td class="text-[13px]">
                                    @if ($activity->options_count > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-badge bg-sage-50 px-1.5 py-0.5 font-semibold text-sage-700">
                                            {{ $activity->options_count }} opsi
                                        </span>
                                        <span class="text-ink-muted">ditentukan opsi</span>
                                    @elseif ($activity->emissionFactor)
                                        <span class="font-medium tabular-nums text-ink-soft">{{ \App\Support\Number::emission($activity->emissionFactor->ef_value) }}</span>
                                        <span class="text-ink-muted">kg CO2e / {{ $activity->activityUnit?->unit_name ?? 'satuan' }}</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-badge bg-sage-50 px-1.5 py-0.5 font-semibold text-sage-700">Manual</span>
                                        <span class="text-ink-muted">0 kg CO2e</span>
                                    @endif
                                </td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$activity->description"
                                        :edit-route="route('admin.config.activities.edit', $activity)"
                                        :delete-route="route('admin.config.activities.destroy', $activity)"
                                        :extra="[
                                            ['label' => 'Atur opsi', 'route' => route('admin.config.activity-options.edit', $activity), 'icon' => 'list-checks'],
                                        ]"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $activities->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
