<x-layouts.app>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-slate-900 text-white">
        <div class="absolute inset-0 opacity-25 mix-blend-overlay">
            <img src="{{ asset('images/hero-textile.webp') }}" alt="" class="size-full object-cover">
        </div>
        <div class="gradient-overlay absolute inset-0"></div>

        <div class="relative mx-auto flex max-w-7xl flex-col items-center px-4 py-24 text-center sm:px-6 lg:px-8 lg:py-32">
            <span class="mb-6 inline-flex items-center gap-2 rounded-full border border-gray-200 bg-sage-500/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sage-300 backdrop-blur-sm">
                <i class="fa-solid fa-seedling text-sm" aria-hidden="true"></i>
                <span>Solusi Tekstil Berkelanjutan</span>
            </span>

            <h1 class="max-w-4xl text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                Masa Depan Industri Tekstil Berkelanjutan &amp; <span class="text-sage-400">Rendah Karbon</span>
            </h1>

            <p class="mt-6 max-w-2xl text-lg font-light text-slate-300 sm:text-xl">
                Jelajahi katalok material ramah lingkungan berkualitas tinggi dan kalkulasi jejak karbon produksi Anda secara akurat dalam hitungan detik.
            </p>

            <div class="mt-10 flex w-full flex-col gap-4 sm:w-auto sm:flex-row">
                <x-ui.button href="#katalog" variant="primary-light" size="xl" weight="semibold">
                    <span>Lihat Katalok Produk</span>
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </x-ui.button>

                <x-ui.button href="#kalkulator" variant="secondary" size="xl" weight="semibold">
                    <i class="fa-solid fa-calculator text-sage-400" aria-hidden="true"></i>
                    <span>Coba Kalkulator Karbon</span>
                </x-ui.button>
            </div>

            <div class="mt-16 grid w-full max-w-4xl grid-cols-2 gap-6 border-t border-slate-800/80 pt-8 text-left md:grid-cols-4">
                <div>
                    <p class="text-3xl font-bold text-sage-400">100%</p>
                    <p class="mt-1 text-xs text-slate-400">Material Ramah Lingkungan</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">-45%</p>
                    <p class="mt-1 text-xs text-slate-400">Rata-rata Pengurangan CO2</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-sage-400">ISO 14040</p>
                    <p class="mt-1 text-xs text-slate-400">Standar Kalkulasi LCA</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">50+</p>
                    <p class="mt-1 text-xs text-slate-400">Varian Kain Industri</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Catalog --}}
    <section id="katalog" x-data="catalog({{ Js::from($products) }})" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 flex flex-col justify-between md:flex-row md:items-end">
                <div>
                    <h2 class="mb-2 text-xs font-bold uppercase tracking-widest text-sage-600">Katalog Ramah Lingkungan</h2>
                    <h3 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Koleksi Material Tekstil</h3>
                </div>
                <p class="mt-4 max-w-md text-sm text-slate-500 md:mt-0">
                    Koleksi tenun dari Desa Kerek, Tuban. Angka jejak karbon menggunakan data contoh prototipe awal dan belum merupakan hasil pengukuran kain ini.
                </p>
            </div>

            <div class="mb-10 flex flex-wrap gap-2 border-b border-gray-100 pb-2" id="filter-container" role="group" aria-label="Filter kategori produk">
                @foreach ($categories as $category)
                    <x-ui.button
                        type="button"
                        variant="filter"
                        x-bind:class="filter === '{{ $category['id'] }}' ? 'bg-sage-600 text-white' : 'bg-slate-100 text-slate-600'"
                        x-bind:aria-pressed="filter === '{{ $category['id'] }}'"
                        x-on:click="setFilter('{{ $category['id'] }}')"
                    >
                        {{ $category['label'] }}
                    </x-ui.button>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3" id="product-grid">
                @foreach ($products as $product)
                    <x-catalog.product-card
                        :product="$product"
                        x-show="filter === 'all' || filter === '{{ $product['category'] }}'"
                        x-on:click="openProduct({{ $product['id'] }})"
                        x-on:keydown.enter.prevent="openProduct({{ $product['id'] }})"
                        x-on:keydown.space.prevent="openProduct({{ $product['id'] }})"
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Producer spotlight --}}
    <section class="bg-slate-100/70 py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold">Desa Kerek, Tuban</h2>
            <p class="mt-3 text-slate-600">Koleksi material tenun Sekar Kawung dari Tuban, Jawa Timur.</p>
            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                <x-ui.card tone="plain" padding="md" rounded="2xl">
                    <h3 class="font-bold">Kapas Putih</h3>
                    <p class="mt-2 italic">Gossypium hirsutum L.</p>
                    <p>Untuk tenun warna putih.</p>
                </x-ui.card>
                <x-ui.card tone="plain" padding="md" rounded="2xl">
                    <h3 class="font-bold">Kapas Cokelat</h3>
                    <p class="mt-2 italic">Gossypium barbadense L.</p>
                    <p>Untuk tenun warna cokelat.</p>
                </x-ui.card>
            </div>
        </div>
    </section>

    {{-- Carbon calculator --}}
    <section id="kalkulator" x-data="calculator({{ Js::from($calculatorDefaults) }})" @calculate-product.window="loadProduct($event.detail)" class="border-y border-gray-200/60 bg-slate-50 py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto mb-16 max-w-3xl text-center">
                <span class="mb-4 inline-block rounded-2xl bg-sage-100 p-3 text-sage-700">
                    <i class="fa-solid fa-calculator text-2xl" aria-hidden="true"></i>
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Kalkulator Jejak Karbon Tekstil</h2>
                <p class="mt-4 text-base text-slate-600">
                    Hitung perkiraan emisi Gas Rumah Kaca (GRK) dari proses manufaktur dan logistik tekstil Anda. Transparansi data untuk rantai pasok yang lebih hijau.
                </p>
            </div>

            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
                <x-ui.card tone="white" padding="md" rounded="3xl" class="sm:p-8 lg:col-span-5">
                    <h3 class="mb-6 flex items-center gap-2 text-xl font-bold text-slate-900">
                        <i class="fa-solid fa-sliders text-sage-600" aria-hidden="true"></i>
                        <span>Parameter Produksi</span>
                    </h3>

                    <form id="calc-form" class="space-y-6" @submit.prevent="updateChart()">
                        <x-form.select id="calc-material" label="Jenis Material Tekstil" x-model.number="material" x-ref="materialSelect">
                            @foreach ($materials as $material)
                                <option value="{{ $material['value'] }}">{{ $material['label'] }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.input
                            id="calc-quantity"
                            type="number"
                            min="1"
                            value="1000"
                            label="Jumlah Produksi"
                            hint="Dalam Kilogram (Kg)"
                            unit="Kg"
                            x-model.number="quantity"
                        />

                        <x-form.select id="calc-energy" label="Sumber Energi Pabrik" x-model.number="energy">
                            @foreach ($energySources as $energySource)
                                <option value="{{ $energySource['value'] }}">{{ $energySource['label'] }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.input
                            id="calc-distance"
                            type="number"
                            min="0"
                            value="250"
                            label="Jarak Logistik / Pengiriman"
                            hint="Truk Darat / Laut"
                            unit="Km"
                            x-model.number="distance"
                        />

                        <x-ui.button type="button" id="btn-calculate" variant="primary" size="lg" weight="semibold" full-width x-on:click="updateChart()">
                            <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                            <span>Hitung Jejak Karbon</span>
                        </x-ui.button>
                    </form>
                </x-ui.card>

                <x-ui.card tone="white" padding="md" rounded="3xl" class="sm:p-8 lg:col-span-7" aria-live="polite">
                    <h3 class="mb-6 flex items-center justify-between text-xl font-bold text-slate-900">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-square-poll-vertical text-sage-600" aria-hidden="true"></i>
                            <span>Hasil Estimasi Emisi</span>
                        </span>
                        <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Real-time Calculation</span>
                    </h3>

                    <div class="mb-8 rounded-2xl bg-linear-to-br from-slate-900 to-slate-800 p-6 text-white shadow-inner">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Total Estimasi Emisi Karbon</p>
                        <div class="flex items-baseline gap-3">
                            <span class="text-4xl font-black text-emerald-400 sm:text-5xl" x-text="formatNumber(totalEmission)">5.025</span>
                            <span class="text-lg font-medium text-slate-300">kg CO₂e</span>
                        </div>

                        <div class="mt-6 flex items-center justify-between border-t border-slate-700/80 pt-4">
                            <div class="flex items-center gap-2">
                                <span class="flex size-8 items-center justify-center rounded-full text-sm" x-bind:class="isLowerThanIndustry ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'">
                                    <i class="fa-solid" x-bind:class="badgeIcon" aria-hidden="true"></i>
                                </span>
                                <div>
                                    <p class="text-xs text-slate-400">Komparasi Industri</p>
                                    <p class="text-sm font-semibold" x-bind:class="isLowerThanIndustry ? 'text-emerald-300' : 'text-rose-300'" x-text="comparisonText">50% Lebih Rendah dari Rata-rata Industri</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 items-center gap-6 md:grid-cols-2">
                        <div class="relative flex h-56 items-center justify-center">
                            <canvas x-ref="chart" role="img" aria-label="Diagram donat rincian sumber emisi"></canvas>
                        </div>

                        <div class="space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Sumber Emisi</h4>

                            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-emerald-500"></span>
                                    <span class="font-medium text-slate-600">Bahan Baku (Material)</span>
                                </div>
                                <span class="font-bold text-slate-800" x-text="formatNumber(materialEmission) + ' kg'">3.800 kg</span>
                            </div>

                            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-blue-500"></span>
                                    <span class="font-medium text-slate-600">Energi Manufaktur</span>
                                </div>
                                <span class="font-bold text-slate-800" x-text="formatNumber(energyEmission) + ' kg'">1.200 kg</span>
                            </div>

                            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-amber-500"></span>
                                    <span class="font-medium text-slate-600">Logistik &amp; Distribusi</span>
                                </div>
                                <span class="font-bold text-slate-800" x-text="formatNumber(logisticsEmission) + ' kg'">25 kg</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 rounded-2xl border border-gray-200 bg-sage-50/80 p-5">
                        <h4 class="mb-2 flex items-center gap-2 text-sm font-bold text-sage-800">
                            <i class="fa-solid fa-lightbulb text-sage-600" aria-hidden="true"></i>
                            <span>Rekomendasi Pengurangan Emisi</span>
                        </h4>
                        <ul class="list-inside list-disc space-y-1.5 text-xs font-medium text-sage-800">
                            <template x-for="recommendation in recommendations" :key="recommendation">
                                <li x-text="recommendation"></li>
                            </template>
                        </ul>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </section>

    {{-- About --}}
    <section id="tentang" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2">
                <div class="relative">
                    <img src="{{ asset('images/about-kapas.webp') }}" alt="Kapas putih dan coklat Sekar Kawung dalam keranjang anyaman" width="1400" height="918" loading="lazy" decoding="async" class="h-[420px] w-full rounded-3xl object-cover shadow-xl">
                    <div class="absolute -bottom-6 -right-6 hidden max-w-xs rounded-2xl bg-sage-600 p-6 text-white shadow-lg sm:block">
                        <p class="text-sm font-semibold">"Menghubungkan industri tekstil global dengan praktik keberlanjutan berbasis data."</p>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-sage-600">Tentang Sekar Kawung - Sustainable Materials</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Mendorong Transformasi Hijau Sektor Tekstil</h2>
                    <p class="mt-4 text-base leading-relaxed text-slate-600">
                        Sekar Kawung merupakan yayasan sekaligus social enterprise yang didirikan oleh Chandra Kirana Prijosusilo pada tahun 2015 dengan misi memuliakan budaya, menjaga alam, serta menguatkan ekonomi berbasis potensi lokal di Indonesia. Melalui pendekatan yang mengintegrasikan kearifan lokal dengan inovasi modern, seluruh kegiatan yang dijalankan oleh Sekar Kawung berupaya menerapkan sistem berkelanjutan (sustainability) serta prinsip ekonomi sirkular yang menjaga keseimbangan ekosistem dan kesejahteraan rakyat.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Product detail modal --}}
    <x-product.modal />
</x-layouts.app>
