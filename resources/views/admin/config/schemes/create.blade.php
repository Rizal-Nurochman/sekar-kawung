<x-admin.layouts.app title="Tambah Skema - Konfigurasi" heading="Tambah skema" subheading="Buat jalur produksi baru untuk dihitung.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Skema', 'url' => route('admin.config.schemes.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.schemes.store')" :back-route="route('admin.config.schemes.index')">
        <x-admin.ui.panel title="Detail skema">
            @include('admin.config.schemes._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
