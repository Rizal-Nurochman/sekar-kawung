<x-admin.layouts.app title="Tambah Opsi - Konfigurasi" heading="Tambah opsi" subheading="Pilihan jawaban yang bisa dipasang ke aktivitas.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Opsi', 'url' => route('admin.config.options.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.options.store')" :back-route="route('admin.config.options.index')">
        <x-admin.ui.panel title="Detail opsi">
            @include('admin.config.options._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
