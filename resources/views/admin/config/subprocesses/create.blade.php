<x-admin.layouts.app title="Tambah Subproses - Konfigurasi" heading="Tambah subproses" subheading="Kelompokkan aktivitas menjadi satu tahap kerja.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Subproses', 'url' => route('admin.config.subprocesses.index')],
            ['label' => 'Tambah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.subprocesses.store')" :back-route="route('admin.config.subprocesses.index')">
        <x-admin.ui.panel title="Detail subproses">
            @include('admin.config.subprocesses._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
