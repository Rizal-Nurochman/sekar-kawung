@php
    $mode = $mode ?? 'empty';
@endphp

<x-landing.layouts.app title="Kalkulator Publik | Sekar Kawung" hero-background="bg-foam-50">
    <x-slot:hero>
        <section class="mx-auto w-full max-w-[1200px] px-4 pb-10 pt-14 sm:px-6 lg:px-0">
            <p class="text-[13px] font-medium text-ink-muted">Kalkulator Publik</p>
            <h1 class="mt-2 max-w-[688px] text-4xl font-bold leading-tight tracking-[-0.02em] text-balance text-pine-900 sm:text-5xl sm:leading-[1.2]">Kalkulator Emisi Karbon</h1>
            <p class="mt-4 max-w-[65ch] text-lg leading-8 text-ink-soft">Isi aktivitas Anda tahap demi tahap, lalu lihat hasil perhitungan jejak karbon Anda secara langsung.</p>
        </section>
    </x-slot:hero>

    @if ($mode === 'empty')
        <section class="mx-auto w-full max-w-[1200px] px-4 pb-20 sm:px-6 lg:px-0">
            <p class="text-[13px] leading-6 text-ink-muted">Belum ada skema atau proses yang tersedia saat ini.</p>
        </section>
    @elseif ($mode === 'start')
        <section class="mx-auto flex min-h-[40dvh] w-full max-w-[1200px] items-center justify-center px-4 pb-20 sm:px-6 lg:px-0">
            <div class="w-full max-w-xs text-center">
                <h2 class="text-xl font-bold leading-7 tracking-tight text-pine-900">Kalkulator Emisi Karbon</h2>

                <form id="start-form" method="POST" action="{{ route('calculator.start') }}" class="mt-6 text-left">
                    @csrf
                    <label for="start-scheme" class="text-[13px] font-medium text-ink-muted">Skema perhitungan</label>
                    <select id="start-scheme" name="scheme_id" class="control mt-1 h-12 w-full">
                        <option value="">Semua skema</option>
                        @foreach ($schemes as $scheme)
                            <option value="{{ $scheme->id }}" @selected((int) ($selectedSchemeId ?? 0) === (int) $scheme->id)>{{ $scheme->description }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-[13px] leading-5 text-ink-muted">Pilih satu skema, atau hitung semua skema sekaligus.</p>
                    <button id="start-calculator" type="submit" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-pine-900 px-6 py-3 text-base font-semibold leading-6 text-white transition-colors hover:bg-pine-950">
                        Mulai Menghitung
                    </button>
                </form>
            </div>
        </section>
    @elseif ($mode === 'step')
        @php
            $position = $currentIndex + 1;
            $percent = $totalSteps > 0 ? round($position / $totalSteps * 100) : 0;
            $oldByActivity = collect(old('items', []))->keyBy('activity_id');
            $rowIndex = 0;
        @endphp

        <section class="mx-auto w-full max-w-[1200px] px-4 pb-20 sm:px-6 lg:px-0">
            <div class="mb-4">
                @include('landing.calculator._scheme-picker')
            </div>
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
                action="{{ route('calculator.step') }}"
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
                            <div class="rounded-panel border border-line bg-white px-5 py-4">
                                <p class="text-[13px] font-semibold text-pine-900">Boundary ini belum punya tahap</p>
                                <p class="mt-0.5 text-[13px] leading-5 text-ink-muted">Boundary ini belum punya proses atau aktivitas. Lanjut ke boundary berikutnya untuk tetap menyimpan posisi.</p>
                            </div>
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

                                                <label for="items-{{ $rowIndex }}-quantity" class="flex min-h-10 items-end text-[14px] font-medium leading-5 text-pine-900"><span>{{ $activity->description }}</span></label>

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
                                                        class="min-w-0 flex-1 bg-transparent px-3.5 text-[16px] tabular-nums text-ink-soft outline-none placeholder:text-slate-300"
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
                                <button id="wizard-back" type="submit" name="direction" value="back" class="btn btn-secondary">Kembali</button>
                            @else
                                <span></span>
                            @endif
                            <button id="wizard-next" type="submit" name="direction" value="next" class="btn btn-primary">
                                @if ($position >= $totalSteps)
                                    Lihat ulasan
                                @else
                                    Lanjut ke Boundary {{ $position + 1 }}
                                @endif
                            </button>
                        </nav>
                    </div>

                    <aside class="hidden lg:block" aria-label="Ringkasan perhitungan">
                        <div class="sticky top-24 space-y-4">
                            <div class="rounded-panel border border-line bg-white px-5 py-4">
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
                            </div>
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
        </section>
    @elseif ($mode === 'review')
        @php
            $boundaries = $review['boundaries'];
            $lastBoundary = $ordered->last();
        @endphp

        <section class="mx-auto w-full max-w-[1200px] px-4 pb-20 sm:px-6 lg:px-0">
            <div class="mb-4">
                @include('landing.calculator._scheme-picker')
            </div>
            <p class="text-[13px] font-medium text-ink-muted">Hasil perhitungan</p>
            <div class="mt-1 flex flex-wrap items-baseline justify-between gap-2">
                <h2 id="review-heading" class="text-lg font-bold leading-7 tracking-tight text-pine-900">Periksa kembali hasil perhitungan Anda</h2>
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
                            <div class="rounded-panel border border-line bg-white px-5 py-4">
                                <p class="text-[13px] font-medium text-ink-muted">{{ $schemeName }}</p>
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
                            </div>
                        @endforeach
                    </div>

                    @foreach ($boundaries as $index => $boundary)
                        @php
                            $position = $index + 1;
                            $boundaryCount = $boundaries->count();
                        @endphp
                        <section aria-labelledby="review-proses-{{ $boundary['id'] }}" class="mt-2">
                            <h2 id="review-proses-{{ $boundary['id'] }}" class="text-lg font-bold leading-7 tracking-tight text-pine-900">
                                Boundary {{ $position }} dari {{ $boundaryCount }}
                            </h2>
                            <p class="mt-3 flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1 rounded-panel bg-pine-900 px-4 py-3">
                                <span class="text-[13px] font-semibold text-white">Subtotal boundary</span>
                                <span class="text-xl font-bold tabular-nums tracking-tight text-white">{{ \App\Support\Number::emissionId($boundary['total']) }} <span class="text-[12px] font-medium text-sage-pale">kg CO2e</span></span>
                            </p>

                            @if ($boundary['items']->isEmpty())
                                <div class="rounded-panel border border-line bg-white px-5 py-4">
                                    <p class="text-[13px] font-semibold text-pine-900">Belum ada isian pada proses ini</p>
                                    <p class="mt-0.5 text-[13px] leading-5 text-ink-muted">Boundary ini belum punya kuantitas tersimpan.</p>
                                </div>
                            @else
                                <div class="rounded-panel border border-line bg-white px-5 py-4">
                                    <h3 class="text-sm font-semibold text-pine-900">Rincian per tahap</h3>
                                    <p class="mt-0.5 text-[13px] leading-5 text-ink-muted">Kontribusi emisi tiap proses pada proses ini.</p>
                                    <table class="data-table mt-3">
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
                                </div>

                                <div class="mt-4 rounded-panel border border-line bg-white px-5 py-4">
                                    <h3 class="text-sm font-semibold text-pine-900">Rincian aktivitas</h3>
                                    <p class="mt-0.5 text-[13px] leading-5 text-ink-muted">{{ $boundary['count'] }} baris perhitungan.</p>
                                    <div class="mt-3 overflow-x-auto">
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
                                </div>
                            @endif
                        </section>
                    @endforeach

                    @if ($review['sources']->isNotEmpty())
                        <div class="rounded-panel border border-line bg-white px-5 py-4">
                            <h3 class="text-sm font-semibold text-pine-900">Sumber faktor emisi</h3>
                            <p class="mt-0.5 text-[13px] leading-5 text-ink-muted">Referensi yang dipakai pada perhitungan ini. Klik untuk melihat daftar.</p>
                            <details class="group mt-2">
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
                        </div>
                    @endif

                    <nav class="flex items-center justify-between gap-3 pt-1" aria-label="Tindakan ulasan">
                        <form method="POST" action="{{ route('calculator.backFromReview') }}">
                            @csrf
                            <button id="wizard-back-review" type="submit" class="btn btn-secondary">
                                Kembali ke {{ $lastBoundary?->description ? 'Boundary '.$totalSteps : 'boundary' }}
                            </button>
                        </form>
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('calculator.reset') }}">
                                @csrf
                                <button id="wizard-reset" type="submit" class="btn btn-secondary" onclick="return confirm('Ulangi dari awal? Isian tersimpan akan dihapus.')">
                                    Ulangi
                                </button>
                            </form>
                            <button id="calculator-print" type="button" class="btn btn-primary" onclick="window.print()">
                                Cetak / Unduh PDF
                            </button>
                        </div>
                    </nav>
                </div>

                <aside class="hidden lg:block" aria-label="Ringkasan akhir">
                    <div class="sticky top-24 space-y-4">
                        <div class="rounded-panel border border-line bg-white px-5 py-4">
                            <p class="text-[13px] font-medium text-ink-muted">Total akhir</p>
                            @foreach ($review['scheme_totals'] as $schemeTotal)
                                <p class="mt-2 text-xs leading-5 text-ink-muted">{{ $schemeTotal['name'] }}</p>
                                <p class="flex flex-wrap items-baseline gap-x-1.5">
                                    <span class="text-xl font-bold tracking-tight tabular-nums text-pine-900">{{ \App\Support\Number::emissionId($schemeTotal['total']) }}</span>
                                    <span class="text-[12px] font-medium text-ink-muted">kg CO2e</span>
                                </p>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    @endif
</x-landing.layouts.app>
