@php
    $tabs = [
        'all' => ['label' => 'Semua', 'count' => $totalReports, 'url' => route('admin.reports.index', array_filter(['q' => $search]))],
        'published' => ['label' => 'Terbit', 'count' => $publishedReports, 'url' => route('admin.reports.index', array_filter(['status' => 'published', 'q' => $search]))],
        'draft' => ['label' => 'Draf', 'count' => $draftReports, 'url' => route('admin.reports.index', array_filter(['status' => 'draft', 'q' => $search]))],
    ];
    $activeTab = $status ?? 'all';
    $isFiltered = $status !== null || $search !== '';
@endphp

<x-admin.layouts.app title="Laporan - Admin Sekar Kawung" heading="Laporan" subheading="Kelola laporan keberlanjutan dan status terbitnya.">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <nav class="flex gap-1" aria-label="Filter status laporan">
            @foreach ($tabs as $key => $tab)
                <a
                    href="{{ $tab['url'] }}"
                    @if ($activeTab === $key) aria-current="page" @endif
                    @class([
                        'inline-flex items-center gap-1.5 rounded-control px-3 py-1.5 text-[13px] font-semibold transition-colors',
                        'bg-pine-900 text-white' => $activeTab === $key,
                        'text-ink-soft hover:bg-pine-900/[0.06]' => $activeTab !== $key,
                    ])
                >
                    {{ $tab['label'] }}
                    <span @class(['tabular-nums text-[11px]', 'text-white/70' => $activeTab === $key, 'text-ink-muted' => $activeTab !== $key])>{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2" role="search">
            @if ($status)
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
            <div class="relative">
                <x-lucide-search class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-ink-muted" />
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari judul laporan"
                    aria-label="Cari judul laporan"
                    class="control h-9 w-56 pl-8"
                >
            </div>
            <x-admin.ui.button type="submit" size="sm">Cari</x-admin.ui.button>
        </form>
    </div>

    <x-admin.ui.panel class="mt-4" flush>
        @if ($reports->isEmpty())
            @if ($isFiltered)
                <x-admin.ui.empty-state
                    title="Tidak ada laporan yang cocok"
                    description="Coba kata kunci lain atau tampilkan semua status."
                >
                    <x-slot:icon>
                        <x-lucide-search />
                    </x-slot:icon>
                    <x-slot:action>
                        <x-admin.ui.button :href="route('admin.reports.index')" size="sm">Hapus filter</x-admin.ui.button>
                    </x-slot:action>
                </x-admin.ui.empty-state>
            @else
                <x-admin.ui.empty-state
                    title="Belum ada laporan"
                    description="Laporan dibuat dari kalkulator: hitung jejak karbon satu batch, lalu simpan hasilnya sebagai laporan."
                >
                    <x-slot:icon>
                        <x-lucide-file-text />
                    </x-slot:icon>
                    <x-slot:action>
                        <x-admin.ui.button :href="route('admin.calculator.edit')" variant="primary" size="sm">
                            <x-lucide-calculator />
                            Hitung di kalkulator
                        </x-admin.ui.button>
                    </x-slot:action>
                </x-admin.ui.empty-state>
            @endif
        @else
            <div class="hidden overflow-x-auto md:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">Laporan</th>
                            <th scope="col">Skema</th>
                            <th scope="col">Status</th>
                            <th scope="col">Diperbarui</th>
                            <th scope="col" class="text-right">Total emisi</th>
                            <th scope="col" class="w-10"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td class="max-w-[26rem]">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="block truncate font-semibold text-pine-900 hover:underline">{{ $report->title }}</a>
                                    <p class="mt-0.5 truncate text-xs text-ink-muted">{{ $report->slug }}</p>
                                </td>
                                <td class="text-[13px] text-ink-soft">
                                    <p>{{ $report->scheme?->description ?? '-' }}</p>
                                    @if ($report->product)
                                        <p class="mt-0.5 text-xs text-ink-muted">{{ $report->product->description }}</p>
                                    @endif
                                </td>
                                <td>
                                    @if ($report->is_published)
                                        <x-admin.ui.badge tone="published">Terbit</x-admin.ui.badge>
                                    @else
                                        <x-admin.ui.badge tone="draft">Draf</x-admin.ui.badge>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-[13px] text-ink-muted">{{ $report->updated_at?->translatedFormat('j M Y') }}</td>
                                <td class="cell-num">{{ number_format((float) $report->total_emission, 2) }}</td>
                                <td>
                                    <div class="flex items-center justify-end">
                                        <a href="{{ route('admin.reports.show', $report) }}" title="Buka {{ $report->title }}" aria-label="Buka {{ $report->title }}" class="inline-flex size-8 items-center justify-center rounded-control text-ink-muted transition-colors hover:bg-pine-900/[0.06] hover:text-pine-900">
                                            <x-lucide-chevron-right class="size-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-line md:hidden">
                @foreach ($reports as $report)
                    <li>
                        <a href="{{ route('admin.reports.show', $report) }}" class="block px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <p class="min-w-0 flex-1 text-[13.5px] font-semibold text-pine-900">{{ $report->title }}</p>
                                <p class="shrink-0 text-[13.5px] font-semibold tabular-nums text-pine-900">{{ number_format((float) $report->total_emission, 2) }}</p>
                            </div>
                            <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-ink-muted">
                                @if ($report->is_published)
                                    <x-admin.ui.badge tone="published">Terbit</x-admin.ui.badge>
                                @else
                                    <x-admin.ui.badge tone="draft">Draf</x-admin.ui.badge>
                                @endif
                                <span class="truncate">{{ $report->scheme?->description ?? '-' }}</span>
                                <span aria-hidden="true">·</span>
                                <span>{{ $report->updated_at?->translatedFormat('j M Y') }}</span>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-admin.ui.panel>

    @if ($reports->hasPages())
        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    @endif
</x-admin.layouts.app>
