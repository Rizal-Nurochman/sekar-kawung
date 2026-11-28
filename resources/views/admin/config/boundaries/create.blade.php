<x-admin.layouts.app title="Tambah Boundary - Konfigurasi" heading="Tambah boundary" subheading="Tentukan batas sistem di dalam sebuah skema.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Boundary', 'url' => route('admin.config.boundaries.index')],
            ['label' => 'Tambah boundary'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.boundaries.store')" :back-route="route('admin.config.boundaries.index')">
        @include('admin.config.boundaries._form')
    </x-admin.ui.form>
</x-admin.layouts.app>
