@php
    $totalItems = $report->items->count();
    $reportSubheading = $report->scheme?->description;

    if ($report->boundary) {
        $reportSubheading = trim(($reportSubheading ? $reportSubheading.' · ' : '').$report->boundary->description);
    }
@endphp

<x-admin.layouts.app :title="$report->title.' - Admin Sekar Kawung'" :heading="$report->title" :subheading="$reportSubheading">
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

    <x-admin.ui.panel>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                @if ($report->is_published)
                    <x-admin.ui.badge tone="published">Terbit</x-admin.ui.badge>
                @else
                    <x-admin.ui.badge tone="draft">Draf</x-admin.ui.badge>
                @endif
                <p class="text-xs tabular-nums text-ink-muted">{{ $totalItems }} item</p>
            </div>
            <p class="text-[13px] text-ink-muted">Diperbarui <span class="text-ink-soft">{{ $report->updated_at?->translatedFormat('j F Y, H:i') }}</span></p>
        </div>

        @if ($report->summary)
            <p class="mt-4 max-w-[70ch] border-t border-line pt-4 text-[13.5px] leading-6 text-ink-soft">{{ $report->summary }}</p>
        @endif
    </x-admin.ui.panel>

    <div x-data="{ activeScheme: {{ $schemes->first()['id'] ?? 'null' }} }">
    <div
        class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2"
        role="tablist"
        aria-label="Pilih skema"
    >
        @foreach ($schemes as $scheme)
            <section aria-labelledby="skema-{{ $scheme['id'] }}-heading">
                <button
                    type="button"
                    role="tab"
                    x-on:click="activeScheme = {{ $scheme['id'] }}"
                    x-bind:aria-selected="activeScheme === {{ $scheme['id'] }}"
                    x-bind:class="activeScheme === {{ $scheme['id'] }} ? 'border-pine-900 ring-1 ring-pine-900/20' : ''"
                    data-scheme-card="{{ $scheme['id'] }}"
                    class="block w-full rounded-panel border border-line bg-white px-5 py-4 text-left"
                >
                    <span class="block border-t-2 border-pine-900 pt-3">
                        <span id="skema-{{ $scheme['id'] }}-heading" class="block text-base font-bold leading-6 tracking-tight text-pine-900">{{ $scheme['name'] }}</span>
                        <span class="mt-1 flex flex-wrap items-baseline justify-between gap-2">
                            <span class="text-[13px] leading-5 text-ink-muted">{{ $scheme["boundaries"]->count() }} boundary · {{ $scheme['filled'] }} dari {{ $scheme['count'] }} aktivitas terisi</span>
                            <span class="text-xl font-bold tabular-nums tracking-tight text-pine-900">{{ \App\Support\Number::emissionId($scheme['total']) }} <span class="text-[12px] font-medium text-ink-muted">kg CO2e</span></span>
                        </span>
                    </span>
                </button>
            </section>
        @endforeach
    </div>

    @foreach ($schemes as $scheme)
        <section aria-labelledby="skema-{{ $scheme['id'] }}-detail-heading" id="skema-{{ $scheme['id'] }}-detail" class="mt-10" x-show="activeScheme === {{ $scheme['id'] }}" x-cloak x-transition.opacity.duration.200ms>
            <h2 id="skema-{{ $scheme['id'] }}-detail-heading" class="text-xl font-bold leading-7 tracking-tight text-pine-900">
                {{ $scheme['name'] }}
            </h2>

            <div class="mt-4 space-y-8">
                @foreach ($scheme["boundaries"] as $boundary)
                    @php
                        $position = $boundaries->search(fn ($item) => (int) $item['id'] === (int) $boundary['id']) + 1;
                        $boundaryTotal = $boundaries->count();
                    @endphp
                    <section aria-labelledby="proses-{{ $boundary['id'] }}">
                        <h3 id="proses-{{ $boundary['id'] }}" class="text-lg font-bold leading-7 tracking-tight text-pine-900">
                            Boundary {{ $position }} dari {{ $boundaryTotal }} : {{ $boundary['description'] }}
                        </h3>
                        <p class="mt-3 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 rounded-panel bg-pine-900 px-4 py-3">
                            <span class="text-[13px] font-semibold text-white">Subtotal boundary</span>
                            <span class="text-xl font-bold tabular-nums tracking-tight text-white">{{ \App\Support\Number::emissionId($boundary['total']) }} <span class="text-[12px] font-medium text-sage-pale">kg CO2e</span></span>
                        </p>

                            @if ($boundary['items']->isEmpty())
                                <x-admin.ui.panel class="mt-4" flush>
                                    <x-admin.ui.empty-state
                                        title="Belum ada isian pada proses ini"
                                        description="Boundary ini belum punya kuantitas tersimpan pada laporan ini."
                                    >
                                        <x-slot:icon>
                                            <x-lucide-list-tree />
                                        </x-slot:icon>
                                    </x-admin.ui.empty-state>
                                </x-admin.ui.panel>
                            @else
                                <x-admin.ui.panel class="mt-4" title="Rincian per tahap" :description="'Kontribusi emisi tiap subproses pada '.$boundary['description'].'.'" flush>
                                    <table class="data-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Tahap</th>
                                                <th scope="col" class="text-right">Item</th>
                                                <th scope="col" class="text-right">Emisi (kg CO2e)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($boundary['stages'] as $stage)
                                                <tr>
                                                    <td class="font-medium text-pine-900">{{ $stage['name'] }}</td>
                                                    <td class="cell-num font-normal text-ink-muted">{{ $stage['count'] }}</td>
                                                    <td class="cell-num">{{ \App\Support\Number::emissionId($stage['total']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </x-admin.ui.panel>

                                <x-admin.ui.panel class="mt-4" title="Rincian aktivitas" :description="$boundary['count'].' baris perhitungan.'" flush>
                                    <div class="overflow-x-auto">
                                        <table class="data-table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Aktivitas</th>
                                                    <th scope="col">Kuantitas</th>
                                                    <th scope="col">Faktor</th>
                                                    <th scope="col" class="text-right">Emisi (kg CO2e)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($boundary['items'] as $item)
                                                    @php
                                                        $factor = $item->option?->emissionFactor?->ef_value ?? $item->activity?->emissionFactor?->ef_value ?? 0;
                                                        $source = $item->option?->emissionFactor?->source ?? $item->activity?->emissionFactor?->source;
                                                        $unit = $item->option?->activityUnit?->unit_name ?? $item->activity?->activityUnit?->unit_name ?? '';
                                                        $isHeritage = $item->activity?->emissionFactor === null && ($item->activity?->options->isEmpty() ?? true);
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <p class="text-[13.5px] font-medium text-pine-900">{{ $item->activity?->description ?? 'Aktivitas #'.$item->activity_id }}</p>
                                                            <p class="mt-0.5 text-xs leading-4 text-ink-muted">{{ $item->activity?->process?->description }}</p>
                                                            @if ($item->option)
                                                                <p class="mt-0.5 text-xs leading-4 text-ink-muted">Opsi: {{ $item->option->description }}</p>
                                                            @elseif ($isHeritage)
                                                                <p class="mt-0.5 text-xs leading-4 text-ink-muted">Tahap manual tanpa emisi</p>
                                                            @endif
                                                        </td>
                                                        <td class="whitespace-nowrap text-[13px] tabular-nums text-ink-soft">{{ \App\Support\Number::quantity($item->quantity) }}{{ $unit !== '' ? ' '.$unit : '' }}</td>
                                                        <td class="whitespace-nowrap text-[13px] tabular-nums text-ink-muted">
                                                            {{ \App\Support\Number::emissionId($factor) }}
                                                            @if ($source)
                                                                <span class="block max-w-40 truncate text-xs">{{ $source->title }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="cell-num">{{ \App\Support\Number::emissionId($item->emission_result) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </x-admin.ui.panel>
                            @endif
                        </section>
                    @endforeach
            </div>
        </section>
    @endforeach
    </div>

    @if ($sources->isNotEmpty())
        <x-admin.ui.panel class="mt-8" flush>
            <details class="group">
                <summary class="flex cursor-pointer list-none items-start justify-between gap-3 px-5 py-3.5 [&::-webkit-details-marker]:hidden">
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-pine-900">Sumber faktor emisi</span>
                        <span class="mt-0.5 block text-[13px] leading-5 text-ink-muted">Referensi yang dipakai pada perhitungan ini. Klik untuk melihat daftar.</span>
                    </span>
                    <x-lucide-chevron-down class="mt-1 size-4 shrink-0 text-ink-muted transition-transform group-open:rotate-180" />
                </summary>
                <ul class="divide-y divide-line border-t border-line px-5">
                    @foreach ($sources as $source)
                        <li class="flex flex-wrap items-center justify-between gap-2 py-2.5">
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
            </details>
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
