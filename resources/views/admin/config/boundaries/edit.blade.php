<x-admin.layouts.app title="Ubah Boundary - Konfigurasi" heading="Ubah boundary" subheading="{{ $boundary->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Boundary', 'url' => route('admin.config.boundaries.index')],
            ['label' => 'Ubah boundary'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.boundaries.update', $boundary)" method="PUT" :back-route="route('admin.config.boundaries.index')" submit-label="Simpan perubahan">
        @include('admin.config.boundaries._form')
    </x-admin.ui.form>
</x-admin.layouts.app>
