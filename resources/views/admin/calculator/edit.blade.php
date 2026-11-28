@php
    $mode = $mode ?? 'empty';
@endphp

<x-admin.layouts.app title="Kalkulator - Admin Sekar Kawung" heading="Kalkulator Emisi Karbon" subheading="Alur pengisian berurutan dari proses pertama hingga ulasan akhir.">
    @if (in_array($mode, ['step', 'review'], true))
        <x-slot:actions>
            <form method="POST" action="{{ route('admin.calculator.reset') }}">
                @csrf
                <x-admin.ui.button type="submit" variant="ghost" onclick="return confirm('Ulangi dari awal? Isian tersimpan akan dihapus.')">
                    <x-lucide-rotate-ccw />
                    Ulangi
                </x-admin.ui.button>
            </form>
        </x-slot:actions>
    @endif

    @if ($mode === 'empty')
        <x-admin.ui.panel flush>
            <x-admin.ui.empty-state
                title="Belum ada skema atau proses"
                description="Tambahkan skema dan proses di Konfigurasi agar kalkulator bisa menampilkan aktivitas."
            >
                <x-slot:icon>
                    <x-lucide-database />
                </x-slot:icon>
            </x-admin.ui.empty-state>
        </x-admin.ui.panel>
    @elseif ($mode === 'start')
        <div class="flex min-h-[60dvh] items-center justify-center py-10">
            <x-admin.ui.panel flush class="w-full max-w-xs">
                <div class="px-6 py-14 text-center">
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-pine-900 text-white" aria-hidden="true">
                        <x-lucide-calculator class="size-6" />
                    </span>
                    <h2 class="mt-4 text-xl font-bold leading-7 tracking-tight text-pine-900">Kalkulator Emisi Karbon</h2>

                    <form method="POST" action="{{ route('admin.calculator.start') }}" class="mt-6">
                        @csrf
                        <x-admin.ui.button id="start-calculator" type="submit" variant="primary" size="lg" class="w-full">
                            Mulai Menghitung
                            <x-lucide-arrow-right />
                        </x-admin.ui.button>
                    </form>
                </div>
            </x-admin.ui.panel>
        </div>
    @elseif ($mode === 'step')
        @php
            $position = $currentIndex + 1;
            $percent = $totalSteps > 0 ? round($position / $totalSteps * 100) : 0;
            $oldByActivity = collect(old('items', []))->keyBy('activity_id');
            $rowIndex = 0;
        @endphp

        <p class="text-[13px] font-medium text-ink-muted">{{ $boundary->scheme?->description }}</p>
        <div class="mt-1 flex flex-wrap items-baseline justify-between gap-2">
            <h2 id="wizard-progress" class="text-lg font-bold leading-7 tracking-tight text-pine-900">
                Boundary {{ $position }} dari {{ $totalSteps }} : {{ $boundary->description }}
            </h2>
            <p class="text-[13px] tabular-nums text-ink-muted">{{ $percent }}%</p>
        </div>
        <div class="mt-2 h-1 w-full overflow-hidden rounded-full bg-sage-100" role="progressbar" aria-valuemin="1" aria-valuemax="{{ $totalSteps }}" aria-valuenow="{{ $position }}" aria-label="Kemajuan proses">
            <div class="h-full rounded-full bg-sage-600" style="width: {{ $percent }}%"></div>
        </div>

        <form
            id="calculator-form"
            method="POST"
            action="{{ route('admin.calculator.step') }}"
            x-data="adminCalculator({ baseSchemeTotals: @js($schemeTotals), currentSchemeId: {{ (int) $currentSchemeId }} })"
            x-on:input="recalculate()"
            x-on:change="recalculate()"
            class="mt-6 pb-24 lg:pb-0"
        >
            @csrf
            <input type="hidden" name="boundary_id" value="{{ $boundary->id }}">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_17.5rem]">
                <div class="min-w-0 space-y-6">
                    <x-admin.ui.errors />

                    @if ($boundary->processes->isEmpty())
                        <x-admin.ui.panel flush>
                            <x-admin.ui.empty-state
                                title="Boundary ini belum punya tahap"
                                description="Boundary ini belum punya proses atau aktivitas. Lanjut ke boundary berikutnya untuk tetap menyimpan posisi."
                            >
                                <x-slot:icon>
                                    <x-lucide-list-tree />
                                </x-slot:icon>
                            </x-admin.ui.empty-state>
                        </x-admin.ui.panel>
                    @else
                        @foreach ($boundary->processes as $process)
                            <section aria-labelledby="process-{{ $process->id }}" class="overflow-hidden rounded-2xl border border-line bg-white">
                                <div class="border-b border-line px-4 py-3 sm:px-5">
                                    <h3 id="process-{{ $process->id }}" class="text-[15px] font-semibold leading-6 text-pine-900">{{ $process->description }}</h3>
                                    <p class="mt-0.5 text-xs tabular-nums text-ink-muted">{{ $process->activities->count() }} aktivitas</p>
                                </div>
                                <div class="grid grid-cols-1 gap-x-6 gap-y-5 px-4 py-4 sm:grid-cols-2 sm:px-5">
                                    @foreach ($process->activities as $activity)
                                        @php
                                            $hasOptions = $activity->options->isNotEmpty();
                                            $fallbackUnit = $activity->activityUnit?->unit_name ?? '';
                                            $saved = $entries[(string) $activity->id] ?? ['option_id' => null, 'quantity' => null];
                                            $oldRow = $oldByActivity->get((string) $activity->id) ?? $oldByActivity->get((int) $activity->id);
                                            $currentOptionId = $oldRow['option_id'] ?? $saved['option_id'] ?? null;
                                            $currentQuantity = $oldRow['quantity'] ?? $saved['quantity'] ?? null;
                                            if ($hasOptions && $currentOptionId !== null && $currentOptionId !== '') {
                                                $matched = $activity->options->firstWhere('id', (int) $currentOptionId);
                                                $defaultUnit = $matched?->activityUnit?->unit_name ?? $fallbackUnit;
                                                $defaultUnit = $defaultUnit ?? '';
                                            } else {
                                                $defaultUnit = $fallbackUnit;
                                            }
                                        @endphp
                                        <div data-calculator-row class="min-w-0">
                                            <input type="hidden" name="items[{{ $rowIndex }}][activity_id]" value="{{ $activity->id }}">

                                            <label for="items-{{ $rowIndex }}-quantity" class="flex min-h-10 items-end text-[14px] font-medium leading-5 text-slate-900"><span>{{ $activity->description }}</span></label>

                                            @if ($hasOptions)
                                                <select
                                                    id="items-{{ $rowIndex }}-option"
                                                    name="items[{{ $rowIndex }}][option_id]"
                                                    aria-label="Opsi untuk {{ $activity->description }}"
                                                    class="control mt-2 h-12"
                                                    data-option-select
                                                    x-on:change="syncUnit($event, '{{ $fallbackUnit }}')"
                                                >
                                                    <option value="">Pilih opsi</option>
                                                    @foreach ($activity->options as $opt)
                                                        <option
                                                            value="{{ $opt->id }}"
                                                            data-ef="{{ (float) ($opt->emissionFactor?->ef_value ?? 0) }}"
                                                            data-unit="{{ $opt->activityUnit?->unit_name ?? '' }}"
                                                            @selected((string) $currentOptionId === (string) $opt->id)
                                                        >{{ $opt->description }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="items[{{ $rowIndex }}][option_id]" value="">
                                            @endif

                                            <div class="{{ $hasOptions ? 'mt-2' : 'mt-1.5' }} flex h-12 items-stretch overflow-hidden rounded-xl border border-mist-100 bg-white focus-within:border-sage-600 focus-within:ring-2 focus-within:ring-sage-600/15">
                                                <input
                                                    id="items-{{ $rowIndex }}-quantity"
                                                    type="number"
                                                    inputmode="decimal"
                                                    min="0"
                                                    step="any"
                                                    name="items[{{ $rowIndex }}][quantity]"
                                                    value="{{ $currentQuantity }}"
                                                    placeholder="0"
                                                    aria-label="{{ $activity->description }} — kuantitas"
                                                    class="min-w-0 flex-1 bg-transparent px-3.5 text-[16px] tabular-nums text-slate-800 outline-none placeholder:text-slate-300"
                                                    data-quantity-input
                                                    data-base-ef="{{ (float) ($activity->emissionFactor?->ef_value ?? 0) }}"
                                                    x-on:focus="selectOnFocus($event)"
                                                >
                                                <span data-unit-pill title="{{ $defaultUnit }}" class="flex w-28 shrink-0 items-center justify-center truncate bg-mist-100 px-3 text-center text-[14px] font-medium leading-5 text-pine-900">{{ $defaultUnit }}</span>
                                            </div>

                                        </div>
                                        @php $rowIndex++; @endphp
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    @endif

                    <nav class="flex items-center justify-between gap-3 pt-1" aria-label="Navigasi proses">
                        @if ($currentIndex > 0)
                            <x-admin.ui.button id="wizard-back" type="submit" name="direction" value="back" size="md">
                                <x-lucide-arrow-left />
                                Kembali
                            </x-admin.ui.button>
                        @else
                            <span></span>
                        @endif
                        <x-admin.ui.button id="wizard-next" type="submit" name="direction" value="next" variant="primary" size="md">
                            @if ($position >= $totalSteps)
                                Lihat ulasan
                            @else
                                Lanjut ke Boundary {{ $position + 1 }}
                            @endif
                            <x-lucide-arrow-right />
                        </x-admin.ui.button>
                    </nav>
                </div>

                <aside class="hidden lg:block" aria-label="Ringkasan perhitungan">
                    <div class="sticky top-24 space-y-4">
                        <x-admin.ui.panel title="Ringkasan">
                            <p class="text-[13px] font-medium text-ink-muted">Total sementara</p>
                            <template x-for="(scheme, index) in schemeTotals" :key="scheme.id">
                                <div class="mt-2" :class="index > 0 ? 'border-t border-line pt-2' : ''" aria-live="polite">
                                    <p class="text-xs leading-5 text-ink-muted" x-text="scheme.name"></p>
                                    <p class="flex flex-wrap items-baseline gap-x-1.5">
                                        <span x-bind:id="'total-sementara-' + scheme.id" class="text-2xl font-bold leading-9 tracking-tight tabular-nums text-pine-900" x-text="scheme.formattedTotal">0</span>
                                        <span class="text-[13px] font-medium text-ink-muted">kg CO2e</span>
                                    </p>
                                </div>
                            </template>
                        </x-admin.ui.panel>
                    </div>
                </aside>
            </div>

            <div class="fixed inset-x-0 bottom-0 z-20 border-t border-line bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
                <p class="text-xs font-medium text-ink-muted">Total sementara</p>
                <template x-for="(scheme, index) in schemeTotals" :key="scheme.id">
                    <p class="flex flex-wrap items-baseline gap-x-1.5" :class="index > 0 ? 'mt-1 border-t border-line pt-1' : ''" aria-live="polite">
                        <span class="text-xs text-ink-muted" x-text="scheme.name"></span>
                        <span x-bind:id="'total-sementara-mobile-' + scheme.id" class="text-lg font-bold tabular-nums text-pine-900" x-text="scheme.formattedTotal">0</span>
                        <span class="text-xs font-medium text-ink-muted">kg CO2e</span>
                    </p>
                </template>
            </div>
        </form>
    @elseif ($mode === 'review')
        @php
            $boundaries = $review['boundaries'];
            $lastBoundary = $ordered->last();
        @endphp

        <p class="text-[13px] font-medium text-ink-muted">Ulasan menyeluruh</p>
        <div class="mt-1 flex flex-wrap items-baseline justify-between gap-2">
            <h2 id="review-heading" class="text-lg font-bold leading-7 tracking-tight text-pine-900">Periksa kembali sebelum menerbitkan</h2>
            <p class="text-[13px] tabular-nums text-ink-muted">{{ $totalSteps }} dari {{ $totalSteps }} proses selesai · 100%</p>
        </div>
        <div class="mt-2 h-1 w-full overflow-hidden rounded-full bg-sage-100" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $totalSteps }}" aria-valuenow="{{ $totalSteps }}" aria-label="Kemajuan proses">
            <div class="h-full rounded-full bg-sage-600" style="width: 100%"></div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_17.5rem]">
            <div class="min-w-0 space-y-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($review['scheme_totals'] as $schemeTotal)
                        @php
                            $schemeName = $schemeTotal['name'];
                            $schemeHotspot = $review['hotspot_by_scheme'][$schemeName] ?? null;
                        @endphp
                        <x-admin.ui.panel :title="$schemeName">
                            <p class="mt-1 flex flex-wrap items-baseline gap-x-2" aria-live="polite">
                                <span class="text-[32px] font-bold leading-10 tracking-tight tabular-nums text-pine-900">{{ \App\Support\Number::emissionId($schemeTotal['total']) }}</span>
                                <span class="text-[13px] font-medium text-ink-muted">kg CO2e</span>
                            </p>

                            @if ($schemeHotspot)
                                <p class="mt-3 border-t border-line pt-3 text-[13px] leading-5 text-ink-soft">
                                    Kontribusi terbesar: <span class="font-semibold text-pine-900">{{ $schemeHotspot['activity'] }}</span>
                                    ({{ \App\Support\Number::emissionId($schemeHotspot['emission']) }} kg CO2e)
                                </p>
                            @endif
                        </x-admin.ui.panel>
                    @endforeach
                </div>

                @foreach ($boundaries as $index => $boundary)
                    @php
                        $position = $index + 1;
                        $boundaryCount = $boundaries->count();
                    @endphp
                    <section aria-labelledby="review-proses-{{ $boundary['id'] }}" class="mt-2">
                        <p class="text-[13px] font-medium text-ink-muted">{{ $boundary['scheme'] }}</p>
                        <h2 id="review-proses-{{ $boundary['id'] }}" class="mt-1 text-lg font-bold leading-7 tracking-tight text-pine-900">
                            Boundary {{ $position }} dari {{ $boundaryCount }} : {{ $boundary['description'] }}
                        </h2>
                        <p class="mt-3 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 rounded-panel bg-pine-900 px-4 py-3">
                            <span class="text-[13px] font-semibold text-white">Subtotal boundary</span>
                            <span class="text-xl font-bold tabular-nums tracking-tight text-white">{{ \App\Support\Number::emissionId($boundary['total']) }} <span class="text-[12px] font-medium text-sage-pale">kg CO2e</span></span>
                        </p>

                        @if ($boundary['items']->isEmpty())
                            <x-admin.ui.panel class="mt-4" flush>
                                <x-admin.ui.empty-state
                                    title="Belum ada isian pada proses ini"
                                    description="Boundary ini belum punya kuantitas tersimpan."
                                >
                                    <x-slot:icon>
                                        <x-lucide-list-tree />
                                    </x-slot:icon>
                                </x-admin.ui.empty-state>
                            </x-admin.ui.panel>
                        @else
                            <x-admin.ui.panel class="mt-4" title="Rincian per tahap" :description="'Kontribusi emisi tiap proses pada '.$boundary['description'].'.'" flush>
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
                                            @foreach ($boundary['items'] as $row)
                                                <tr>
                                                    <td>
                                                        <p class="text-[13.5px] font-medium text-pine-900">{{ $row['activity'] }}</p>
                                                        <p class="mt-0.5 text-xs leading-4 text-ink-muted">{{ $row['process'] }}</p>
                                                        @if ($row['option'])
                                                            <p class="mt-0.5 text-xs leading-4 text-ink-muted">Opsi: {{ $row['option'] }}</p>
                                                        @elseif ($row['is_heritage'])
                                                            <p class="mt-0.5 text-xs leading-4 text-ink-muted">Tahap manual tanpa emisi</p>
                                                        @endif
                                                    </td>
                                                    <td class="whitespace-nowrap text-[13px] tabular-nums text-ink-soft">{{ \App\Support\Number::quantity($row['quantity']) }}{{ $row['unit'] !== '' ? ' '.$row['unit'] : '' }}</td>
                                                    <td class="whitespace-nowrap text-[13px] tabular-nums text-ink-muted">
                                                        {{ \App\Support\Number::emissionId($row['factor']) }}
                                                        @if ($row['source'])
                                                            <span class="block max-w-40 truncate text-xs">{{ $row['source'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="cell-num">{{ \App\Support\Number::emissionId($row['emission']) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </x-admin.ui.panel>
                        @endif
                    </section>
                @endforeach

                @if ($review['sources']->isNotEmpty())
                    <x-admin.ui.panel title="Sumber faktor emisi" description="Referensi yang dipakai pada perhitungan ini. Klik untuk melihat daftar." flush>
                        <details class="group px-5 py-3">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-2 text-[13.5px] font-medium text-pine-900 [&::-webkit-details-marker]:hidden">
                                <span>{{ $review['sources']->count() }} sumber</span>
                                <x-lucide-chevron-down class="size-4 shrink-0 text-ink-muted transition-transform group-open:rotate-180" />
                            </summary>
                            <ul class="mt-2 divide-y divide-line border-t border-line">
                                @foreach ($review['sources'] as $source)
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

                <nav class="flex items-center justify-between gap-3 pt-1" aria-label="Tindakan ulasan">
                    <form method="POST" action="{{ route('admin.calculator.backFromReview') }}">
                        @csrf
                        <x-admin.ui.button id="wizard-back-review" type="submit" size="md">
                            <x-lucide-arrow-left />
                            Kembali ke {{ $lastBoundary?->description ? 'Boundary '.$totalSteps : 'boundary' }}
                        </x-admin.ui.button>
                    </form>
                    <form method="POST" action="{{ route('admin.calculator.store') }}">
                        @csrf
                        <x-admin.ui.button id="wizard-save" type="submit" variant="primary" size="md">
                            <x-lucide-send />
                            Simpan &amp; aktifkan
                        </x-admin.ui.button>
                    </form>
                </nav>
            </div>

            <aside class="hidden lg:block" aria-label="Ringkasan akhir">
                <div class="sticky top-24 space-y-4">
                    <x-admin.ui.panel title="Ringkasan">
                        <p class="text-[13px] font-medium text-ink-muted">Total akhir</p>
                        @foreach ($review['scheme_totals'] as $schemeTotal)
                            <p class="mt-2 text-xs leading-5 text-ink-muted">{{ $schemeTotal['name'] }}</p>
                            <p class="flex flex-wrap items-baseline gap-x-1.5">
                                <span class="text-xl font-bold tracking-tight tabular-nums text-pine-900">{{ \App\Support\Number::emissionId($schemeTotal['total']) }}</span>
                                <span class="text-[12px] font-medium text-ink-muted">kg CO2e</span>
                            </p>
                        @endforeach
                    </x-admin.ui.panel>
                </div>
            </aside>
        </div>
    @endif
</x-admin.layouts.app>
