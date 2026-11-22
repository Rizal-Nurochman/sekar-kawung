# Product Requirements Document (PRD): Sekar Kawung LCA & Carbon Footprint Platform

## 1. Product Overview

This project is a dual-purpose **Life Cycle Assessment (LCA) and Carbon Footprint Platform** built around traditional Indonesian handwoven textiles (**Tenun Gedog Tuban** and **Tenun ATBM Pisano**). Structuring each textile line (`scheme`) into **two progressive Cradle-to-Gate system boundaries** (`processes`)—from raw cotton cultivation up to plain woven fabric, and extending through hand-drawn waxing (*Membatik*) and natural dyeing (*Pewarnaan*) to finalized batik—the application serves two distinct workflows from one unified LCI/LCIA engine:

1. **Internal Sustainability Reporting (Sekar Kawung CMS):** An authenticated `/admin` workspace where Sekar Kawung staff input official production inventory data, save verified footprints to the database, and publish them to a public-facing **Product Report** page that reads like a curated, static sustainability disclosure.
2. **Interactive Public Calculator (Guest Mode):** A stateless, public-facing calculator on the main site allowing visitors, researchers, and buyers to simulate textile carbon footprints on the fly without persisting data to the database.

---

## 2. Target Users & Access Modes

| User Persona | Interface & Route | Persistence | Primary Purpose |
| --- | --- | --- | --- |
| **Sekar Kawung Admin**<br>

<br>*(Internal Sustainability Team)* | `/admin` (Authenticated CMS) | **Saved in DB** & published to public Product Report page | Input verified production quantities across all lifecycle stages, manage published product reports, and maintain master emission factors. |
| **Public Visitor / Guest**<br>

<br>*(Researchers, Buyers, General Public)* | Main Page (`/calculator` & `/reports`) | **Stateless (Not stored in DB)** | Read Sekar Kawung's published sustainability reports and test custom LCI scenarios using the interactive public calculator. |

---

## 3. Functional Requirements

### FR-1: Shared Master LCI/LCIA Engine

Both modes query the same master hierarchy (`scheme` $\rightarrow$ `processes` $\rightarrow$ `subprocesses` $\rightarrow$ `activities` / `options`) and apply identical calculation logic:

* **Boundary Resolution:** Selecting Boundary 1 (*Kain*) loads `order = 1` subprocesses; selecting Boundary 2 (*Kain Batik*) cumulatively loads `order = 1` + `order = 2`.


* **Activity Archetypes:**
* *Standalone Quantifiable:* Renders numeric input + `activity_units.unit_name`.


* *Multi-Option (`options`):* Renders dropdown (e.g., *Sepeda Motor*, *Mobil-Bensin*, *Mobil-Diesel*, *Tosa*, *Truk*, *RON 92*, *RON 98*), dynamically resolving the option's `activity_unit_id` and `emission_factor_id` (or `0` for *Tosa*).


* *Zero-Emission Heritage Step:* Renders documented manual tasks (e.g., *Cangkul*, *Handpicked*, *Hand-weaving*, *Jemur Matahari*) with a `0 kg CO₂e` badge.




* **Calculation Formula:**

$$\text{Emission} = \text{Quantity} \times (\text{Option EF} \lor \text{Activity EF} \lor 0)$$



---

### FR-2: Mode A — Sekar Kawung Admin CMS & Public Product Report

* **Admin Report Builder (`/admin/reports`):**
* Allows Sekar Kawung admins to create, edit, and publish product sustainability reports (e.g., *Laporan Keberlanjutan Kain Batik Gedog Tuban 2026*).
* Displays the full LCI form for the chosen `scheme` and boundary (`process_id`), allowing admins to enter actual production quantities, pick applicable transport/fuel `option_id`s, and optionally add narrative notes or batch metadata (e.g., functional unit, production period, batch weight).


* Supports **Draft** vs. **Published** states so incomplete entries stay private until verified.


* **Public Product Report Page (`/reports/{slug}`):**
* Renders Sekar Kawung's published data in a clean, editorial/static-style layout—displaying total $\text{kg CO}_2\text{e}$, stage-by-stage breakdown charts, hotspot highlights, and full scientific source citations (`sources.title` + `sources.link_reference`)—dynamically powered by the CMS database.





---

### FR-3: Mode B — Public Guest Calculator

