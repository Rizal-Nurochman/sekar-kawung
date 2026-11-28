<div
    id="product-modal"
    x-data="productModal"
    x-on:open-product.window="openProduct($event.detail.product)"
    x-show="modalOpen"
    x-cloak
    x-on:click.self="closeModal()"
    x-on:keydown.escape.window="closeModal()"
    x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-0 backdrop-blur-sm sm:p-4 lg:p-6"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
    aria-describedby="modal-description"
>
    <x-old.ui.card
        id="modal-card"
        x-ref="dialog"
        as="div"
        tone="plain"
        padding="none"
        rounded="2xl"
        x-show="modalOpen"
        x-on:keydown.tab="trapFocus($event)"
        x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-[0.98]"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150 motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 sm:translate-y-0 sm:scale-[0.98]"
        class="relative flex h-dvh max-h-dvh w-full max-w-5xl flex-col overflow-hidden shadow-2xl max-sm:rounded-none sm:h-[92dvh] sm:max-h-[92dvh] md:grid md:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]"
    >
        <div class="relative h-52 shrink-0 overflow-hidden bg-slate-100 sm:h-64 md:h-auto md:min-h-0 md:border-r md:border-slate-200">
            <img
                :src="selectedProduct?.image"
                :alt="selectedProduct?.name"
                width="960"
                height="1200"
                decoding="async"
                class="absolute inset-0 size-full object-cover"
            >
        </div>

        <x-old.ui.button
            type="button"
            x-ref="closeButton"
            variant="icon"
            class="absolute right-3 top-3 z-20"
            aria-label="Tutup detail produk"
            x-on:click="closeModal()"
        >
            <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
        </x-old.ui.button>

        <div class="flex min-h-0 flex-1 flex-col">
            <div x-ref="scrollArea" tabindex="0" class="relative min-h-0 flex-1 overflow-y-auto overscroll-contain px-5 pb-6 sm:px-7 sm:pb-7">
                <header class="pt-6 sm:pt-7 md:pr-12">
                    <p class="text-xs font-semibold text-sage-700" x-text="selectedProduct?.categoryName"></p>

                    <h2
                        id="modal-title"
                        class="mt-1.5 text-2xl font-bold leading-tight tracking-tight text-balance text-slate-900 sm:text-3xl"
                        x-text="selectedProduct?.name"
                    ></h2>

                    <p class="mt-2 text-sm leading-relaxed text-slate-500" x-text="selectedProduct?.spec"></p>

                    <p class="mt-2 flex items-start gap-1.5 text-sm text-slate-600">
                        <i class="fa-solid fa-location-dot mt-1 text-xs text-sage-600" aria-hidden="true"></i>
                        <span class="font-medium text-slate-800" x-text="selectedProduct?.company?.name"></span>
                    </p>
                </header>

                <dl class="mt-5 grid grid-cols-2 gap-4 rounded-xl border border-sage-100 bg-sage-50 p-4 sm:gap-6 sm:p-5">
                    <div>
                        <dt class="text-xs font-medium text-sage-700">Jejak karbon</dt>
                        <dd class="mt-1 flex flex-wrap items-baseline gap-1.5">
                            <span class="text-3xl font-bold tabular-nums text-sage-800" x-text="selectedProduct?.emission"></span>
                            <span class="text-xs font-medium text-sage-700">kg CO₂e/kg</span>
                        </dd>
                    </div>

                    <div class="border-l border-sage-100 pl-4 sm:pl-6">
                        <dt class="text-xs font-medium text-sage-700">Dibanding katun konvensional</dt>
                        <dd class="mt-1 flex flex-wrap items-baseline gap-1.5">
                            <span class="text-2xl font-bold tabular-nums text-sage-800" x-text="savingsLabel"></span>
                            <span class="text-xs font-medium text-sage-700" x-text="savingsCaption"></span>
                        </dd>
                    </div>
                </dl>

                <p class="mt-2 flex items-start gap-1.5 text-xs leading-relaxed text-slate-500">
                    <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                    <span>Data prototipe; belum diverifikasi untuk kain ini.</span>
                </p>

                <div x-ref="tabAnchor" class="mt-6" aria-hidden="true"></div>

                <div x-ref="tabBar" class="sticky top-0 z-10 -mx-5 border-b border-slate-200 bg-white/95 px-5 py-1.5 backdrop-blur-sm sm:-mx-7 sm:px-7">
                    <div role="tablist" aria-label="Bagian detail produk" class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1">
                        <x-old.ui.button
                            type="button"
                            variant="segmented"
                            role="tab"
                            id="tab-btn-product"
                            aria-controls="tab-product"
                            x-bind:aria-selected="activeTab === 'product'"
                            x-bind:tabindex="activeTab === 'product' ? 0 : -1"
                            x-on:click="selectTab('product')"
                            x-on:keydown="handleTabKeydown($event)"
                            x-bind:class="activeTab === 'product' ? 'bg-white font-semibold text-sage-800 shadow-sm' : 'font-medium text-slate-600 hover:text-slate-900'"
                        >
                            <span>Tentang produk</span>
                        </x-old.ui.button>

                        <x-old.ui.button
                            type="button"
                            variant="segmented"
                            role="tab"
                            id="tab-btn-impact"
                            aria-controls="tab-impact"
                            x-bind:aria-selected="activeTab === 'impact'"
                            x-bind:tabindex="activeTab === 'impact' ? 0 : -1"
                            x-on:click="selectTab('impact')"
                            x-on:keydown="handleTabKeydown($event)"
                            x-bind:class="activeTab === 'impact' ? 'bg-white font-semibold text-sage-800 shadow-sm' : 'font-medium text-slate-600 hover:text-slate-900'"
                        >
                            <span>Dampak lingkungan</span>
                        </x-old.ui.button>
                    </div>
                </div>

                <div id="tab-product" role="tabpanel" aria-labelledby="tab-btn-product" x-show="activeTab === 'product'" class="pt-5">
                    <section>
                        <h3 class="text-sm font-semibold text-slate-900">Tentang material</h3>
                        <p
                            id="modal-description"
                            class="mt-2 text-sm leading-relaxed text-pretty text-slate-600"
                            x-text="selectedProduct?.desc"
                        ></p>
                    </section>

                    <section class="mt-5 border-t border-slate-100 pt-5">
                        <h3 class="text-sm font-semibold text-slate-900">Detail pasokan</h3>

                        <dl class="mt-3 grid gap-x-6 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs text-slate-500">Lokasi</dt>
                                <dd class="mt-0.5 text-sm font-medium text-slate-900" x-text="displayValue(selectedProduct?.company?.location)"></dd>
                            </div>

                            <div>
                                <dt class="text-xs text-slate-500">Kapasitas minimum (MOQ)</dt>
                                <dd
                                    class="mt-0.5 text-sm font-medium"
                                    x-bind:class="isAvailable(selectedProduct?.company?.moq) ? 'text-slate-900' : 'text-slate-500'"
                                    x-text="displayValue(selectedProduct?.company?.moq)"
                                ></dd>
                            </div>

                            <div>
                                <dt class="text-xs text-slate-500">Standar kepatuhan</dt>
                                <dd
                                    class="mt-0.5 text-sm font-medium"
                                    x-bind:class="isAvailable(selectedProduct?.company?.compliance) ? 'text-slate-900' : 'text-slate-500'"
                                    x-text="displayValue(selectedProduct?.company?.compliance)"
                                ></dd>
                            </div>

                            <div>
                                <dt class="text-xs text-slate-500">Sertifikasi</dt>
                                <dd
                                    class="mt-0.5 text-sm font-medium"
                                    x-bind:class="isAvailable(selectedProduct?.cert) ? 'text-slate-900' : 'text-slate-500'"
                                    x-text="displayValue(selectedProduct?.cert)"
                                ></dd>
                            </div>
                        </dl>
                    </section>

                    <section class="mt-5 border-t border-slate-100 pt-5">
                        <h3 class="text-sm font-semibold text-slate-900">Proses produksi</h3>
                        <p class="mt-1 text-xs text-slate-500">Urutan proses dari bahan baku hingga kain tenun.</p>

                        <ol class="mt-4">
                            <template x-for="(step, index) in (selectedProduct?.company?.processSteps ?? [])" :key="step.title">
                                <li class="relative flex gap-3.5 pb-5 last:pb-0">
                                    <span
                                        class="absolute bottom-0 left-4 top-8 border-l border-dashed border-slate-300"
                                        x-show="index < (selectedProduct?.company?.processSteps?.length ?? 0) - 1"
                                        aria-hidden="true"
                                    ></span>

                                    <span
                                        class="relative z-10 flex size-8 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-xs font-semibold tabular-nums text-slate-700"
                                        x-text="index + 1"
                                    ></span>

                                    <div class="min-w-0 pt-1">
                                        <h4 class="text-sm font-semibold text-slate-900" x-text="step.title"></h4>
                                        <p class="mt-1 text-sm leading-relaxed text-slate-600" x-text="step.desc"></p>
                                    </div>
                                </li>
                            </template>
                        </ol>

                        <p
                            class="mt-2 text-sm text-slate-500"
                            x-show="(selectedProduct?.company?.processSteps ?? []).length === 0"
                        >
                            Dokumentasi proses belum tersedia.
                        </p>
                    </section>
                </div>

                <div id="tab-impact" role="tabpanel" aria-labelledby="tab-btn-impact" x-show="activeTab === 'impact'" class="pt-5">
                    <section>
                        <h3 class="text-sm font-semibold text-slate-900">Jejak karbon</h3>
                        <p class="mt-1 text-xs text-slate-500">Perkiraan dari awal hingga gerbang pabrik per kg kain.</p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <div class="flex items-baseline justify-between gap-4 text-sm">
                                    <span class="font-medium text-slate-900">Produk ini</span>
                                    <span class="font-semibold tabular-nums text-sage-700">
                                        <span x-text="selectedProduct?.emission"></span> kg CO₂e/kg
                                    </span>
                                </div>
                                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100" aria-hidden="true">
                                    <div class="h-full rounded-full bg-sage-600" x-bind:style="'width: ' + emissionBarWidth + '%'"></div>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-baseline justify-between gap-4 text-sm">
                                    <span class="text-slate-600">Katun konvensional</span>
                                    <span class="tabular-nums text-slate-600"><span x-text="baselineEmission"></span> kg CO₂e/kg</span>
                                </div>
                                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100" aria-hidden="true">
                                    <div class="h-full rounded-full bg-slate-300" x-bind:style="'width: ' + baselineBarWidth + '%'"></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mt-5 border-t border-slate-100 pt-5">
                        <h3 class="text-sm font-semibold text-slate-900">Sumber daya &amp; energi</h3>

                        <dl class="mt-3 grid divide-y divide-slate-200 rounded-xl border border-slate-200 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                            <div class="p-4">
                                <dt class="flex items-center gap-2 text-xs text-slate-500">
                                    <i class="fa-solid fa-bolt text-sage-600" aria-hidden="true"></i>
                                    <span>Sumber energi</span>
                                </dt>
                                <dd
                                    class="mt-1.5 text-sm font-medium"
                                    x-bind:class="isAvailable(selectedProduct?.company?.energySource) ? 'text-slate-900' : 'text-slate-500'"
                                    x-text="displayValue(selectedProduct?.company?.energySource)"
                                ></dd>
                            </div>

                            <div class="p-4">
                                <dt class="flex items-center gap-2 text-xs text-slate-500">
                                    <i class="fa-solid fa-droplet text-sage-600" aria-hidden="true"></i>
                                    <span>Konsumsi air</span>
                                </dt>
                                <dd
                                    class="mt-1.5 text-sm font-medium"
                                    x-bind:class="isAvailable(selectedProduct?.company?.waterUsage) ? 'text-slate-900' : 'text-slate-500'"
                                    x-text="displayValue(selectedProduct?.company?.waterUsage)"
                                ></dd>
                            </div>
                        </dl>

                        <div class="mt-3">
                            <h4 class="text-xs font-semibold text-slate-900">Bahan kimia &amp; pewarnaan</h4>
                            <p class="mt-1 text-sm leading-relaxed text-slate-600" x-text="displayValue(selectedProduct?.company?.chemicalInfo)"></p>
                        </div>
                    </section>

                    <p class="mt-5 text-xs leading-relaxed text-slate-500">
                        Skor keberlanjutan dan potensi daur ulang belum dinilai untuk kain ini.
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end border-t border-slate-200 bg-white px-5 pb-[calc(1rem_+_env(safe-area-inset-bottom))] pt-4 sm:px-7 sm:pb-4">
                <x-old.ui.button
                    type="button"
                    id="modal-calculator-button"
                    variant="primary"
                    size="sm"
                    weight="semibold"
                    class="min-h-11 w-full sm:w-auto"
                    x-on:click="useInCalculator()"
                >
                    <i class="fa-solid fa-calculator" aria-hidden="true"></i>
                    <span>Hitung di kalkulator</span>
                </x-old.ui.button>
            </div>
        </div>
    </x-old.ui.card>
</div>
