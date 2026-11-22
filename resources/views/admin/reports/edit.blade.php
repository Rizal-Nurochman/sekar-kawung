<x-admin.layouts.app title="Ubah laporan - Admin Sekar Kawung" heading="Ubah laporan" subheading="{{ $report->title }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Laporan', 'url' => route('admin.reports.index')],
            ['label' => $report->title, 'url' => route('admin.reports.show', $report)],
            ['label' => 'Ubah'],
        ]" />
    </x-slot:breadcrumbs>

    <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="pb-20">
        @method('PUT')
        @include('admin.reports._form')

        <div class="fixed inset-x-0 bottom-0 z-20 border-t border-line bg-white/95 backdrop-blur lg:left-60">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <p class="hidden text-[13px] text-ink-muted sm:block">Cakupan perhitungan tidak dapat diubah dari sini.</p>
                <div class="flex w-full items-center justify-end gap-2 sm:w-auto">
                    <x-admin.ui.button :href="route('admin.reports.show', $report)">Batal</x-admin.ui.button>
                    <x-admin.ui.button type="submit" variant="primary">
                        <x-lucide-check />
                        Simpan perubahan
                    </x-admin.ui.button>
                </div>
            </div>
        </div>
    </form>
</x-admin.layouts.app>
