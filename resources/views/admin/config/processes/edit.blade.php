<x-admin.layouts.app title="Ubah Proses - Konfigurasi" heading="Ubah proses" subheading="{{ $process->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Proses', 'url' => route('admin.config.processes.index')],
            ['label' => 'Ubah proses'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.processes.update', $process)" method="PUT" :back-route="route('admin.config.processes.index')" submit-label="Simpan perubahan">
        @include('admin.config.processes._form')
    </x-admin.ui.form>
</x-admin.layouts.app>
