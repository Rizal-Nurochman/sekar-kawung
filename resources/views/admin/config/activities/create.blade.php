<x-admin.layouts.app title="Tambah Aktivitas - Konfigurasi" heading="Tambah aktivitas" subheading="Satu baris inventaris yang diisi di kalkulator.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Aktivitas', 'url' => route('admin.config.activities.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.activities.store')" :back-route="route('admin.config.activities.index')">
        <x-admin.ui.panel title="Detail aktivitas">
            @include('admin.config.activities._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
