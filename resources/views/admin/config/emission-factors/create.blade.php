<x-admin.layouts.app title="Tambah Faktor Emisi - Konfigurasi" heading="Tambah faktor emisi" subheading="Nilai emisi per satuan untuk dipakai aktivitas atau opsi.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Faktor emisi', 'url' => route('admin.config.emission-factors.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.emission-factors.store')" :back-route="route('admin.config.emission-factors.index')">
        <x-admin.ui.panel title="Detail faktor emisi">
            @include('admin.config.emission-factors._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
