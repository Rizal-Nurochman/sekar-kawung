<div
    id="product-modal"
    x-data="productModal"
    x-on:open-product.window="openProduct($event.detail.product)"
    x-show="modalOpen"
    x-cloak
    x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200 motion-reduce:transition-none"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/70 p-3 backdrop-blur-md sm:p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-title"
>
    <x-ui.card
        id="modal-card"
        as="div"
        tone="plain"
        padding="none"
        rounded="3xl"
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-200 motion-reduce:transition-none"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 motion-reduce:transition-none"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden shadow-2xl"
    >
        <div class="relative h-48 shrink-0 bg-slate-100 sm:h-60">
            <img :src="selectedProduct?.image" :alt="selectedProduct?.name" class="size-full object-cover">
            <div class="absolute inset-0 bg-linear-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>

            <x-ui.button type="button" x-ref="closeButton" variant="icon" class="absolute right-4 top-4 z-10" aria-label="Tutup detail produk" x-on:click="closeModal()">
                <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
            </x-ui.button>

            <div class="absolute bottom-4 left-6 right-6 text-white">
                <span class="mb-1 inline-block rounded-full bg-sage-600 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-white" x-text="selectedProduct?.categoryName"></span>
                <h3 id="modal-title" class="text-2xl font-extrabold leading-tight text-white sm:text-3xl" x-text="selectedProduct?.name"></h3>
                <p class="mt-1 text-xs font-medium text-slate-300" x-text="selectedProduct?.spec"></p>
            </div>
        </div>

        <div class="flex-1 space-y-6 overflow-y-auto p-6">
            <div class="flex gap-6 overflow-x-auto border-b border-gray-200 text-sm font-semibold" role="tablist" aria-label="Detail produk">
                <template x-for="tab in tabs" :key="tab.id">
                    <x-ui.button
                        type="button"
                        variant="tab"
                        role="tab"
                        x-bind:id="'tab-btn-' + tab.id"
                        x-bind:aria-controls="'tab-' + tab.id"
                        x-bind:aria-selected="activeTab === tab.id"
                        x-bind:class="activeTab === tab.id ? 'border-sage-600 font-bold text-sage-600' : 'border-transparent font-medium text-slate-400 hover:text-slate-700'"
                        x-on:click="activeTab = tab.id"
                    >
                        <i class="fa-solid" x-bind:class="tab.icon" aria-hidden="true"></i>
                        <span x-text="tab.label"></span>
                    </x-ui.button>
                </template>
            </div>

            <div id="tab-overview" class="space-y-6" role="tabpanel" x-show="activeTab === 'overview'">
                <div>
                    <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Produk</h4>
                    <p class="text-sm leading-relaxed text-slate-600" x-text="selectedProduct?.desc"></p>
                </div>

                <x-ui.card tone="muted" padding="md" rounded="2xl" class="space-y-4">
                    <div class="flex flex-col justify-between gap-3 border-b border-gray-200/60 pb-4 sm:flex-row sm:items-center">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-sage-600">Asal Material</span>
                            <h5 class="text-lg font-bold text-slate-900" x-text="selectedProduct?.company?.name"></h5>
                            <p class="mt-0.5 text-xs text-slate-500">
                                <i class="fa-solid fa-location-dot mr-1 text-sage-600" aria-hidden="true"></i>
                                <span x-text="selectedProduct?.company?.location"></span>
                            </p>
                        </div>

                        <span class="inline-flex items-center self-start rounded-xl bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800 sm:self-auto">
                            <i class="fa-solid fa-shield-halved mr-1.5" aria-hidden="true"></i>
                            <span x-text="selectedProduct?.cert"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs sm:grid-cols-3">
                        <div>
                            <span class="block text-slate-400">Kapasitas Minimum (MOQ)</span>
                            <span class="font-bold text-slate-800" x-text="selectedProduct?.company?.moq"></span>
                        </div>
                        <div>
                            <span class="block text-slate-400">Standar Kepatuhan</span>
                            <span class="font-bold text-slate-800" x-text="selectedProduct?.company?.compliance"></span>
                        </div>
                        <div>
                            <span class="block text-slate-400">Tipe Pasokan</span>
                            <span class="font-bold text-slate-800">Tenun Tuban</span>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <div id="tab-process" class="space-y-6" role="tabpanel" x-show="activeTab === 'process'">
                <div>
                    <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Tahapan Proses Produksi Berkelanjutan</h4>
                    <p class="mb-4 text-xs text-slate-500">Dokumentasi tahapan produksi material.</p>

                    <div class="space-y-3">
                        <template x-for="step in (selectedProduct?.company?.processSteps ?? [])" :key="step.title">
                            <x-ui.card tone="muted" padding="xs" rounded="xl">
                                <h5 class="text-xs font-bold text-slate-800" x-text="step.title"></h5>
                                <p class="mt-1 text-xs text-slate-600" x-text="step.desc"></p>
                            </x-ui.card>
                        </template>
                    </div>
                </div>
            </div>

            <div id="tab-resources" class="space-y-6" role="tabpanel" x-show="activeTab === 'resources'">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-ui.card tone="sage" padding="sm" rounded="2xl">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase text-sage-700">
                            <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                            <span>Sumber Energi Manufaktur</span>
                        </div>
                        <p class="mt-2 text-lg font-extrabold text-sage-900" x-text="selectedProduct?.company?.energySource"></p>
                        <p class="mt-1 text-[11px] text-sage-600">Data sumber energi akan dilengkapi.</p>
                    </x-ui.card>

                    <x-ui.card tone="blue" padding="sm" rounded="2xl">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase text-blue-700">
                            <i class="fa-solid fa-droplet" aria-hidden="true"></i>
                            <span>Konsumsi Air (L/Kg)</span>
                        </div>
                        <p class="mt-2 text-lg font-extrabold text-blue-900" x-text="selectedProduct?.company?.waterUsage"></p>
                        <p class="mt-1 text-[11px] text-blue-600">Data konsumsi air akan dilengkapi.</p>
                    </x-ui.card>

                    <x-ui.card tone="amber" padding="sm" rounded="2xl">
                        <div class="flex items-center gap-2 text-xs font-bold uppercase text-amber-700">
                            <i class="fa-solid fa-recycle" aria-hidden="true"></i>
                            <span>Bahan Baku Kapas</span>
                        </div>
                        <p class="mt-2 text-lg font-extrabold text-amber-900" x-text="selectedProduct?.company?.recycledPct"></p>
                        <p class="mt-1 text-[11px] text-amber-600">Jenis kapas sesuai warna material.</p>
                    </x-ui.card>
                </div>

                <x-ui.card tone="muted" padding="sm" rounded="2xl">
                    <h5 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-800">Bahan Kimia &amp; Pewarnaan Ramah Lingkungan</h5>
                    <p class="text-xs leading-relaxed text-slate-600" x-text="selectedProduct?.company?.chemicalInfo"></p>
                </x-ui.card>
            </div>

            <div id="tab-impact" class="space-y-6" role="tabpanel" x-show="activeTab === 'impact'">
                <div class="flex flex-col items-center justify-between gap-4 rounded-2xl bg-linear-to-r from-slate-900 to-slate-800 p-6 text-white sm:flex-row">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Jejak Karbon Spesifik</span>
                        <div class="mt-1 flex items-baseline gap-2">
                            <span class="text-3xl font-black text-white" x-text="selectedProduct?.emission"></span>
                            <span class="text-xs text-slate-300">kg CO₂e per Kg kain</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Dihitung dari alur pengambilan bahan hingga pintu pabrik (Cradle-to-Gate).</p>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-center backdrop-blur-sm sm:text-right">
                        <span class="block text-[10px] uppercase text-slate-300">Penghematan Karbon</span>
                        <span class="text-lg font-bold text-emerald-300" x-text="carbonSavingsText"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <span class="mb-1 block text-slate-400">Skor Keberlanjutan</span>
                        <span class="text-xl font-bold text-sage-600">Belum dinilai</span>
                        <p class="mt-1 text-[10px] text-slate-500">Penilaian material belum tersedia.</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 bg-white p-4">
                        <span class="mb-1 block text-slate-400">Potensi Daur Ulang Kembali</span>
                        <span class="text-xl font-bold text-slate-800">Belum dinilai</span>
                        <p class="mt-1 text-[10px] text-slate-500">Penilaian akhir masa pakai belum tersedia.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex shrink-0 flex-col items-center justify-between gap-3 border-t border-gray-200 bg-slate-50 p-4 sm:flex-row sm:p-6">
            <span class="text-xs text-slate-500">
                <i class="fa-solid fa-circle-check mr-1 text-sage-600" aria-hidden="true"></i>
                Data karbon contoh prototipe; belum diverifikasi untuk kain ini.
            </span>

            <x-ui.button type="button" variant="primary" size="sm" weight="semibold" class="w-full sm:w-auto" x-on:click="useInCalculator()">
                <i class="fa-solid fa-calculator" aria-hidden="true"></i>
                <span>Hitung Karbon Produk Ini di Kalkulator</span>
            </x-ui.button>
        </div>
    </x-ui.card>
</div>