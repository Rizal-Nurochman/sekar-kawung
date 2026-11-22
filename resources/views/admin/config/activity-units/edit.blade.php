<x-admin.layouts.app title="Ubah Satuan - Konfigurasi" heading="Ubah satuan" subheading="{{ $unit->unit_name }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Satuan aktivitas', 'url' => route('admin.config.activity-units.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.activity-units.update', $unit)" method="PUT" :back-route="route('admin.config.activity-units.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail satuan">
            @include('admin.config.activity-units._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
