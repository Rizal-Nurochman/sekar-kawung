<x-admin.layouts.app title="Tambah Sumber - Konfigurasi" heading="Tambah sumber" subheading="Referensi yang akan disitasi di laporan publik.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Sumber', 'url' => route('admin.config.sources.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.sources.store')" :back-route="route('admin.config.sources.index')">
        <x-admin.ui.panel title="Detail sumber">
            @include('admin.config.sources._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
