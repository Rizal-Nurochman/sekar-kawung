<x-admin.layouts.app title="Ubah Produk - Konfigurasi" heading="Ubah produk" subheading="{{ $product->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Produk', 'url' => route('admin.config.products.index')],
            ['label' => 'Ubah produk'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.form :action="route('admin.config.products.update', $product)" method="PUT" :back-route="route('admin.config.products.index')" submit-label="Simpan perubahan">
        @include('admin.config.products._form')
    </x-admin.ui.form>
</x-admin.layouts.app>
