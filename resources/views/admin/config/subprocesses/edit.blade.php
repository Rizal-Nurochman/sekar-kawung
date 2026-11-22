<x-admin.layouts.app title="Ubah Subproses - Konfigurasi" heading="Ubah subproses" subheading="{{ $subprocess->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Subproses', 'url' => route('admin.config.subprocesses.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.subprocesses.update', $subprocess)" method="PUT" :back-route="route('admin.config.subprocesses.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail subproses">
            @include('admin.config.subprocesses._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