* **Stateless Simulation (`/` or `/calculator`):**
* Accessible without login on the main landing page.
* Visitors choose a `scheme` (*Gedog Tuban* or *Tenun ATBM Pisano*) and boundary (*Kain* or *Kain Batik*), then enter hypothetical quantities and transport/fuel options.




* **Zero Database Persistence:**
* Calculations run either client-side (using master schema JSON passed to the frontend) or via a stateless API endpoint (`POST /api/calculate`) that computes totals and returns the breakdown immediately **without writing user inputs to the database**.


* **Instant Result Summary:**
* Displays total $\text{kg CO}_2\text{e}$, subprocess breakdown, top emission hotspots, and cited emission factors in real time, with an optional "Reset" or "Print / Export PDF" view in the browser.



---

## 4. Database Extension for Sekar Kawung CMS

Your existing 9 tables (`scheme`, `processes`, `subprocesses`, `activities`, `options`, `activity_option`, `activity_units`, `emission_factors`, `sources`) remain untouched as the **Master Reference Layer**. To support Sekar Kawung's saved sustainability reports without affecting the stateless guest calculator, add two lightweight CMS tables:

### Additional CMS Tables

* **`sustainability_reports`** — Stores Sekar Kawung's published report headers shown on the main Product Report page.
* `report_id` (`Integer`, PK, Not Null)
* `scheme_id` (`Integer`, FK $\rightarrow$ `scheme.scheme_id`, Not Null)


* `process_id` (`Integer`, FK $\rightarrow$ `processes.process_id`, Not Null) — Target boundary (`1`–`4`)


* `title` (`String`, Not Null) — Public report title (e.g., *Kain Batik Gedog Tuban — Batch 2026*)
* `slug` (`String`, Unique, Not Null) — URL-friendly identifier for `/reports/{slug}`
* `summary` (`Text`, Nullable) — Editorial notes shown on the public report page
* `total_emission` (`Decimal(12, 4)`, Not Null) — Cached total $\text{kg CO}_2\text{e}$
* `is_published` (`Boolean`, Default `false`) — Controls visibility on the main page
* `timestamps`


* **`sustainability_report_items`** — Stores Sekar Kawung's filled-in LCI inputs and calculated line-item emissions for each report.
* `report_item_id` (`Integer`, PK, Not Null)
* `report_id` (`Integer`, FK $\rightarrow$ `sustainability_reports.report_id`, Cascade Delete)
* `activity_id` (`Integer`, FK $\rightarrow$ `activities.activity_id`, Not Null)


* `option_id` (`Integer`, FK $\rightarrow$ `options.option_id`, Nullable) — Selected vehicle/fuel option if applicable


* `quantity` (`Decimal(12, 4)`, Default `0`) — Admin-entered activity quantity
* `emission_result` (`Decimal(12, 4)`, Default `0`) — Calculated $\text{kg CO}_2\text{e}$ snapshot at save time



### Additional Cardinalities

* **`scheme` one to many `sustainability_reports**`


* **`processes` one to many `sustainability_reports**`


* **`sustainability_reports` one to many `sustainability_report_items**`
* **`activities` one to many `sustainability_report_items**`


* **`options` one to many `sustainability_report_items**`


### Seeder 

BEGIN;

-- ============================================================================
-- 1. SCHEME
-- ============================================================================
INSERT INTO scheme (scheme_id, description) VALUES
(1, 'Gedog Tuban'),
(2, 'Tenun ATBM Pisano');

-- ============================================================================
-- 2. PROCESSES (Progressive Cradle-to-Gate Boundaries)
-- ============================================================================
INSERT INTO processes (process_id, scheme_id, "order", description) VALUES
(1, 1, 1, 'Proses Produksi dari Biji Sampai Jadi Kain Gedog Tuban'),
(2, 1, 2, 'Proses Produksi dari Biji Sampai Jadi Kain Batik Gedog Tuban'),
(3, 2, 1, 'Proses Produksi dari Biji Sampai Jadi Kain TENUN ATBM PISANO'),
(4, 2, 2, 'Proses Produksi dari Biji Sampai Jadi Kain BATIK TENUN ATBM PISANO');

-- ============================================================================
-- 3. SUBPROCESSES
-- ============================================================================
INSERT INTO subprocesses (subprocess_id, process_id, "order", description) VALUES
-- Process 1: Kain Gedog Tuban
(1,  1, 1, 'Mengolah Tanah/media tanam'),
(2,  1, 2, 'Proses Pemeliharaan Tanaman Kapas'),
(3,  1, 3, 'Proses Pemanenan'),
(4,  1, 4, 'Proses Paska Panen'),
(5,  1, 5, 'Proses Pembuatan Benang – hingga Jadi kain'),

