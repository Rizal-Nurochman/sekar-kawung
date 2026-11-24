<x-admin.layouts.app title="Tambah Proses - Konfigurasi" heading="Tambah proses" subheading="Kelompokkan aktivitas menjadi satu tahap kerja.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Proses', 'url' => route('admin.config.processes.index')],
            ['label' => 'Tambah proses'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.processes.store')" :back-route="route('admin.config.processes.index')">
        @include('admin.config.processes._form')
    </x-admin.ui.form>
</x-admin.layouts.app>
