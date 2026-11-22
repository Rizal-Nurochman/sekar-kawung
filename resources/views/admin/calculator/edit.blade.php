@php
    $activityTotal = $subprocesses->sum(fn ($subprocess) => $subprocess->activities->count());
    $hasScope = $selectedScheme !== null && $selectedProcess !== null;
@endphp

<x-admin.layouts.app title="Kalkulator - Admin Sekar Kawung" heading="Kalkulator" subheading="Hitung jejak karbon satu batch, lalu simpan hasilnya sebagai laporan.">
    <x-slot:actions>
        <x-admin.ui.button :href="route('admin.reports.index')">
            <x-lucide-file-text />
            Lihat laporan
        </x-admin.ui.button>
    </x-slot:actions>

    <form method="GET" action="{{ route('admin.calculator.edit') }}" class="rounded-panel border border-line bg-white" x-data aria-label="Pilih cakupan perhitungan">
        <div class="grid grid-cols-1 gap-4 px-4 py-3.5 sm:grid-cols-2 sm:px-5">
            <div>
                <label for="scheme_id" class="block text-[13px] font-medium text-pine-900">Skema</label>
                <select
                    id="scheme_id"
                    name="scheme_id"
                    class="control mt-1.5"
                    x-on:change="$refs.process.disabled = true; $el.form.requestSubmit()"
                >
                    @foreach ($schemes as $scheme)
                        <option value="{{ $scheme->id }}" @selected($selectedScheme?->id === $scheme->id)>{{ $scheme->description }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="process_id" class="block text-[13px] font-medium text-pine-900">Batas sistem</label>
                <select
                    id="process_id"
                    name="process_id"
                    x-ref="process"
                    class="control mt-1.5"
                    x-on:change="$el.form.requestSubmit()"
                >
                    @foreach ($schemeProcesses as $process)
                        <option value="{{ $process->id }}" @selected($selectedProcess?->id === $process->id)>{{ $process->description }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <noscript>
            <div class="border-t border-line px-4 py-3 sm:px-5">
                <x-admin.ui.button type="submit" size="sm">Tampilkan</x-admin.ui.button>
            </div>
        </noscript>
    </form>

    @if ($hasScope)
        <form
            id="calculator-form"
            method="POST"
            action="{{ route('admin.calculator.store') }}"
            x-data="adminCalculator()"
            class="mt-6 pb-24 lg:pb-0"
        >
            @csrf
            <input type="hidden" name="scheme_id" value="{{ $selectedScheme->id }}">
            <input type="hidden" name="process_id" value="{{ $selectedProcess->id }}">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_17.5rem]">
                <div class="min-w-0 space-y-6">
                    <x-admin.ui.errors />

                    <x-admin.ui.panel title="Identitas laporan" description="Laporan disimpan dari perhitungan di halaman ini.">
                        <div class="space-y-4">
                            <x-admin.ui.field label="Judul laporan" name="title" required>
                                <input
                                    id="title"
                                    type="text"
                                    name="title"
                                    value="{{ old('title') }}"
                                    required
                                    maxlength="255"
                                    placeholder="Kain Batik Gedog Tuban - Batch 2026"
                                    class="control"
                                    @error('title') aria-invalid="true" aria-describedby="title-error" @enderror
                                >
                            </x-admin.ui.field>

                            <x-admin.ui.field label="Ringkasan" name="summary" hint="Opsional">
                                <textarea id="summary" name="summary" rows="2" class="control" placeholder="Catatan singkat tentang batch, periode produksi, atau metode.">{{ old('summary') }}</textarea>
                            </x-admin.ui.field>
                        </div>
                    </x-admin.ui.panel>

                    @if ($subprocesses->isEmpty())
                        <x-admin.ui.panel flush>
                            <x-admin.ui.empty-state
                                title="Belum ada data inventaris"
                                description="Cakupan ini belum punya subproses atau aktivitas. Tambahkan data master terlebih dahulu, lalu kembali ke sini."
                            >
                                <x-slot:icon>
                                    <x-lucide-database />
                                </x-slot:icon>
                                <x-slot:action>
                                    <x-admin.ui.button :href="route('admin.config.index')" size="sm">
                                        <x-lucide-settings-2 />
                                        Buka konfigurasi
                                    </x-admin.ui.button>
                                </x-slot:action>
                            </x-admin.ui.empty-state>
                        </x-admin.ui.panel>
                    @else
                        <section aria-label="Aktivitas per subproses" class="space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-[13px] text-ink-muted">
                                    {{ $activityTotal }} aktivitas · {{ $subprocesses->count() }} tahap
                                </p>
                                <button
                                    type="button"
                                    class="rounded-control px-2 py-1 text-[13px] font-medium text-ink-muted transition-colors hover:bg-pine-900/[0.06] hover:text-pine-900"
                                    x-on:click="allCollapsed ? expandAll() : collapseAll()"
                                    x-text="allCollapsed ? 'Buka semua tahap' : 'Tutup semua tahap'"
                                >Tutup semua tahap</button>
                            </div>

                            @php $rowIndex = 0; @endphp
                            @foreach ($subprocesses as $subprocess)
                                @php $stageKey = 'stage-'.$subprocess->id; @endphp
                                <section
                                    data-calculator-stage
                                    data-stage-key="{{ $stageKey }}"
                                    class="overflow-hidden rounded-panel border border-line bg-white"
                                    aria-labelledby="stage-{{ $subprocess->id }}"
                                >
                                    <div class="flex items-center gap-2 px-4 py-2.5">
                                        <button
                                            type="button"
                                            x-on:click="toggleStage('{{ $stageKey }}')"
                                            x-bind:aria-expanded="!isCollapsed('{{ $stageKey }}')"
                                            aria-controls="{{ $stageKey }}-body"
                                            class="flex min-w-0 flex-1 items-center gap-2 text-left"
                                        >
                                            <x-lucide-chevron-right class="size-4 shrink-0 text-ink-muted transition-transform" x-bind:class="isCollapsed('{{ $stageKey }}') ? '' : 'rotate-90'" />
                                            <span id="stage-{{ $subprocess->id }}" class="truncate text-[13.5px] font-semibold text-pine-900">{{ $subprocess->description }}</span>
                                            <span class="shrink-0 text-xs tabular-nums text-ink-muted">{{ $subprocess->activities->count() }}</span>
                                        </button>
                                    </div>

                                    <div id="{{ $stageKey }}-body" x-show="!isCollapsed('{{ $stageKey }}')" x-cloak class="border-t border-line">
                                        @foreach ($subprocess->activities as $activity)
                                            @php
                                                $optionFactor = [];
                                                foreach ($activity->options as $opt) {
                                                    $optionFactor[$opt->id] = (float) ($opt->emissionFactor?->ef_value ?? 0);
                                                }
                                                $activityFactor = (float) ($activity->emissionFactor?->ef_value ?? 0);
                                                $hasOptions = $activity->options->isNotEmpty();
                                                $unitName = $activity->activityUnit?->unit_name;
                                            @endphp
                                            <div
                                                data-calculator-row
                                                class="grid gap-x-4 gap-y-2 border-b border-line px-4 py-2.5 last:border-b-0 sm:grid-cols-[minmax(0,1fr)_9.5rem_6.5rem] sm:items-center"
                                            >
                                                <input type="hidden" name="items[{{ $rowIndex }}][activity_id]" value="{{ $activity->id }}">

                                                <div class="min-w-0">
                                                    <label for="items-{{ $rowIndex }}-quantity" class="block text-[13.5px] font-medium leading-5 text-pine-900">{{ $activity->description }}</label>
                                                    <p class="mt-0.5 text-xs leading-5 text-ink-muted">
                                                        {{ $unitName ?? '(tanpa satuan)' }}
                                                    </p>
                                                </div>

                                                <div class="min-w-0">
                                                    @if ($hasOptions)
                                                        <label for="items-{{ $rowIndex }}-option" class="mb-1 block text-xs text-ink-muted sm:sr-only">Opsi</label>
                                                        <select
                                                            id="items-{{ $rowIndex }}-option"
                                                            name="items[{{ $rowIndex }}][option_id]"
                                                            class="control h-9"
                                                            data-option-select
                                                            data-factors="{{ json_encode($optionFactor) }}"
                                                            x-on:change="recalculate()"
                                                        >
                                                            <option value="">Pilih opsi</option>
                                                            @foreach ($activity->options as $opt)
                                                                <option value="{{ $opt->id }}" data-ef="{{ (float) ($opt->emissionFactor?->ef_value ?? 0) }}" @selected((string) old('items.'.$rowIndex.'.option_id') === (string) $opt->id)>{{ $opt->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="hidden" name="items[{{ $rowIndex }}][option_id]" value="">
                                                    @endif
                                                </div>

                                                <div class="flex items-center gap-2 sm:block">
                                                    <label for="items-{{ $rowIndex }}-quantity" class="w-20 shrink-0 text-xs text-ink-muted sm:sr-only">Kuantitas</label>
                                                    <input
                                                        id="items-{{ $rowIndex }}-quantity"
                                                        type="number"
                                                        inputmode="decimal"
                                                        min="0"
                                                        step="any"
                                                        name="items[{{ $rowIndex }}][quantity]"
                                                        value="{{ old('items.'.$rowIndex.'.quantity') }}"
                                                        placeholder="0"
                                                        class="control h-9 w-full text-right tabular-nums placeholder:text-slate-300"
                                                        data-quantity-input
                                                        data-base-ef="{{ $activityFactor }}"
                                                        x-on:input="recalculate()"
                                                        x-on:focus="selectOnFocus($event)"
                                                    >
                                                </div>
                                            </div>
                                            @php $rowIndex++; @endphp
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </section>
                    @endif
                </div>

                <aside class="hidden lg:block" aria-label="Ringkasan perhitungan">
                    <div class="sticky top-24 space-y-4">
                        <x-admin.ui.panel title="Ringkasan">
                            <p class="text-[13px] font-medium text-ink-muted">Total sementara</p>
                            <p class="mt-1 flex flex-wrap items-baseline gap-x-1.5">
                                <span class="text-[32px] font-bold leading-10 tracking-tight tabular-nums text-pine-900" x-text="formattedTotal">0</span>
                                <span class="text-[13px] font-medium text-ink-muted">kg CO2e</span>
                            </p>

                            <div class="mt-3">
                                <div class="h-1 w-full overflow-hidden rounded-full bg-sage-100" role="presentation">
                                    <div class="h-full rounded-full bg-sage-600 transition-[width] duration-200 motion-reduce:transition-none" x-bind:style="`width: ${progress}%`"></div>
                                </div>
                                <p class="mt-1.5 text-xs text-ink-muted">
                                    <span x-text="filled">0</span> dari {{ $activityTotal }} aktivitas terisi
                                </p>
                            </div>

                            <div class="mt-4 border-t border-line pt-4">
                                <x-admin.ui.button type="submit" name="is_published" value="1" variant="primary" class="w-full">
                                    <x-lucide-send />
                                    Simpan &amp; terbitkan
                                </x-admin.ui.button>
                            </div>
                        </x-admin.ui.panel>
                    </div>
                </aside>
            </div>

            <div class="fixed inset-x-0 bottom-0 z-20 border-t border-line bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
                <div class="flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-medium text-ink-muted">Total sementara</p>
                        <p class="truncate">
                            <span class="text-lg font-bold tabular-nums text-pine-900" x-text="formattedTotal">0</span>
                            <span class="text-xs font-medium text-ink-muted">kg CO2e</span>
                        </p>
                    </div>
                    <x-admin.ui.button type="submit" name="is_published" value="1" variant="primary" size="sm">
                        <x-lucide-send />
                        Simpan &amp; terbitkan
                    </x-admin.ui.button>
                </div>
            </div>
        </form>
    @else
        <x-admin.ui.panel class="mt-6" flush>
            <x-admin.ui.empty-state
                title="Belum ada skema atau proses"
                description="Jalankan seeder data master LCI terlebih dahulu agar kalkulator bisa menampilkan aktivitas."
            >
                <x-slot:icon>
                    <x-lucide-database />
                </x-slot:icon>
            </x-admin.ui.empty-state>
        </x-admin.ui.panel>
    @endif
</x-admin.layouts.app>
