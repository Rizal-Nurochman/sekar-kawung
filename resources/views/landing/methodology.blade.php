@php
    $concepts = [
        ['title' => 'Data Aktivitas', 'description' => 'Jumlah terukur suatu material, bahan bakar, listrik, atau sumber daya lain yang dipakai dalam kegiatan produksi.'],
        ['title' => 'Faktor Emisi', 'description' => 'Nilai yang menghubungkan jumlah aktivitas atau penggunaan sumber daya dengan emisi gas rumah kaca yang dihasilkannya.'],
        ['title' => 'Ekuivalen Karbon Dioksida', 'description' => 'Satuan umum untuk menyatakan efek pemanasan berbagai gas rumah kaca relatif terhadap karbon dioksida.'],
        ['title' => 'Kontribusi Aktivitas', 'description' => 'Rincian yang menunjukkan seberapa besar setiap kegiatan yang disertakan berkontribusi terhadap perkiraan jejak produk.'],
    ];

    $inputs = [
        ['number' => '01', 'title' => 'Data Aktivitas', 'description' => 'Kuantitas sumber daya yang dipakai dalam kegiatan produksi yang disertakan, dicatat untuk periode penilaian dan kuantitas produksi yang konsisten.'],
        ['number' => '02', 'title' => 'Faktor Emisi', 'description' => 'Nilai yang menyatakan emisi gas rumah kaca per satuan aktivitas. Sumber, satuan, relevansi geografis, dan tahun referensinya perlu didokumentasikan.'],
        ['number' => '03', 'title' => 'Perkiraan Emisi', 'description' => 'Kontribusi terhitung dari suatu aktivitas, dinyatakan dalam ekuivalen karbon dioksida (CO₂e) jika faktor yang dipilih mewakili gabungan emisi gas rumah kaca.'],
    ];

    $examples = [
        ['title' => 'Penggunaan Listrik', 'description' => 'Kalikan konsumsi listrik dengan faktor emisi listrik yang sesuai untuk lokasi dan periode penilaian.', 'equation' => 'Listrik Terpakai (kWh) × Faktor Emisi (kg CO₂e/kWh) = Emisi (kg CO₂e)', 'icon' => 'electricity'],
        ['title' => 'Penggunaan Material', 'description' => 'Kalikan kuantitas material dengan faktor emisi yang sesuai dengan material dan proses yang diwakili faktor tersebut.', 'equation' => 'Material Terpakai (kg) × Faktor Emisi (kg CO₂e/kg) = Emisi (kg CO₂e)', 'icon' => 'material'],
    ];

    $factorDetails = [
        'Judul sumber serta penulis atau organisasi',
        'Tahun terbit',
        'Material atau aktivitas yang diwakili',
        'Nilai dan satuan faktor',
        'Cakupan geografis dan proses',
        'Tautan sumber asli',
        'Asumsi yang relevan',
    ];
@endphp

<x-landing.layouts.app
    title="Metodologi | Sekar Kawung"
    description="Cara Kalkulator Karbon Sekar Kawung memperkirakan emisi produksi tekstil menggunakan data aktivitas dan faktor emisi."
    hero-background="bg-white"
