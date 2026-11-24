<x-admin.layouts.app title="Tambah Produk - Konfigurasi" heading="Tambah produk" subheading="Tentukan batas sistem di dalam sebuah skema.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Produk', 'url' => route('admin.config.products.index')],
            ['label' => 'Tambah produk'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.products.store')" :back-route="route('admin.config.products.index')">
        @include('admin.config.products._form')
    </x-admin.ui.form>
</x-admin.layouts.app>
