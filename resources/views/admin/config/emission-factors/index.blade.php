<x-admin.layouts.app title="Faktor Emisi - Konfigurasi" heading="Faktor emisi" subheading="Nilai emisi per satuan yang dipakai kalkulator.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Faktor emisi'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Faktor emisi"
        :count="$factors->total()"
        count-label="faktor"
        create-label="Tambah faktor"
        :create-route="route('admin.config.emission-factors.create')"
    >
        @if ($factors->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada faktor emisi"
                description="Tanpa faktor emisi, aktivitas selalu menghasilkan 0 kg CO2e. Tambahkan nilai per satuan beserta sumbernya."
            >
                <x-slot:icon>
                    <x-lucide-flask-conical />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.emission-factors.create')" variant="primary" size="sm">Tambah faktor</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Satuan</th>
                            <th scope="col" class="text-right">Nilai (kg CO2e)</th>
                            <th scope="col">Sumber</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($factors as $factor)
                            <tr>
                                <td class="font-medium text-pine-900">{{ $factor->unit_name }}</td>
                                <td class="cell-num">{{ \App\Support\Number::emission($factor->ef_value) }}</td>
                                <td class="text-[13px] text-ink-soft">{{ $factor->source?->title ?? '-' }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$factor->unit_name"
                                        :edit-route="route('admin.config.emission-factors.edit', $factor)"
                                        :delete-route="route('admin.config.emission-factors.destroy', $factor)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $factors->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
