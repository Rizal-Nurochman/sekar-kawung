<x-admin.layouts.app title="Relasi Opsi - Konfigurasi" heading="Opsi untuk aktivitas" subheading="{{ $activity->description }}">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Konfigurasi', 'url' => route('admin.config.index')],
            ['label' => 'Aktivitas', 'url' => route('admin.config.activities.index')],
            ['label' => $activity->description, 'url' => route('admin.config.activities.edit', $activity)],
            ['label' => 'Opsi'],
        ]" />
    </x-slot:breadcrumbs>

    <form method="POST" action="{{ route('admin.config.activity-options.update', $activity) }}" class="pb-20">
        @csrf
        @method('PUT')

        <x-admin.ui.errors class="mb-5" />

        <div class="max-w-2xl space-y-5">
            <x-admin.ui.panel title="Opsi tersedia" description="Centang opsi yang muncul di kalkulator untuk aktivitas ini.">
                @if ($options->isEmpty())
                    <x-admin.ui.empty-state
                        title="Belum ada opsi"
                        description="Tambahkan opsi terlebih dahulu, lalu kembali ke halaman ini untuk memasangnya."
                    >
                        <x-slot:icon>
                            <x-lucide-circle-dot />
                        </x-slot:icon>
                        <x-slot:action>
                            <x-admin.ui.button :href="route('admin.config.options.create')" variant="primary" size="sm">Tambah opsi</x-admin.ui.button>
                        </x-slot:action>
                    </x-admin.ui.empty-state>
                @else
                    @php($selectedIds = collect(old('option_ids', $selected))->map(fn ($id) => (int) $id)->all())
                    <div class="space-y-2">
                        @foreach ($options as $option)
                            <x-admin.ui.check-option
                                :label="$option->description"
                                name="option_ids"
                                :value="$option->id"
                                :checked="in_array($option->id, $selectedIds, true)"
                                :meta="$option->emissionFactor ? $option->emissionFactor->ef_value.' kg CO2e / '.($option->activityUnit?->unit_name ?? 'satuan') : 'Tanpa faktor emisi'"
                            />
                        @endforeach
                    </div>
                @endif
            </x-admin.ui.panel>
        </div>

        <div class="fixed inset-x-0 bottom-0 z-20 border-t border-line bg-white/95 backdrop-blur lg:left-60">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-end gap-2 px-4 py-3 sm:px-6">
                <x-admin.ui.button :href="route('admin.config.activities.index')">Batal</x-admin.ui.button>
                <x-admin.ui.button type="submit" variant="primary">
                    <x-lucide-check />
                    Simpan relasi
                </x-admin.ui.button>
            </div>
        </div>
    </form>
</x-admin.layouts.app>
