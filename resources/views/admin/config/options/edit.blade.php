<x-admin.layouts.app title="Ubah Opsi - Konfigurasi" heading="Ubah opsi" subheading="{{ $option->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Opsi', 'url' => route('admin.config.options.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.options.update', $option)" method="PUT" :back-route="route('admin.config.options.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail opsi">
            @include('admin.config.options._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
