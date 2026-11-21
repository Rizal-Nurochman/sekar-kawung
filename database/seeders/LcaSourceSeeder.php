<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class LcaSourceSeeder extends Seeder
{
    /**
     * @var array<int, array{id: int, title: string, link_reference: string|null}>
     */
    private const array SOURCES = [
        [
            'id' => 1,
            'title' => 'IPCC 2019',
            'link_reference' => 'https://www.ipcc-nggip.iges.or.jp/public/2019rf/pdf/4_Volume4/19R_V4_Ch11_Soils_N2O_CO2.pdf',
        ],
        [
            'id' => 2,
            'title' => 'HONG KONG GREEN ORGANISATION CERTIFICATION',
            'link_reference' => 'https://www.hkgoc.gov.hk/uploads/2023%20CRC-Type%20III-Guidebook_Eng.pdf',
        ],
        [
            'id' => 3,
            'title' => 'Keputusan Menteri Energi dan Sumber Daya Mineral Republik Indonesia Nomor 163.K/HK.02/MEM.S/2021',
            'link_reference' => 'https://jdih.esdm.go.id/common/dokumen-external/Kepmen%20ESDM%20No.%20163.K_HK.02-MEM.S-2021.pdf',
        ],
        [
            'id' => 4,
            'title' => 'life waste 4 green',
            'link_reference' => 'https://waste4green.eu/en/monitoring-impact-actions',
        ],
        [
            'id' => 5,
            'title' => 'INVENTARISASI GAS RUMAH KACA KOTA SURAKARTA & UNFCC',
            'link_reference' => 'Laporan Akhir IGRK 2024.pdf dan https://unfccc.int/sites/default/files/resource/FI_NID_UN_2022_2024-12-16.pdf',
        ],
        [
            'id' => 6,
            'title' => 'Keputusan Menteri Energi dan Sumber Daya Mineral Republik Indonesia Nomor 163.K/HK.02/MEM.S/2021',
            'link_reference' => null,
        ],
        [
            'id' => 7,
            'title' => 'Nepal Emission Inventory (NEEMI)',
            'link_reference' => 'https://www.researchgate.net/profile/Maheswar-Rupakheti/publication/331613262_Nepal_Emission_Inventory_NEEMI_a_high_resolution_technology-based_bottom-up_emissions_inventory_for_Nepal_2001-2016/links/5c841273299bf1268d4c7389/Nepal-Emission-Inventory-NEEMI-a_high_resolution_technology-based_bottom-up_emissions_inventory_for_Nepal_2001-2016.pdf',
        ],
        [
            'id' => 8,
            'title' => 'WRI Indonesia (2021)',
            'link_reference' => 'https://wri-indonesia.org/id/publikasi/aplikasi-nol-emisi-indonesia-emisi-metode-penghitungan-emisi-dari-transportasi-dan?utm_source=chatgpt.com',
        ],
        [
            'id' => 9,
            'title' => 'WRI Indonesia (2021)',
            'link_reference' => 'https://wri-indonesia.org/en/publications/indonesia-zero-emissions-application-emisi-methodology-calculating-individual-0?__cf_chl_rt_tk=KDI86GyBA4Rpx1W3novqrRU.HLlgZGYf9FwI7yPCTLM-1794844870-1.0.1.1-aZibRkTqZLndMdx1VNCFLnnAGApnw4uIKThXvzD4LPI',
        ],
        [
            'id' => 10,
            'title' => 'WRI Indonesia (2021)',
            'link_reference' => 'https://wri-indonesia.org/sites/default/files/Indonesia_Zero_Emissions_Application_EMISI_Methodology_for_Calculating.pdf',
        ],
        [
            'id' => 11,
            'title' => 'WRI Indonesia (2020)',
            'link_reference' => 'https://wri-indonesia.org/sites/default/files/TN_Indonesia%20Zero%20Emissions%20Application%20%28EMISI%29.pdf',
        ],
        [
            'id' => 12,
            'title' => 'WRI Indonesia (2020)',
            'link_reference' => null,
        ],
        [
            'id' => 13,
            'title' => 'Survei Lapangan Tuban (Bu Samiun, Bu Uswatun) & WRI Indonesia (2021)',
            'link_reference' => 'https://wri-indonesia.org/sites/default/files/Indonesia_Zero_Emissions_Application_EMISI_Methodology_for_Calculating.pdf',
        ],
    ];

    public function run(): void
    {
        Source::upsert(
            self::SOURCES,
            uniqueBy: ['id'],
            update: ['title', 'link_reference'],
        );
    }
}
