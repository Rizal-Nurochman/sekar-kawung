@php
    $boundaries = [
        [
            'number' => '01',
            'title' => 'Kain Gedog Tuban',
            'scope' => 'Dari Biji Kapas hingga Kain',
            'summary' => 'Budidaya kapas dan pengolahan manual hingga menjadi kain Gedog tenun tangan.',
            'activities' => [
                'Persiapan tanah dan media tanam',
                'Perawatan tanaman kapas',
                'Panen, penjemuran, dan penyimpanan alami',
                'Pemisahan biji dan penguraian serat secara manual setelah panen',
                'Pemintalan dan pertenunan tangan',
            ],
        ],
        [
            'number' => '02',
            'title' => 'Kain Batik Gedog Tuban',
            'scope' => 'Dari Kain hingga Kain Batik',
            'summary' => 'Batik tulis dan kegiatan pewarnaan yang tercantum untuk kain Gedog Tuban.',
            'activities' => [
                'Batik tulis: malam, penerangan, dan gas kompor',
                'Pewarnaan batik biru: transportasi ke Temanggung, gas perebusan, pasta Strobilanthes cussia, dan penjemuran',
                'Pewarnaan batik cokelat: serbuk gergaji mahoni, kayu bakar, dan penjemuran',
                'Warna batik lainnya: transportasi ke DIY, bahan tanaman, kayu bakar, dan penjemuran',
            ],
        ],
        [
            'number' => '03',
            'title' => 'Kain Tenun Pisano (ATBM)',
            'scope' => 'Dari Biji Kapas hingga Kain',
            'summary' => 'Budidaya kapas, persiapan benang berbantuan mesin, dan pertenunan tangan ATBM, termasuk tahap transportasi yang tercantum.',
            'activities' => [
                'Persiapan tanah dan media tanam',
                'Perawatan tanaman kapas',
                'Panen, penjemuran, dan penyimpanan alami',
                'Transportasi pascapanen, pemisahan biji dengan mesin, dan penguraian serat dengan listrik',
                'Produksi benang menjadi kain: transportasi sliver, pemintalan pabrik, pertenunan ATBM di Karangasem, Klaten, dan transportasi kembali ke Yogya',
            ],
        ],
        [
            'number' => '04',
            'title' => 'Kain Batik Tenun Pisano (ATBM)',
            'scope' => 'Dari Kain hingga Kain Batik',
            'summary' => 'Batik tulis dan tahap pewarnaan biru serta putih yang tercantum untuk kain ATBM Pisano.',
            'activities' => [
                'Batik tulis: transportasi ke Tancep/DIY, malam, penerangan, dan gas kompor',
                'Pewarnaan batik biru: transportasi ke Temanggung, gas perebusan, pasta Strobilanthes cussia, dan penjemuran',
                'Pewarnaan batik putih: transportasi ke DIY, daun, kulit kayu, akar atau bunga, kayu bakar, dan penjemuran',
            ],
        ],
    ];

@endphp

<x-landing.layouts.app
    title="Tentang Sekar Kawung | Memahami Jejak di Balik Setiap Kain"
    description="Pelajari bagaimana Sekar Kawung menjelaskan batas produksi tekstil, data aktivitas, faktor emisi, dan jejak karbon produk."
    hero-background="bg-white"