-- Process 2: Kain Batik Gedog Tuban
(6,  2, 1, 'Proses Membatik tulis tangan'),
(7,  2, 2, 'Proses Pewarnaan Batik (Biru)'),
(8,  2, 3, 'Proses Pewarnaan Batik (Cokelat)'),
(9,  2, 4, 'Proses Pewarnaan Batik (Warna Lainnya)'),

-- Process 3: Kain TENUN ATBM PISANO
(10, 3, 1, 'Mengolah Tanah/media tanam'),
(11, 3, 2, 'Proses Pemeliharaan Tanaman Kapas'),
(12, 3, 3, 'Proses Pemanenan'),
(13, 3, 4, 'Proses Paska Panen'),
(14, 3, 5, 'Proses Pembuatan Benang – hingga Jadi kain'),

-- Process 4: Kain BATIK TENUN ATBM PISANO
(15, 4, 1, 'Proses Membatik tulis tangan'),
(16, 4, 2, 'Proses Pewarnaan Batik (Biru)'),
(17, 4, 3, 'Proses Pewarnaan Batik (Putih)');

-- ============================================================================
-- 4. ACTIVITY UNITS
-- ============================================================================
INSERT INTO activity_units (activity_unit_id, unit_name) VALUES
(1,  'kg pupuk agregat'),
(2,  'Liter solar/diesel'),
(3,  'kWh listrik'),
(4,  'kg pestisida organik'),
(5,  'kg malam (paraffin wax)'),
(6,  'kWh'),
(7,  'kg LPG'),
(8,  'km'),
(9,  'kg kayu bakar'),
(10, 'Liter bensin');

-- ============================================================================
-- 5. SOURCES
-- ============================================================================
INSERT INTO sources (source_id, title, link_reference) VALUES
(1,  'IPCC 2019', 'https://www.ipcc-nggip.iges.or.jp/public/2019rf/pdf/4_Volume4/19R_V4_Ch11_Soils_N2O_CO2.pdf'),
(2,  'HONG KONG GREEN ORGANISATION CERTIFICATION', 'https://www.hkgoc.gov.hk/uploads/2023%20CRC-Type%20III-Guidebook_Eng.pdf'),
(3,  'Keputusan Menteri Energi dan Sumber Daya Mineral Republik Indonesia Nomor 163.K/HK.02/MEM.S/2021', 'https://jdih.esdm.go.id/common/dokumen-external/Kepmen%20ESDM%20No.%20163.K_HK.02-MEM.S-2021.pdf'),
(4,  'life waste 4 green', 'https://waste4green.eu/en/monitoring-impact-actions'),
(5,  'INVENTARISASI GAS RUMAH KACA KOTA SURAKARTA & UNFCC', 'Laporan Akhir IGRK 2024.pdf dan https://unfccc.int/sites/default/files/resource/FI_NID_UN_2022_2024-12-16.pdf'),
(6,  'Keputusan Menteri Energi dan Sumber Daya Mineral Republik Indonesia Nomor 163.K/HK.02/MEM.S/2021', NULL),
(7,  'Nepal Emission Inventory (NEEMI)', 'https://www.researchgate.net/profile/Maheswar-Rupakheti/publication/331613262_Nepal_Emission_Inventory_NEEMI_a_high_resolution_technology-based_bottom-up_emissions_inventory_for_Nepal_2001-2016/links/5c841273299bf1268d4c7389/Nepal-Emission-Inventory-NEEMI-a_high_resolution_technology-based_bottom-up-emissions-inventory-for-Nepal-2001-2016.pdf'),
(8,  'WRI Indonesia (2021)', 'https://wri-indonesia.org/id/publikasi/aplikasi-nol-emisi-indonesia-emisi-metode-penghitungan-emisi-dari-transportasi-dan?utm_source=chatgpt.com'),
(9,  'WRI Indonesia (2021)', 'https://wri-indonesia.org/en/publications/indonesia-zero-emissions-application-emisi-methodology-calculating-individual-0?__cf_chl_rt_tk=KDI86GyBA4Rpx1W3novqrRU.HLlgZGYf9FwI7yPCTLM-1794844870-1.0.1.1-aZibRkTqZLndMdx1VNCFLnnAGApnw4uIKThXvzD4LPI'),
(10, 'WRI Indonesia (2021)', 'https://wri-indonesia.org/sites/default/files/Indonesia_Zero_Emissions_Application_EMISI_Methodology_for_Calculating.pdf'),
(11, 'WRI Indonesia (2020)', 'https://wri-indonesia.org/sites/default/files/TN_Indonesia%20Zero%20Emissions%20Application%20%28EMISI%29.pdf'),
(12, 'WRI Indonesia (2020)', NULL),
(13, 'Survei Lapangan Tuban (Bu Samiun, Bu Uswatun) & WRI Indonesia (2021)', 'https://wri-indonesia.org/sites/default/files/Indonesia_Zero_Emissions_Application_EMISI_Methodology_for_Calculating.pdf');

