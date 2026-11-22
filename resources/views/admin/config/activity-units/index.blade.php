<x-admin.layouts.app title="Satuan Aktivitas - Konfigurasi" heading="Satuan aktivitas" subheading="Satuan yang dipakai aktivitas dan opsi.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Satuan aktivitas'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Satuan aktivitas"
        :count="$units->total()"
        count-label="satuan"
        create-label="Tambah satuan"
        :create-route="route('admin.config.activity-units.create')"
    >
        @if ($units->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada satuan"
                description="Satuan menjelaskan kuantitas di kalkulator, misalnya kWh, liter, atau kg."
            >
                <x-slot:icon>
                    <x-lucide-ruler />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.activity-units.create')" variant="primary" size="sm">Tambah satuan</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama satuan</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $unit)
                            <tr>
                                <td class="font-medium text-pine-900">{{ $unit->unit_name }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$unit->unit_name"
                                        :edit-route="route('admin.config.activity-units.edit', $unit)"
                                        :delete-route="route('admin.config.activity-units.destroy', $unit)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $units->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
