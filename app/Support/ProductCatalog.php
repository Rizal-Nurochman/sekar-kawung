<?php

namespace App\Support;

class ProductCatalog
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            0 => [
                'id' => 1,
                'name' => 'Tenun Intipiyan',
                'category' => 'campuran',
                'categoryName' => 'Kapas Coklat & Putih',
                'spec' => 'Kapas coklat: Gossypium barbadense L.; kapas putih: Gossypium hirsutum L.',
                'emission' => 3.8,
                'cert' => 'Data sertifikasi belum tersedia',
                'image' => '/images/products/tenun-intipiyan.webp',
                'desc' => 'Pola garis berulang membentuk susunan geometris dengan perpaduan warna cokelat dan putih. Terbuat dari kapas coklat (Gossypium barbadense L.) dan kapas putih (Gossypium hirsutum L.).',
                'company' => [
                    'name' => 'Desa Kerek, Tuban',
                    'location' => 'Tuban, Jawa Timur, Indonesia',
                    'moq' => 'Hubungi pengelola',
                    'compliance' => 'Belum tersedia',
                    'energySource' => 'Belum tersedia',
                    'waterUsage' => 'Belum tersedia',
                    'recycledPct' => 'Kapas coklat: Gossypium barbadense L.; kapas putih: Gossypium hirsutum L.',
                    'chemicalInfo' => 'Informasi bahan kimia dan pewarnaan belum tersedia.',
                    'processSteps' => [
                        0 => [
                            'title' => 'Dokumentasi proses',
                            'desc' => 'Rincian proses produksi akan dilengkapi berdasarkan dokumentasi perajin.',
                        ],
                    ],
                ],
            ],
            1 => [
                'id' => 2,
                'name' => 'Tenun Jangan Menir',
                'category' => 'campuran',
                'categoryName' => 'Kapas Coklat & Putih',
                'spec' => 'Kapas coklat: Gossypium barbadense L.; kapas putih: Gossypium hirsutum L.',
                'emission' => 4.2,
                'cert' => 'Data sertifikasi belum tersedia',
                'image' => '/images/products/tenun-jangan-menir.webp',
                'desc' => 'Perpaduan garis halus dan bidang bertekstur dengan aksen garis melintang pada bagian kain. Terbuat dari kapas coklat (Gossypium barbadense L.) dan kapas putih (Gossypium hirsutum L.).',
                'company' => [
                    'name' => 'Desa Kerek, Tuban',
                    'location' => 'Tuban, Jawa Timur, Indonesia',
                    'moq' => 'Hubungi pengelola',
                    'compliance' => 'Belum tersedia',
                    'energySource' => 'Belum tersedia',
                    'waterUsage' => 'Belum tersedia',
                    'recycledPct' => 'Kapas coklat: Gossypium barbadense L.; kapas putih: Gossypium hirsutum L.',
                    'chemicalInfo' => 'Informasi bahan kimia dan pewarnaan belum tersedia.',
                    'processSteps' => [
                        0 => [
                            'title' => 'Dokumentasi proses',
                            'desc' => 'Rincian proses produksi akan dilengkapi berdasarkan dokumentasi perajin.',
                        ],
                    ],
                ],
            ],
            2 => [
                'id' => 3,
                'name' => 'Tenun Kembang Jati',
                'category' => 'campuran',
                'categoryName' => 'Kapas Coklat & Putih',
                'spec' => 'Kapas coklat: Gossypium barbadense L.; kapas putih: Gossypium hirsutum L.',
                'emission' => 2.9,
                'cert' => 'Data sertifikasi belum tersedia',
                'image' => '/images/products/tenun-kembang-jati.webp',
                'desc' => 'Motif kecil berulang tersusun dalam barisan teratur dengan kontras putih di atas dasar cokelat. Terbuat dari kapas coklat (Gossypium barbadense L.) dan kapas putih (Gossypium hirsutum L.).',
                'company' => [
                    'name' => 'Desa Kerek, Tuban',
                    'location' => 'Tuban, Jawa Timur, Indonesia',
                    'moq' => 'Hubungi pengelola',
                    'compliance' => 'Belum tersedia',
                    'energySource' => 'Belum tersedia',
                    'waterUsage' => 'Belum tersedia',
                    'recycledPct' => 'Kapas coklat: Gossypium barbadense L.; kapas putih: Gossypium hirsutum L.',
                    'chemicalInfo' => 'Informasi bahan kimia dan pewarnaan belum tersedia.',
                    'processSteps' => [
                        0 => [
                            'title' => 'Dokumentasi proses',
                            'desc' => 'Rincian proses produksi akan dilengkapi berdasarkan dokumentasi perajin.',
                        ],
                    ],
                ],
            ],
            3 => [
                'id' => 4,
                'name' => 'Tenun Polos Lowo',
                'category' => 'cokelat',
                'categoryName' => 'Kapas Cokelat',
                'spec' => 'Kapas cokelat: Gossypium barbadense L.',
                'emission' => 3.5,
                'cert' => 'Data sertifikasi belum tersedia',
                'image' => '/images/products/tenun-polos-lowo.webp',
                'desc' => 'Tenun polos berwarna cokelat dengan tekstur benang yang tampak alami dan tidak seragam. Terbuat dari kapas cokelat: Gossypium barbadense L..',
                'company' => [
                    'name' => 'Desa Kerek, Tuban',
                    'location' => 'Tuban, Jawa Timur, Indonesia',
                    'moq' => 'Hubungi pengelola',
                    'compliance' => 'Belum tersedia',
                    'energySource' => 'Belum tersedia',
                    'waterUsage' => 'Belum tersedia',
                    'recycledPct' => 'Kapas cokelat: Gossypium barbadense L.',
                    'chemicalInfo' => 'Informasi bahan kimia dan pewarnaan belum tersedia.',
                    'processSteps' => [
                        0 => [
                            'title' => 'Dokumentasi proses',
                            'desc' => 'Rincian proses produksi akan dilengkapi berdasarkan dokumentasi perajin.',
                        ],
                    ],
                ],
            ],
            4 => [
                'id' => 5,
                'name' => 'Tenun Seser Lowo',
                'category' => 'cokelat',
                'categoryName' => 'Kapas Cokelat',
                'spec' => 'Kapas cokelat: Gossypium barbadense L.',
                'emission' => 2.1,
                'cert' => 'Data sertifikasi belum tersedia',
                'image' => '/images/products/tenun-seser-lowo.webp',
                'desc' => 'Tenun bernuansa cokelat dengan garis putih halus yang membentuk tekstur berirama. Terbuat dari kapas cokelat: Gossypium barbadense L..',
                'company' => [
                    'name' => 'Desa Kerek, Tuban',
                    'location' => 'Tuban, Jawa Timur, Indonesia',
                    'moq' => 'Hubungi pengelola',
                    'compliance' => 'Belum tersedia',
                    'energySource' => 'Belum tersedia',
                    'waterUsage' => 'Belum tersedia',
                    'recycledPct' => 'Kapas cokelat: Gossypium barbadense L.',
                    'chemicalInfo' => 'Informasi bahan kimia dan pewarnaan belum tersedia.',
                    'processSteps' => [
                        0 => [
                            'title' => 'Dokumentasi proses',
                            'desc' => 'Rincian proses produksi akan dilengkapi berdasarkan dokumentasi perajin.',
                        ],
                    ],
                ],
            ],
            5 => [
                'id' => 6,
                'name' => 'Tenun Seser Putih',
                'category' => 'putih',
                'categoryName' => 'Kapas Putih',
                'spec' => 'Kapas putih: Gossypium hirsutum L.',
                'emission' => 4.0,
                'cert' => 'Data sertifikasi belum tersedia',
                'image' => '/images/products/tenun-seser-putih.webp',
                'desc' => 'Tenun berwarna putih dengan susunan benang dan garis halus yang memperlihatkan tekstur kain. Terbuat dari kapas putih: Gossypium hirsutum L..',
                'company' => [
                    'name' => 'Desa Kerek, Tuban',
                    'location' => 'Tuban, Jawa Timur, Indonesia',
                    'moq' => 'Hubungi pengelola',
                    'compliance' => 'Belum tersedia',
                    'energySource' => 'Belum tersedia',
                    'waterUsage' => 'Belum tersedia',
                    'recycledPct' => 'Kapas putih: Gossypium hirsutum L.',
                    'chemicalInfo' => 'Informasi bahan kimia dan pewarnaan belum tersedia.',
                    'processSteps' => [
                        0 => [
                            'title' => 'Dokumentasi proses',
                            'desc' => 'Rincian proses produksi akan dilengkapi berdasarkan dokumentasi perajin.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array{id: string, label: string}>
     */
    public static function categories(): array
    {
        return [
            ['id' => 'all', 'label' => 'Semua Jenis'],
            ['id' => 'cokelat', 'label' => 'Kapas Cokelat'],
            ['id' => 'putih', 'label' => 'Kapas Putih'],
            ['id' => 'campuran', 'label' => 'Kapas Coklat & Putih'],
        ];
    }
}
