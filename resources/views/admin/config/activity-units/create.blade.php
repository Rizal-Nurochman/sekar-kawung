<x-admin.layouts.app title="Tambah Satuan - Konfigurasi" heading="Tambah satuan" subheading="Satuan untuk kuantitas aktivitas dan opsi.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Satuan aktivitas', 'url' => route('admin.config.activity-units.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.activity-units.store')" :back-route="route('admin.config.activity-units.index')">
        <x-admin.ui.panel title="Detail satuan">
            @include('admin.config.activity-units._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
