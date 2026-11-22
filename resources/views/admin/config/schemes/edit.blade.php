<x-admin.layouts.app title="Ubah Skema - Konfigurasi" heading="Ubah skema" subheading="{{ $scheme->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Skema', 'url' => route('admin.config.schemes.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.schemes.update', $scheme)" method="PUT" :back-route="route('admin.config.schemes.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail skema">
            @include('admin.config.schemes._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
