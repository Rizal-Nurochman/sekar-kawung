<x-admin.layouts.app title="Ubah Sumber - Konfigurasi" heading="Ubah sumber" subheading="{{ $source->title }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Sumber', 'url' => route('admin.config.sources.index')],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.sources.update', $source)" method="PUT" :back-route="route('admin.config.sources.index')" submit-label="Simpan perubahan">
        <x-admin.ui.panel title="Detail sumber">
            @include('admin.config.sources._form')
        </x-admin.ui.panel>
    </x-admin.ui.form>
</x-admin.layouts.app>
