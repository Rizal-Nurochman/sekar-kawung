<x-admin.layouts.app title="Tambah Proses - Konfigurasi" heading="Tambah proses" subheading="Tentukan batas sistem di dalam sebuah skema.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Proses', 'url' => route('admin.config.processes.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.processes.store')" :back-route="route('admin.config.processes.index')">
        <x-admin.ui.panel title="Detail proses">
            @include('admin.config.processes._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