>
    <x-slot:hero>
        <section class="mx-auto w-full max-w-[1200px] px-4 pb-9 pt-16 text-pine-900 sm:px-6 lg:px-0" aria-labelledby="methodology-heading">
            <h1 id="methodology-heading" class="text-4xl font-bold leading-tight tracking-[-0.02em] sm:text-5xl sm:leading-[58px]">Metodologi</h1>
            <p class="mt-5 text-lg leading-[30px] lg:text-justify">Kalkulator Karbon Sekar Kawung memakai data aktivitas untuk memperkirakan emisi gas rumah kaca dalam batas produksi tekstil yang ditetapkan. Penggunaan material dan energi dihubungkan dengan faktor emisi yang relevan untuk menghitung kontribusi setiap kegiatan yang disertakan. Penilaian mengikuti kegiatan produksi yang tercatat untuk batas yang dipilih. Hasilnya perlu ditafsirkan bersama sumber data, satuan, dan asumsi yang dipakai.</p>
        </section>
    </x-slot:hero>

    <section class="bg-white pb-9 text-pine-900" aria-labelledby="footprints-heading">
        <div class="mx-auto w-full max-w-[1200px] border-t border-mist-100 px-4 pt-9 sm:px-6 lg:px-0">
            <h2 id="footprints-heading" class="text-3xl font-bold leading-tight tracking-[-0.02em] sm:text-[40px] sm:leading-[48px]">Memahami Emisi Karbon dan Jejak Produk</h2>
            <p class="mt-5 text-lg leading-[30px] lg:text-justify">Emisi gas rumah kaca mencakup karbon dioksida (CO₂), metana (CH₄), dan dinitrogen oksida (N₂O), yang berkontribusi terhadap perubahan iklim. Jejak karbon produk menggabungkan emisi tersebut dari kegiatan yang disertakan dalam batas penilaian. Gas-gas itu dinyatakan dalam ekuivalen karbon dioksida (CO₂e) sehingga efek pemanasannya yang berbeda dapat diwakili dengan satuan yang sama.</p>

            <div class="mt-9 grid gap-7 sm:grid-cols-2 sm:gap-x-12">
                @foreach ($concepts as $concept)
                    <div class="rounded-2xl border border-mist-100 px-4 pb-4">
                        <div class="h-px bg-mist-100"></div>
                        <h3 class="mt-4 text-[22px] font-bold leading-[30px]">{{ $concept['title'] }}</h3>
                        <p class="mt-4 text-lg leading-[30px]">{{ $concept['description'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-9 rounded-xl border border-pine-900 bg-mist-100 px-6 py-4 sm:px-8">
                <p class="text-center text-2xl font-bold leading-9 sm:text-[26px]">PCF = Σ (ADᵢ × EFᵢ)</p>
                <p class="mt-4 text-center text-lg font-bold leading-8 sm:text-[26px] sm:leading-9">Jejak Karbon Produk = Data Aktivitas × Faktor Emisi</p>
                <p class="mt-4 text-base leading-6">Satuan aktivitas harus sama dengan satuan yang dipakai faktor emisi. Total emisi dihitung dengan menjumlahkan kontribusi yang disertakan tanpa penghitungan ganda.</p>
            </div>

            <h3 class="mt-9 text-[32px] font-bold leading-10 tracking-[-0.02em]">Memahami Input</h3>
            <dl class="mt-9 border-t border-mist-100">
                @foreach ($inputs as $input)
                    <div class="grid gap-3 border-b border-mist-100 py-6 sm:grid-cols-[30px_244px_minmax(0,1fr)] sm:gap-8">
                        <span class="flex size-[30px] items-center justify-center rounded-lg bg-pine-900 text-lg font-bold leading-[30px] text-mist-100" aria-hidden="true">{{ $input['number'] }}</span>
                        <dt class="text-[22px] font-bold leading-[30px]">{{ $input['title'] }}</dt>
                        <dd class="text-lg leading-[30px] lg:text-justify">{{ $input['description'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="bg-white py-9 text-pine-900" aria-labelledby="examples-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <h2 id="examples-heading" class="text-[32px] font-bold leading-10 tracking-[-0.02em]">Contoh Perhitungan</h2>
            <div class="mt-6 space-y-6">
                @foreach ($examples as $example)
                    <article class="flex flex-col gap-6 rounded-xl bg-mist-100 p-6 sm:flex-row sm:p-8">
                        <div class="relative h-11 w-12 shrink-0" aria-hidden="true">
                            @if ($example['icon'] === 'electricity')
                                <img src="{{ asset('images/landing/methodology-lightning.svg') }}" alt="" width="22" height="34" class="ml-2 mt-1">
                            @else
                                <img src="{{ asset('images/landing/methodology-layer-top.svg') }}" alt="" width="32" height="18" class="absolute left-1 top-1">
                                <img src="{{ asset('images/landing/methodology-layer-middle.svg') }}" alt="" width="32" height="10" class="absolute left-1 top-[18px]">
                                <img src="{{ asset('images/landing/methodology-layer-middle.svg') }}" alt="" width="32" height="10" class="absolute left-1 top-6">
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-2xl font-bold leading-8">{{ $example['title'] }}</h3>
                            <p class="mt-3 text-lg leading-[30px]">{{ $example['description'] }}</p>
                            <p class="mt-3 text-lg font-bold leading-[30px]">{{ $example['equation'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-9 text-pine-900" aria-labelledby="factors-heading">
        <div class="mx-auto grid w-full max-w-[1200px] gap-8 px-4 sm:px-6 lg:grid-cols-[600px_minmax(0,1fr)] lg:gap-16 lg:px-0">
            <div>
                <h2 id="factors-heading" class="text-[32px] font-bold leading-10 tracking-[-0.02em]">Faktor Emisi dan Sumber Data</h2>
                <p class="mt-5 text-lg leading-[30px]">Faktor emisi dipilih berdasarkan relevansinya terhadap material, sumber energi, proses, lokasi, dan periode penilaian. Paper riset, laporan teknis, dan kumpulan data terdokumentasi dapat menjadi informasi pendukung.</p>
                <p class="mt-5 text-lg leading-[30px]">Setiap faktor yang dipakai dalam perhitungan harus dapat dilacak ke sumber aslinya. Perbedaan batas sumber dan satuan perlu ditinjau sebelum menerapkan suatu faktor.</p>
            </div>
            <div class="rounded-xl bg-mist-100 p-7">
                <h3 class="text-2xl font-bold leading-8">Untuk Setiap Faktor, Catat:</h3>
                <ul class="mt-3 space-y-3 text-base leading-6">
                    @foreach ($factorDetails as $detail)
                        <li class="flex gap-2"><span aria-hidden="true">•</span><span>{{ $detail }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section id="references" class="bg-white pb-[72px] pt-9 text-pine-900" aria-labelledby="references-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <h2 id="references-heading" class="text-[32px] font-bold leading-10 tracking-[-0.02em]">Riset dan Referensi</h2>
            <p class="mt-6 text-lg leading-[30px]">Telusuri dokumen riset dan sumber yang mendukung metodologi perhitungan serta faktor emisi.</p>

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                @foreach (range(1, 4) as $number)
                    <article class="flex min-h-[300px] flex-col gap-4 rounded-xl border border-mist-100 bg-white p-6">
                        <div class="flex items-start justify-between gap-3">
                            <span class="rounded-full bg-mist-100 px-[10px] py-1 text-base font-bold leading-6">Referensi akan ditambahkan</span>
                            <span class="text-base font-bold leading-6">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-[22px] font-bold leading-[30px]">[Judul Lengkap Paper atau Laporan]</h3>
                        <p class="text-base leading-6">[Penulis / Organisasi] · [Tahun]</p>
                        <p class="text-base leading-6">[Penjelasan singkat tentang bagaimana sumber ini mendukung metodologi atau faktor emisi.]</p>
                        <div class="mt-auto text-base leading-6">
                            <p class="font-bold opacity-65">Lihat Sumber Asli ↗</p>
                            <p class="mt-1 opacity-80">Tautan sumber belum tersedia</p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-6 text-right">
                <button type="button" disabled title="Referensi belum tersedia" class="inline-flex items-center gap-2 rounded-xl bg-pine-900 px-[33px] py-[15px] text-base font-bold leading-5 text-white">Lihat Semua Referensi <img src="{{ asset('images/landing/methodology-arrow.svg') }}" alt="" width="16" height="16"></button>
            </div>
        </div>
    </section>
</x-landing.layouts.app>
