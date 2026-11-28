@php
    $report ??= null;
@endphp

<x-admin.ui.errors class="mb-5" />

@csrf

<div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
    <div class="space-y-5">
        <x-admin.ui.panel title="Isi laporan">
            <div class="space-y-4">
                <x-admin.ui.field label="Judul laporan" name="title" required>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title', $report->title) }}"
                        required
                        maxlength="255"
                        placeholder="Contoh: Jejak Karbon Batik Tulis 2026"
                        class="control"
                        @error('title') aria-invalid="true" aria-describedby="title-error" @enderror
                    >
                </x-admin.ui.field>

                <x-admin.ui.field label="Ringkasan" name="summary" hint="Opsional">
                    <textarea id="summary" name="summary" rows="5" class="control" placeholder="Catatan editorial yang tampil di halaman laporan publik.">{{ old('summary', $report->summary) }}</textarea>
                </x-admin.ui.field>
            </div>
        </x-admin.ui.panel>

        <x-admin.ui.panel title="Terbitkan" description="Laporan terbit tampil di katalog publik.">
            <label class="flex cursor-pointer items-start gap-3">
                <input
                    type="checkbox"
                    name="is_published"
                    id="is_published"
                    value="1"
                    @checked((bool) old('is_published', $report->is_published ?? false))
                    class="mt-0.5 size-4 rounded border-line-strong accent-pine-900"
                >
                <span class="min-w-0">
                    <span class="block text-[13.5px] font-medium text-pine-900">Terbitkan laporan</span>
                    <span class="mt-0.5 block text-[13px] leading-5 text-ink-muted">Kosongkan untuk menyimpan sebagai draf yang hanya terlihat oleh admin.</span>
                </span>
            </label>
        </x-admin.ui.panel>
    </div>

    <aside class="space-y-4">
        <x-admin.ui.panel title="Cakupan perhitungan" description="Ditetapkan saat laporan dibuat dari kalkulator.">
            <dl class="space-y-3 text-[13px]">
                <div>
                    <dt class="text-ink-muted">Skema</dt>
                    <dd class="mt-0.5 font-medium text-pine-900">{{ $report->scheme?->description ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-ink-muted">Batas sistem</dt>
                    <dd class="mt-0.5 font-medium text-pine-900">{{ $report->boundary?->description ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-ink-muted">Total emisi</dt>
                    <dd class="mt-0.5 font-semibold tabular-nums text-pine-900">{{ number_format((float) $report->total_emission, 2) }} kg CO2e</dd>
                </div>
                <div>
                    <dt class="text-ink-muted">Item</dt>
                    <dd class="mt-0.5 font-medium tabular-nums text-pine-900">{{ $report->items_count ?? $report->items()->count() }} baris</dd>
                </div>
            </dl>
            <p class="mt-3 border-t border-line pt-3 text-xs leading-5 text-ink-muted">Untuk mengubah cakupan atau nilai perhitungan, buat laporan baru dari kalkulator.</p>
        </x-admin.ui.panel>
    </aside>
</div>
