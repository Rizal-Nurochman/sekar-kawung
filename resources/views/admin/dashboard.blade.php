@php
    $hotspotTotal = (float) $hotspots->sum('total');
@endphp

<x-admin.layouts.app title="Dashboard - Admin Sekar Kawung" heading="Dashboard" subheading="Ringkasan laporan keberlanjutan dan data master.">
    <x-slot:actions>
        <x-admin.ui.button :href="route('admin.reports.index')">
            <x-lucide-file-text />
            Lihat laporan
        </x-admin.ui.button>
    </x-slot:actions>

    <section aria-label="Ringkasan" class="rounded-panel border border-line bg-white">
        <dl class="grid grid-cols-1 divide-y divide-line sm:grid-cols-3 sm:divide-x sm:divide-y-0">
            <div class="px-5 py-4">
                <x-admin.ui.stat
                    label="Emisi terbit"
                    :value="number_format($totalEmission, 2)"
                    unit="kg CO2e"
                    :meta="$publishedReports.' laporan terbit'"
                />
            </div>
            <div class="px-5 py-4">
                <x-admin.ui.stat
                    label="Laporan"
                    :value="$totalReports"
                    :meta="$draftReports.' draf menunggu ditinjau'"
                />
            </div>
            <div class="px-5 py-4">
                <x-admin.ui.stat
                    label="Data master"
                    :value="$activityCount"
                    unit="aktivitas"
                    :meta="$schemeCount.' skema · '.$optionCount.' opsi'"
                />
            </div>
        </dl>
    </section>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-5">
        <x-admin.ui.panel
            class="lg:col-span-3"
            title="Laporan terbaru"
            :description="$totalReports === 0 ? null : 'Lima laporan terakhir yang dibuat.'"
            flush
        >
            <x-slot:actions>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-1 text-[13px] font-semibold text-pine-900 hover:underline">
                    Lihat semua
                    <x-lucide-arrow-right class="size-3.5" />
                </a>
            </x-slot:actions>

            @if ($recentReports->isEmpty())
                <x-admin.ui.empty-state
                    title="Belum ada laporan"
                    description="Laporan dibuat dari kalkulator: hitung jejak karbon satu batch, lalu simpan hasilnya sebagai laporan."
                >
                    <x-slot:icon>
                        <x-lucide-file-plus-2 />
                    </x-slot:icon>
                    <x-slot:action>
                        <x-admin.ui.button :href="route('admin.calculator.edit')" variant="primary" size="sm">
                            <x-lucide-calculator />
                            Hitung di kalkulator
                        </x-admin.ui.button>
                    </x-slot:action>
                </x-admin.ui.empty-state>
            @else
                <ul class="divide-y divide-line">
                    @foreach ($recentReports as $report)
                        <li>
                            <a href="{{ route('admin.reports.show', $report) }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-sage-50/60">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13.5px] font-semibold text-pine-900">{{ $report->title }}</p>
                                    <p class="mt-0.5 truncate text-xs text-ink-muted">
                                        {{ $report->scheme?->description ?? 'Tanpa skema' }}
                                        @if ($report->process)
                                            <span aria-hidden="true">·</span> {{ $report->process->description }}
                                        @endif
                                    </p>
                                </div>
                                @if ($report->is_published)
                                    <x-admin.ui.badge tone="published">Terbit</x-admin.ui.badge>
                                @else
                                    <x-admin.ui.badge tone="draft">Draf</x-admin.ui.badge>
                                @endif
                                <p class="w-24 shrink-0 text-right text-[13.5px] font-semibold tabular-nums text-pine-900">{{ number_format((float) $report->total_emission, 2) }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-admin.ui.panel>

        <x-admin.ui.panel
            class="lg:col-span-2"
            title="Hotspot emisi"
            description="Aktivitas penyumbang emisi terbesar dari seluruh item laporan."
            flush
        >
            @if ($hotspots->isEmpty())
                <x-admin.ui.empty-state
                    title="Belum ada data emisi"
                    description="Hotspot muncul setelah laporan berisi item perhitungan tersimpan."
                >
                    <x-slot:icon>
                        <x-lucide-chart-column />
                    </x-slot:icon>
                </x-admin.ui.empty-state>
            @else
                <ol class="divide-y divide-line">
                    @foreach ($hotspots as $hotspot)
                        @php($share = $hotspotTotal > 0 ? (float) $hotspot->total / $hotspotTotal * 100 : 0)
                        <li class="flex items-start gap-3 px-5 py-3">
                            <span class="mt-0.5 w-4 shrink-0 text-xs font-semibold tabular-nums text-ink-muted/70">{{ $loop->iteration }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13.5px] font-medium text-pine-900">{{ $hotspot->activity?->description ?? 'Aktivitas '.$hotspot->activity_id }}</p>
                                <p class="mt-0.5 text-xs tabular-nums text-ink-muted">{{ number_format($share, 1) }}% dari total hotspot</p>
                            </div>
                            <p class="shrink-0 text-[13.5px] font-semibold tabular-nums text-pine-900">{{ number_format((float) $hotspot->total, 2) }}</p>
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-admin.ui.panel>
    </div>
</x-admin.layouts.app>
