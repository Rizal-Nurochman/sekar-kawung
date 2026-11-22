@php
    $totalItems = $report->items->count();
@endphp

<x-admin.layouts.app title="{{ $report->title }} - Admin Sekar Kawung" heading="{{ $report->title }}" subheading="{{ $report->scheme?->description }}@if ($report->process) · {{ $report->process->description }}@endif">
    <x-slot:breadcrumbs>
        <x-admin.ui.breadcrumbs :items="[
            ['label' => 'Laporan', 'url' => route('admin.reports.index')],
            ['label' => $report->title],
        ]" class="mb-0" />
    </x-slot:breadcrumbs>

    <x-slot:actions>
        <x-admin.ui.button :href="route('admin.reports.edit', $report)" size="sm">
            <x-lucide-pencil />
            Ubah
        </x-admin.ui.button>
        @if ($report->is_published)
            <form method="POST" action="{{ route('admin.reports.unpublish', $report) }}">
                @csrf
                @method('DELETE')
                <x-admin.ui.button type="submit" size="sm">
                    <x-lucide-eye-off />
                    Jadikan draf
                </x-admin.ui.button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.reports.publish', $report) }}">
                @csrf
                <x-admin.ui.button type="submit" variant="primary" size="sm">
                    <x-lucide-send />
                    Terbitkan
                </x-admin.ui.button>
            </form>
        @endif
    </x-slot:actions>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-admin.ui.panel class="lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-[13px] font-medium text-ink-muted">Total emisi</p>
                    <p class="mt-1 flex flex-wrap items-baseline gap-x-2">
                        <span class="text-[34px] font-bold leading-10 tracking-tight tabular-nums text-pine-900">{{ number_format((float) $report->total_emission, 2) }}</span>
                        <span class="text-sm font-medium text-ink-muted">kg CO2e</span>
                    </p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    @if ($report->is_published)
                        <x-admin.ui.badge tone="published">Terbit</x-admin.ui.badge>
                    @else
                        <x-admin.ui.badge tone="draft">Draf</x-admin.ui.badge>
                    @endif
                    <p class="text-xs tabular-nums text-ink-muted">{{ $totalItems }} item</p>
                </div>
            </div>

            @if ($report->summary)
                <p class="mt-4 max-w-[70ch] border-t border-line pt-4 text-[13.5px] leading-6 text-ink-soft">{{ $report->summary }}</p>
            @endif

            <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-3 border-t border-line pt-4 text-[13px] sm:grid-cols-2">
                <div class="flex items-baseline justify-between gap-3 sm:block">
                    <dt class="text-ink-muted">Slug</dt>
                    <dd class="truncate font-mono text-xs text-ink-soft sm:mt-1">{{ $report->slug }}</dd>
                </div>
                <div class="flex items-baseline justify-between gap-3 sm:block">
                    <dt class="text-ink-muted">Diperbarui</dt>
                    <dd class="text-ink-soft sm:mt-1">{{ $report->updated_at?->translatedFormat('j F Y, H:i') }}</dd>
                </div>
            </dl>
        </x-admin.ui.panel>

        <x-admin.ui.panel title="Hotspot emisi">
            @if (! $hotspot)
                <p class="text-[13px] leading-6 text-ink-muted">Belum ada item untuk diringkas.</p>
            @else
                <p class="text-[13.5px] font-semibold text-pine-900">{{ $hotspot->activity?->description ?? 'Aktivitas #'.$hotspot->activity_id }}</p>
                <p class="mt-0.5 text-xs text-ink-muted">
                    {{ $hotspot->option?->description ?? 'Nilai dasar aktivitas' }}
                    <span aria-hidden="true">·</span> kuantitas {{ number_format((float) $hotspot->quantity, 2) }}
                </p>
                <p class="mt-3 flex flex-wrap items-baseline gap-x-1.5">
                    <span class="text-2xl font-bold tabular-nums tracking-tight text-pine-900">{{ number_format((float) $hotspot->emission_result, 2) }}</span>
                    <span class="text-[13px] font-medium text-ink-muted">kg CO2e</span>
                </p>
                @if ($totalItems > 1)
                    <p class="mt-1 text-xs text-ink-muted">Item terbesar dari {{ $totalItems }} item.</p>
                @endif
            @endif
        </x-admin.ui.panel>
    </div>

    <x-admin.ui.panel class="mt-6" title="Rincian per tahap" description="Kontribusi emisi tiap subproses." flush>
        @if ($stages->isEmpty())
            <x-admin.ui.empty-state
                title="Belum ada item"
                description="Item laporan dihitung dari kalkulator dan disimpan bersama laporan."
            >
                <x-slot:icon>
                    <x-lucide-list-tree />
                </x-slot:icon>
                <x-slot:action>
                    <x-admin.ui.button :href="route('admin.calculator.edit', ['scheme_id' => $report->scheme_id, 'process_id' => $report->process_id])" size="sm">
                        <x-lucide-calculator />
                        Hitung di kalkulator
                    </x-admin.ui.button>
                </x-slot:action>
            </x-admin.ui.empty-state>
        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Tahap</th>
                        <th scope="col" class="text-right">Item</th>
                        <th scope="col" class="text-right">Emisi (kg CO2e)</th>
                        <th scope="col" class="w-28 text-right">Porsi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stages as $stage)
                        <tr>
                            <td class="font-medium text-pine-900">{{ $stage['name'] }}</td>
                            <td class="cell-num font-normal text-ink-muted">{{ $stage['count'] }}</td>
                            <td class="cell-num">{{ number_format($stage['total'], 2) }}</td>
                            <td class="cell-num font-normal text-ink-muted">{{ $stageTotal > 0 ? number_format($stage['total'] / $stageTotal * 100, 1) : '0.0' }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-admin.ui.panel>

    <x-admin.ui.panel class="mt-6" title="Item laporan" :description="$totalItems > 0 ? $totalItems.' baris perhitungan.' : null" flush>
        @if ($report->items->isEmpty())
            <p class="px-5 py-4 text-[13px] leading-6 text-ink-muted">Belum ada item pada laporan ini.</p>
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Aktivitas</th>
                            <th scope="col">Opsi</th>
                            <th scope="col" class="text-right">Kuantitas</th>
                            <th scope="col" class="text-right">Emisi (kg CO2e)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report->items as $item)
                            <tr>
                                <td class="text-[13.5px] text-pine-900">{{ $item->activity?->description ?? 'Aktivitas #'.$item->activity_id }}</td>
                                <td class="text-[13px] text-ink-soft">{{ $item->option?->description ?? '-' }}</td>
                                <td class="cell-num font-normal text-ink-soft">{{ number_format((float) $item->quantity, 2) }}</td>
                                <td class="cell-num">{{ number_format((float) $item->emission_result, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-admin.ui.panel>

    @if ($sources->isNotEmpty())
        <x-admin.ui.panel class="mt-6" title="Sumber faktor emisi" description="Referensi yang dipakai pada perhitungan ini.">
            <ul class="divide-y divide-line">
                @foreach ($sources as $source)
                    <li class="flex flex-wrap items-center justify-between gap-2 py-2.5 first:pt-0 last:pb-0">
                        <span class="text-[13.5px] text-ink-soft">{{ $source->title }}</span>
                        @if ($source->link_reference)
                            <a href="{{ $source->link_reference }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-[13px] font-semibold text-pine-900 hover:underline">
                                Referensi
                                <x-lucide-external-link class="size-3.5" />
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </x-admin.ui.panel>
    @endif

    <x-admin.ui.panel class="mt-6" title="Zona berbahaya">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <p class="max-w-[60ch] text-[13px] leading-6 text-ink-muted">Menghapus laporan juga menghapus seluruh item perhitungan di dalamnya. Tindakan ini tidak dapat dibatalkan.</p>
            <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" onsubmit="return confirm('Hapus laporan ini beserta seluruh itemnya?')">
                @csrf
                @method('DELETE')
                <x-admin.ui.button type="submit" variant="danger" size="sm">
                    <x-lucide-trash-2 />
                    Hapus laporan
                </x-admin.ui.button>
            </form>
        </div>
    </x-admin.ui.panel>
</x-admin.layouts.app>