>
    <x-slot:hero>
        <section class="mx-auto grid w-full max-w-[1200px] items-center gap-10 px-4 pb-20 pt-14 text-pine-900 sm:px-6 lg:grid-cols-[minmax(0,688px)_minmax(0,448px)] lg:gap-16 lg:px-0 lg:pb-24 lg:pt-[88px]" aria-labelledby="about-heading">
            <div>
                <p class="text-base font-bold leading-6 tracking-[0.08em]">TENTANG SEKAR KAWUNG</p>
                <h1 id="about-heading" class="mt-6 max-w-[688px] text-4xl font-bold leading-tight tracking-[-0.02em] sm:text-5xl sm:leading-[1.2]">
                    Memahami Jejak di Balik Setiap Kain
                </h1>
                <div class="mt-6 space-y-[18px] text-base leading-[30px] lg:text-justify">
                    <p>Kalkulator Karbon Sekar Kawung membantu pengguna memperkirakan emisi gas rumah kaca dari produksi tekstil, mulai dari budidaya biji kapas hingga kain jadi dalam batas produksi yang ditetapkan.</p>
                    <p>Dengan mencatat data aktivitas, penggunaan material, dan konsumsi energi, pengguna dapat lebih memahami bagaimana setiap kegiatan produksi berkontribusi terhadap jejak karbon produk. Hasilnya menjadi titik awal untuk meninjau penggunaan sumber daya dan mengidentifikasi peluang perbaikan.</p>
                </div>
            </div>
            <img
                src="{{ asset('images/landing/about-environment.png') }}"
                alt="Ilustrasi bumi dengan bangunan, pohon, dan energi terbarukan"
                width="1254"
                height="1254"
                class="mx-auto h-auto w-full max-w-[448px] object-contain"
                fetchpriority="high"
            >
        </section>
    </x-slot:hero>

    <section class="bg-mist-100 py-8 text-pine-900" aria-labelledby="report-heading">
        <div class="mx-auto grid w-full max-w-[1200px] gap-10 px-4 sm:px-6 lg:grid-cols-[600px_520px] lg:gap-20 lg:px-0">
            <div class="space-y-6">
                <h2 id="report-heading" class="text-3xl font-bold leading-tight tracking-[-0.02em] sm:text-[40px] sm:leading-[48px]">Apa Itu Laporan Jejak Karbon Produk?</h2>
                <p class="text-lg leading-[30px] lg:text-justify">Laporan Jejak Karbon Produk merangkum perkiraan emisi gas rumah kaca suatu produk dalam batas produksi yang ditetapkan. Laporan ini menjelaskan apa yang dinilai, kegiatan apa saja yang disertakan, dan bagaimana hasilnya dihitung.</p>
                <p class="text-lg leading-[30px] lg:text-justify">Hasil dinyatakan dalam ekuivalen karbon dioksida (CO₂e) dan perlu dibaca bersama kuantitas produk, periode pelaporan, sumber data, serta asumsi yang dipakai dalam penilaian.</p>
            </div>
            <div>
                <h3 class="text-2xl font-bold leading-8">Cakupan Laporan</h3>
                <ul class="mt-6 space-y-4 text-lg leading-[30px]">
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Produk dan kuantitas yang dinilai</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Batas produksi dan kegiatan yang disertakan</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Data aktivitas dan faktor emisi</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Total perkiraan emisi dan rincian aktivitas</span></li>
                    <li class="flex gap-[14px]"><span aria-hidden="true">—</span><span>Sumber data, asumsi, dan keterbatasan</span></li>
                </ul>
                <p class="mt-6 text-base leading-6">Emisi per satuan produk hanya ditampilkan jika kuantitas keluaran dan satuannya sudah ditetapkan.</p>
            </div>
        </div>
    </section>

    <section class="bg-white pb-20 pt-8 text-pine-900" aria-labelledby="boundaries-heading">
        <div class="mx-auto w-full max-w-[1200px] px-4 sm:px-6 lg:px-0">
            <h2 id="boundaries-heading" class="text-3xl font-bold leading-tight tracking-[-0.02em] sm:text-[40px] sm:leading-[48px]">Empat Boundary Produksi</h2>
            <p class="mt-5 text-lg leading-[30px]">Boundary produksi menentukan kegiatan mana yang disertakan dalam perhitungan jejak karbon serta di mana penilaian dimulai dan berakhir. Kalkulator mencakup empat jalur produksi, masing-masing dengan rangkaian kegiatannya sendiri.</p>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                @foreach ($boundaries as $boundary)
                    <article class="min-h-[560px] rounded-2xl border border-pine-900 bg-mist-100 p-6 sm:p-8">
                        <p class="text-base font-bold leading-6">{{ $boundary['number'] }}</p>
                        <h3 class="mt-3 text-2xl font-bold leading-8">{{ $boundary['title'] }}</h3>
                        <p class="mt-3 text-base leading-6">{{ $boundary['scope'] }}</p>
                        <p class="mt-5 text-lg leading-[30px]">{{ $boundary['summary'] }}</p>
                        <h4 class="mt-5 text-base font-bold leading-6">Kegiatan yang Disertakan</h4>
                        <ul class="mt-3 space-y-2 text-lg leading-[30px]">
                            @foreach ($boundary['activities'] as $activity)
                                <li class="flex gap-3"><span aria-hidden="true">•</span><span>{{ $activity }}</span></li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

</x-landing.layouts.app>