-- ============================================================================
-- 6. EMISSION FACTORS
-- ============================================================================
INSERT INTO emission_factors (emission_factor_id, source_id, unit_name, ef_value) VALUES
(1,  1,  'kg N₂O-N',                      0.0100),
(2,  2,  'kg CO₂/liter solar/diesel',    2.6140),
(3,  3,  'kg CO₂/kWh',                   0.8700),
(4,  4,  'kg CO₂e/kg pestisida organik', 0.9000),
(5,  5,  'kg CO₂/kg malam',              0.5900),
(6,  6,  'kg CO₂/kWh',                   0.8700),
(7,  7,  'kg CO₂/kg LPG',                3.0850),
(8,  7,  'CO₂ (g/kg bahan bakar)',       3.0850),
(9,  8,  'CO2 g/km',                     0.0140),
(10, 8,  'kg CO/km',                     0.0400),
(11, 8,  'kg CO/km',                     0.0028),
(12, 9,  'kg CO/km',                     0.0045),
(13, 10, 'kg CO2e/kg of materials',      1.2610),
(14, 11, 'kg CO₂/liter bensin',          2.3900),
(15, 12, 'kg CO₂/liter bensin',          2.4100),
(16, 13, 'kg CO2e/kg of materials',      1.2610);

-- ============================================================================
-- 7. OPTIONS
-- ============================================================================
INSERT INTO options (option_id, activity_unit_id, emission_factor_id, description) VALUES
(1, 8,    9,    'Sepeda Motor'),
(2, 8,    10,   'Mobil-Bensin'),
(3, 8,    11,   'Mobil-Diesel'),
(4, NULL, NULL, 'Tosa'),
(5, 8,    12,   'Truk'),
(6, 10,   14,   'RON 92'),
(7, 10,   15,   'RON 98');

-- ============================================================================
-- 8. ACTIVITIES
-- ============================================================================
INSERT INTO activities (activity_id, subprocess_id, "order", activity_unit_id, emission_factor_id, description) VALUES
-- Process 1: Kain Gedog Tuban
(1,  1,  1, NULL, NULL, 'Cangkul'),
(2,  1,  2, 1,    1,    'Pupuk organic mandiri'),
(3,  2,  1, NULL, NULL, 'Pengairan tadah hujan/rain feed'),
(4,  2,  2, 2,    2,    'Pengairan artificial (pompa diesel)'),
(5,  2,  3, 3,    3,    'Pengairan artificial (pompa listrik)'),
(6,  2,  4, 1,    1,    'Pupuk organic mandiri'),
(7,  2,  5, 4,    4,    'Pestisida organic mandiri'),
(8,  3,  1, NULL, NULL, 'Handpicked'),
(9,  3,  2, NULL, NULL, 'Sun dried'),
(10, 3,  3, NULL, NULL, 'Stored naturally/no electric humidity control'),
(11, 4,  1, NULL, NULL, 'Hand-ginning'),
(12, 4,  2, NULL, NULL, 'Hand carding'),
(13, 5,  1, NULL, NULL, 'Hand spinning'),
(14, 5,  2, NULL, NULL, 'Hand-weaving'),

-- Process 2: Kain Batik Gedog Tuban
(15, 6,  1, 5,    5,    'Input malam (dibeli)'),
(16, 6,  2, 6,    6,    'Input Lampu Penerangan'),
(17, 6,  3, 7,    7,    'Input Gas u/ Kompor'),
(18, 7,  1, NULL, NULL, 'Transport ke Temanggung'),
(19, 7,  2, 7,    8,    'Gas untuk perebusan'),
(20, 7,  3, 7,    7,    'Pasta Strobilanthes cussia'),
(21, 7,  4, NULL, NULL, 'Jemur Matahari'),
(22, 8,  1, NULL, NULL, 'Limbah gergajian mahoni'),
(23, 8,  2, 9,    13,   'Kayu Bakar'),
(24, 8,  3, NULL, NULL, 'Jemur matahari'),
(25, 9,  1, NULL, NULL, 'Transport ke DIY'),
(26, 9,  2, NULL, NULL, 'Daun/kulit kayu/akar/bunga'),
(27, 9,  3, 9,    13,   'Kayu Bakar'),
(28, 9,  4, NULL, NULL, 'Jemur matahari'),

