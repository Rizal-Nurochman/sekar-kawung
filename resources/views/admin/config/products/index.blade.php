<x-admin.layouts.app title="Produk - Konfigurasi" heading="Produk" subheading="Batas sistem di dalam skema.">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Produk'],
        ]" />
    </x-slot:breadcrumbs>

    <x-admin.ui.data-shell
        title="Produk"
        :count="$products->total()"
        count-label="produk"
        create-label="Tambah produk"
        :create-route="route('admin.config.products.create')"
    >
        @if ($products->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada produk"
                description="Produk menandai batas sistem, misalnya kain mentah atau kain batik. Kalkulator memakai urutannya untuk menggabungkan tahap."
            >
                <x-slot:icon>
                    <x-lucide-workflow />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.config.products.create')" variant="primary" size="sm">Tambah produk</x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Skema</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="w-16 text-right">Urutan</th>
                            <th scope="col" class="w-24"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="text-[13px] text-ink-soft">{{ $product->scheme?->description ?? '-' }}</td>
                                <td class="font-medium text-pine-900">{{ $product->description }}</td>
                                <td class="cell-num font-normal text-ink-muted">{{ $product->sort_order }}</td>
                                <td>
                                    <x-admin.ui.row-actions
                                        :label="$product->description"
                                        :edit-route="route('admin.config.products.edit', $product)"
                                        :delete-route="route('admin.config.products.destroy', $product)"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:footer>
            {{ $products->links() }}
        </x-slot:footer>
    </x-admin.ui.data-shell>
</x-admin.layouts.app>
