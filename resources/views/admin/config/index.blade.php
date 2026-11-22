@php
    $groups = [
        [
            'title' => 'Struktur perhitungan',
            'description' => 'Urutan data yang membentuk satu perhitungan, dari skema sampai aktivitas.',
            'items' => $structure,
            'numbered' => true,
        ],
        [
            'title' => 'Data referensi',
            'description' => 'Nilai dan sumber yang dipakai berulang oleh aktivitas dan opsi.',
            'items' => $reference,
            'numbered' => false,
        ],
    ];
@endphp

<x-admin.layouts.app title="Konfigurasi - Admin Sekar Kawung" heading="Konfigurasi" subheading="Data master yang dipakai kalkulator dan laporan.">
    <x-slot:actions>
        @if ($missingFactors > 0)
            <x-admin.ui.button :href="route('admin.config.activities.index')">
                <x-lucide-list-checks />
                Tinjau aktivitas
            </x-admin.ui.button>
        @endif
    </x-slot:actions>

    <p class="max-w-[75ch] text-[13px] leading-6 text-ink-soft">
        Perubahan di sini langsung dipakai kalkulator. Data yang sudah dipakai laporan tidak bisa dihapus, dan melepas satuan atau faktor emisi dari aktivitas akan mengosongkannya.
    </p>

    @if ($missingFactors > 0)
        <div class="mt-4 flex flex-wrap items-start gap-2.5 rounded-panel border border-amber-300/60 bg-amber-50/70 px-4 py-3">
            <x-lucide-triangle-alert class="mt-0.5 text-amber-700" />
            <p class="min-w-0 flex-1 text-[13px] leading-6 text-amber-900">
                <span class="font-semibold">{{ $missingFactors }} aktivitas belum punya faktor emisi atau opsi.</span>
                Aktivitas seperti ini selalu menghasilkan 0 kg CO2e di kalkulator.
            </p>
            <a href="{{ route('admin.config.activities.index') }}" class="text-[13px] font-semibold text-amber-900 hover:underline">Tinjau</a>
        </div>
    @endif

    <div class="mt-6 space-y-6">
        @foreach ($groups as $group)
            <section aria-labelledby="config-{{ $loop->index }}">
                <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                    <h2 id="config-{{ $loop->index }}" class="text-sm font-semibold text-pine-900">{{ $group['title'] }}</h2>
                    <p class="text-[13px] text-ink-muted">{{ $group['description'] }}</p>
                </div>

                <ul class="mt-2 divide-y divide-line rounded-panel border border-line bg-white">
                    @foreach ($group['items'] as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="group flex items-center gap-3 px-4 py-3 transition-colors hover:bg-sage-50/60">
                                @if ($group['numbered'])
                                    <span class="w-4 shrink-0 text-xs font-semibold tabular-nums text-ink-muted/70">{{ $loop->iteration }}</span>
                                @endif

                                <span class="flex size-8 shrink-0 items-center justify-center rounded-control bg-sage-50 text-sage-600">
                                    <x-dynamic-component :component="'lucide-'.$item['icon']" class="size-4" />
                                </span>

                                <div class="min-w-0 flex-1">
                                    <p class="text-[13.5px] font-semibold text-pine-900">{{ $item['name'] }}</p>
                                    <p class="mt-0.5 truncate text-xs leading-5 text-ink-muted">{{ $item['description'] }}</p>
                                </div>

                                <span class="shrink-0 text-xs tabular-nums text-ink-muted">{{ $item['count'] }} {{ $item['unit'] }}</span>
                                <x-lucide-chevron-right class="size-4 shrink-0 text-ink-muted/50 transition-transform group-hover:translate-x-0.5 group-hover:text-ink-soft" />
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>

    <section class="mt-6" aria-labelledby="config-links">
        <h2 id="config-links" class="text-sm font-semibold text-pine-900">Relasi aktivitas-opsi</h2>
        <p class="mt-0.5 text-[13px] text-ink-muted">Opsi mana yang tersedia untuk tiap aktivitas. Dibuka dari daftar aktivitas.</p>
        <a href="{{ route('admin.config.activities.index') }}" class="mt-2 inline-flex items-center gap-1.5 text-[13px] font-semibold text-pine-900 hover:underline">
            Pilih aktivitas
            <x-lucide-arrow-right class="size-3.5" />
        </a>
    </section>
</x-admin.layouts.app>