-- Process 3: Kain TENUN ATBM PISANO
(29, 10, 1, NULL, NULL, 'Cangkul'),
(30, 10, 2, 1,    1,    'Pupuk organic mandiri'),
(31, 11, 1, NULL, NULL, 'Pengairan tadah hujan/rain feed'),
(32, 11, 2, 2,    2,    'Pengairan artificial (pompa diesel)'),
(33, 11, 3, 3,    3,    'Pengairan artificial (pompa listrik)'),
(34, 11, 4, 1,    1,    'Pupuk organic mandiri'),
(35, 11, 5, 4,    4,    'Pestisida organic mandiri'),
(36, 12, 1, NULL, NULL, 'Handpicked'),
(37, 12, 2, NULL, NULL, 'Sun dried'),
(38, 12, 3, NULL, NULL, 'Stored naturally/no electric humidity control'),
(39, 13, 1, NULL, NULL, 'Transport ke Sekaralas/Ngawi'),
(40, 13, 2, NULL, NULL, 'Bensin untuk mesin ginning'),
(41, 13, 3, NULL, NULL, 'Transport ke Klaten'),
(42, 13, 4, 6,    6,    'Listrik untuk mesin carding'),
(43, 14, 1, NULL, NULL, 'Pengiriman sliver ke Transport ke pabrik di Pekalongan/Bandung/Solo'),
(44, 14, 2, NULL, NULL, 'Spinning dg mesin pabrik'),
(45, 14, 3, NULL, NULL, 'Transport ke Klaten'),
(46, 14, 4, NULL, NULL, 'Tenun Tangan ATBM di Desa Karangasem Klaten'),
(47, 14, 5, NULL, NULL, 'Transport kembali ke Yogya'),

-- Process 4: Kain BATIK TENUN ATBM PISANO
(48, 15, 1, NULL, NULL, 'Transport ke Tancep/DIY'),
(49, 15, 2, 5,    5,    'Input malam di beli'),
(50, 15, 3, 6,    3,    'Input Lampu Penerangan'),
(51, 15, 4, 7,    7,    'Input Gas u/ Kompor'),
(52, 16, 1, NULL, NULL, 'Transport ke Temanggung'),
(53, 16, 2, 7,    7,    'Gas untuk perebusan'),
(54, 16, 3, 7,    7,    'Pasta Strobilanthes cussia'),
(55, 16, 4, NULL, NULL, 'Jemur Matahari'),
(56, 17, 1, NULL, NULL, 'Transport ke DIY'),
(57, 17, 2, NULL, NULL, 'Daun/kulit kayu/akar/bunga'),
(58, 17, 3, 9,    16,   'Kayu Bakar'),
(59, 17, 4, NULL, NULL, 'Jemur matahari');

-- ============================================================================
-- 9. ACTIVITY_OPTION (Pivot)
-- ============================================================================
INSERT INTO activity_option (activity_id, option_id) VALUES
-- Transport activities -> Vehicles (1..5)
(18, 1), (18, 2), (18, 3), (18, 4), (18, 5),
(25, 1), (25, 2), (25, 3), (25, 4), (25, 5),
(39, 1), (39, 2), (39, 3), (39, 4), (39, 5),
(41, 1), (41, 2), (41, 3), (41, 4), (41, 5),
(43, 1), (43, 2), (43, 3), (43, 4), (43, 5),
(45, 1), (45, 2), (45, 3), (45, 4), (45, 5),
(47, 1), (47, 2), (47, 3), (47, 4), (47, 5),
(48, 1), (48, 2), (48, 3), (48, 4), (48, 5),
(52, 1), (52, 2), (52, 3), (52, 4), (52, 5),
(56, 1), (56, 2), (56, 3), (56, 4), (56, 5),

-- Fuel activities -> RON 92 & RON 98 (6..7)
(40, 6), (40, 7),
(44, 6), (44, 7);

COMMIT;


---
