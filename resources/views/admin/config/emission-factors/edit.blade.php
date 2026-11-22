<x-admin.layouts.app title="Ubah Faktor Emisi - Konfigurasi" heading="Ubah faktor emisi" subheading="{{ $factor->unit_name }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Faktor emisi', 'url' => route('admin.config.emission-factors.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.emission-factors.update', $factor)" method="PUT" :back-route="route('admin.config.emission-factors.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail faktor emisi">
            @include('admin.config.emission-factors._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
