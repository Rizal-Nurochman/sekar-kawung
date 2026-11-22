# Analisis proyek Sekar Kawung

Tanggal pemeriksaan: 22 November 2026. Basis kode: HEAD `fed67ba`. Situs yang diperiksa: https://sekarkawung-green.vercel.app/.

## Cakupan dan metode

Saya membaca PRD, route, controller, action, model, migrasi, seeder, request validation, view utama, JavaScript, konfigurasi deploy, dan suite pengujian. Saya membandingkan route publik dengan situs production melalui browser tanpa login. Saya memeriksa standar ISO 14040/14044/14067, GHG Protocol Product Standard, dokumen IPCC 2019, publikasi metodologi WRI Indonesia, dan metadata literatur batik/tekstil. Saya tidak mengakses database production atau halaman admin setelah login. Karena itu, temuan tentang data production merupakan inferensi dari kode dan seed, bukan audit atas nilai yang tersimpan saat ini.

## Tujuan produk dan status

PRD menetapkan dua alur: (1) staf membuat laporan jejak karbon produk berdasarkan LCI yang diverifikasi lalu menerbitkannya; (2) pengunjung menghitung skenario tanpa menyimpan input. Ada dua skema produk (Gedog Tuban dan ATBM Pisano), masing-masing dengan batas kain dan kain batik yang seharusnya kumulatif.

| Bagian | Implementasi repository | Bukti situs production |
| --- | --- | --- |
| Landing | Kartu proses diambil dari `schemes/processes`, fallback statis | Aktif pada `/` |
| Admin | Login, dashboard, wizard, laporan, konfigurasi master | Halaman `/admin/login` aktif; fitur setelah login tidak diperiksa |
| Kalkulator publik | Route placeholder | `/calculator` menampilkan “Segera Hadir” |
| Metodologi dan About | Route placeholder | `/methodology` menampilkan “Segera Hadir” |
| Laporan publik | Route/controller/view belum ada | `/reports` mengembalikan 404 |
| Katalog lama | `/old` dan data statis `app/Support` | Tidak diperiksa |

Status production berarti situs sudah terpasang dan dapat diakses. Itu belum berarti seluruh cakupan PRD tersedia.

## Peta arsitektur

```text
Browser publik -> routes/web.php -> HomeController -> Scheme/Process -> landing.blade.php
Browser admin  -> auth session -> CalculatorController -> CalculatorWizard (session)
                                           -> CalculateEmissions -> UpsertReport
                                           -> SustainabilityReport + Items (DB)
Admin konfigurasi -> 8 CRUD controller -> UpsertMasterRecord/DeleteMasterRecord
Master DB: Scheme -> Process -> Subprocess -> Activity -> EmissionFactor -> Source
                                                   \-> ActivityOption -> Option -> Factor/Unit
```

Stack: PHP 8.4 / Laravel 13, Blade, Alpine.js, Tailwind 4, Vite 8, Pest 5. Deployment memakai image FrankenPHP dan Vercel Container pada region Singapura. Koneksi MySQL/TiDB memakai TLS. Dockerfile sekarang menetapkan cache dan sesi berbasis database; keadaan runtime production tetap perlu diperiksa lewat konfigurasi Vercel bila diperlukan.

Skema baru berisi 11 tabel domain: 9 master LCI serta `sustainability_reports` dan `sustainability_report_items`. Seed saat ini berisi 2 skema, 4 proses, 17 subproses, 59 aktivitas, 10 satuan, 13 sumber, 16 faktor emisi, 7 opsi, dan 54 relasi aktivitas-opsi. Dari 59 aktivitas, 24 punya faktor langsung, 12 memakai opsi, dan 23 tidak punya faktor maupun opsi. Sebagian dari 23 itu langkah manual, tetapi sebagian perlu klasifikasi eksplisit sebelum dianggap nol.

## Alur data yang benar-benar terjadi

1. Wizard admin mengurutkan **semua** proses dari semua skema dan menyimpan kuantitas/opsi per aktivitas dalam session.
2. UI Alpine memberi pratinjau memakai `quantity × emission_factor`; server menghitung ulang dengan rumus yang sama.
3. Saat langkah terakhir selesai, wizard mengirim **semua aktivitas dari semua proses** ke `UpsertReport`. Laporan diberi `scheme_id` dan `process_id` dari proses terakhir, lalu langsung `is_published=true`.
4. `UpsertReport` menyimpan total dan item, sedangkan halaman detail admin menyusun tahap/hotspot/sumber dari item.
5. Penyuntingan metadata laporan memanggil `UpsertReport` lagi tanpa `items`.

## Temuan prioritas

### P0 — Penyuntingan laporan menghapus hasil perhitungan

`ReportController::update()` hanya mengirim judul, slug, ringkasan, status, dan ID batas. `UpsertReport::handle()` selalu menghitung dari payload, lalu selalu menghapus item lama. Payload tersebut tidak berisi `items`, sehingga kalkulator mengembalikan total 0 dan daftar kosong. Setiap edit metadata dapat menghapus inventaris yang disimpan. Bukti: `app/Http/Controllers/Admin/ReportController.php:73-80`, `app/Actions/Admin/UpsertReport.php:15-45`, `app/Actions/Admin/CalculateEmissions.php:20-23`. Test yang ada memeriksa judul/slug, tetapi tidak memeriksa item dan total setelah edit.

### P0 — Satu laporan mencampur dua skema dan batas proses

`CalculatorWizard::orderedProcesses()` mengambil seluruh proses; `buildStorePayload()` mengambil seluruh aktivitas tetapi memakai skema/proses terakhir sebagai identitas laporan. Dengan seed bawaan, laporan berlabel Pisano batik dapat memuat Gedog Tuban serta Pisano kain/batik. Ini tidak cocok dengan satu produk dan satu batas yang dinyatakan PRD. Bukti: `app/Actions/Admin/CalculatorWizard.php:29-41,377-422`. Bahkan jika memilih satu skema, proses kain dan kain batik tersimpan sebagai dua daftar aktivitas yang terpisah, sehingga perlu definisi eksplisit apakah batas kedua mewarisi seluruh tahap batas pertama.

### P0 — Beberapa faktor tidak dapat langsung disebut kg CO₂e

Mesin menghitung angka tanpa pemeriksaan dimensi. Contoh seed:

- EF 1 = 0,010 `kg N₂O-N / kg N` menurut IPCC, tetapi aktivitas memakai `kg pupuk agregat`. Konversi kandungan N, N₂O-N ke N₂O (44/28), dan GWP ke CO₂e belum dilakukan. Angka 0,010 langsung ditampilkan sebagai kg CO₂e.
- EF 9 berlabel `CO2 g/km`, tetapi nilai 0,014 dikalikan km dan ditampilkan sebagai kg CO₂e tanpa konversi gram ke kilogram atau klarifikasi maksud nilai.
- EF 10–12 berlabel `kg CO/km` (karbon monoksida), sementara hasil dilabeli CO₂e. WRI membedakan penghitungan GRK CO₂ berbasis bahan bakar dari polutan udara berbasis jarak. Angka ini perlu dilacak ke tabel asli sebelum dipakai.
- EF 8 berlabel `CO₂ (g/kg bahan bakar)` pada aktivitas LPG, tetapi hasil ditampilkan kg; EF 13/16 berlabel `kg CO2e/kg of materials` dipakai untuk kayu bakar dan perlu verifikasi kesesuaian prosesnya.

Bukti lokal: `database/seeders/LcaEmissionFactorSeeder.php:14-29`, `database/seeders/LcaActivitySeeder.php`, `app/Actions/Admin/CalculateEmissions.php:58-75`. Temuan ini berarti total numerik saat ini belum layak disebut jejak karbon produk yang terverifikasi.

### P1 — “Tidak ada faktor” dan “emisi nol” digabung

`CalculateEmissions` mengubah faktor yang tidak tersedia menjadi 0. Opsi Tosa memang tidak memiliki faktor. Aktivitas tanpa faktor juga mencakup transport, bahan pewarna, mesin, dan pekerjaan tangan. Karena itu 0 dapat berarti langkah tanpa emisi langsung yang memang dibenarkan, data belum tersedia, atau faktor tidak dipilih. Ketiganya perlu status yang berbeda. Bukti: `app/Actions/Admin/CalculateEmissions.php:69-73`, `database/seeders/LcaOptionSeeder.php:15`, `app/Http/Controllers/Admin/Config/ConfigController.php:34`.

### P1 — Publikasi otomatis mendahului verifikasi

Wizard membentuk `is_published=true` dan langsung menyimpan saat review selesai. Tidak ada data batch, periode, unit fungsional, reviewer, atau persetujuan yang mengikat hasil dengan klaim “verified”. `TogglePublish` hanya mengubah boolean. Bukti: `app/Actions/Admin/CalculatorWizard.php:414-421`, `app/Actions/Admin/TogglePublish.php`, migrasi laporan. Untuk produk akhir, GHG Protocol mensyaratkan unit analisis, batas, periode, dan kualitas data dinyatakan.

### P1 — Jejak audit laporan belum beku

Item menyimpan aktivitas, opsi, kuantitas, dan hasil, tetapi tidak menyalin nilai faktor, satuan, sumber, versi metode, atau asumsi. Halaman laporan membaca relasi master yang bisa diedit setelah penerbitan. Total lama tetap, tetapi faktor/sumber yang ditampilkan kemudian bisa berbeda dari faktor yang menghasilkan total itu. Sumber di `ReportController::show()` mendahulukan faktor aktivitas daripada faktor opsi, sedangkan kalkulator mendahulukan opsi. Bukti: migrasi item, `app/Http/Controllers/Admin/ReportController.php:90-110`.

### P1 — Ringkasan dashboard dapat salah tafsir

`totalEmission` menjumlahkan semua laporan terbit. Laporan bisa memiliki produk, batas, periode, dan batch berbeda sehingga jumlah itu bukan jejak karbon satu produk. Query hotspot juga menjumlahkan item **semua** laporan tanpa filter publikasi. Bukti: `app/Http/Controllers/Admin/DashboardController.php:20-38`.

### P2 — Penyajian dan pengujian

Halaman admin laporan memakai `number_format` default Inggris, sedangkan wizard review memakai format Indonesia. Kartu landing memakai judul yang dipetakan berdasarkan ID 1–4; perubahan ID atau urutan mengubah/menghilangkan label yang dimaksud. README masih README Laravel bawaan. Kode lama `/old` tetap ada, tetapi tidak menjadi sumber kalkulator baru. Local `vendor/` tidak memuat dua paket ikon yang ada di `composer.lock`, sehingga 17 dari 71 Feature/Unit test gagal saat Blade memuat `lucide-*` atau `fas-*`. Setelah cache konfigurasi dibersihkan dan tes dijalankan dengan SQLite in-memory di luar sandbox, 54 test lulus dan 17 gagal karena paket lokal tidak terpasang. Itu keterbatasan instalasi lokal; situs production menampilkan ikon/halaman publik.

## Kesesuaian dengan standar dan literatur

- ISO 14040/14044 mencakup tujuan dan ruang lingkup, inventaris, penilaian dampak, interpretasi, pelaporan, dan tinjauan kritis. Aplikasi saat ini kuat pada input inventaris dan penjumlahan faktor, tetapi belum menyimpan banyak elemen ruang lingkup dan kualitas data.
- ISO 14067:2018 mengatur kuantifikasi dan pelaporan carbon footprint produk (termasuk yang parsial) dalam kerangka LCA. Label hasil perlu dibatasi sebagai estimasi sementara sampai faktor, satuan, batas, dan data primer diverifikasi.
- GHG Protocol Product Standard menekankan unit analisis/reference flow, batas proses, periode inventaris, kualitas data, dan pembenaran pengecualian. Ini relevan langsung untuk desain model laporan dan metodologi publik.
- IPCC 2019 memberi EF₁ tanah 0,010 dalam unit kg N₂O–N per kg N; itu bukan faktor kg CO₂e per kg pupuk.
- WRI Indonesia memisahkan GRK dan polutan transportasi; tabel dengan CO perlu diperlakukan sebagai polutan, bukan CO₂e.
- Metadata akademik menunjukkan studi batik LCA di Surakarta (Yoshanti & Dowaki, 2016, DOI 10.1007/978-981-10-0471-1_68) dan studi LCA rantai tekstil 2024 (DOI 10.1007/s11367-024-02346-2). Saya memverifikasi identitas publikasinya; isi penuh studi Surakarta tidak tersedia lewat pencarian yang dipakai, sehingga saya tidak menarik angka empiris darinya.

Sumber utama:

- ISO 14040: https://www.iso.org/standard/37456.html
- ISO 14044: https://www.iso.org/standard/38498.html
- ISO 14067: https://www.iso.org/standard/71206.html
- GHG Protocol Product Standard: https://ghgprotocol.org/sites/default/files/standards/Product-Life-Cycle-Accounting-Reporting-Standard_041613.pdf
- IPCC 2019 Volume 4 Bab 11: https://www.ipcc-nggip.iges.or.jp/public/2019rf/pdf/4_Volume4/19R_V4_Ch11_Soils_N2O_CO2.pdf
- WRI Indonesia transport: https://wri-indonesia.org/en/initiatives/emission-reduction-and-sequestration-initiativeinisiatif-pengurangan-dan-penyerapan

## Urutan tindak lanjut yang disarankan

1. Lindungi data laporan: pisahkan edit metadata dari kalkulasi ulang, lalu tambah regresi test yang memastikan item/total tidak berubah.
2. Tetapkan identitas perhitungan: satu skema, satu batas, unit fungsional, batch/masa produksi, dan aturan kumulatif batas kedua. Ubah wizard dan model sesuai keputusan domain itu.
3. Audit setiap faktor seed dengan penanggung jawab LCA: jenis gas, unit input/output, konversi, GWP, cakupan geografis/waktu, serta dokumen sumber sampai tingkat tabel/halaman. Tandai data tidak tersedia secara eksplisit.
4. Tambah status draf, peninjauan dan snapshot metodologi/faktor pada laporan sebelum publikasi. Sajikan sumber yang benar-benar dipakai.
5. Bangun kalkulator publik stateless dan halaman laporan publik sesuai PRD, lalu metodologi dan penjelasan keterbatasan.
6. Perbarui README, dependency lokal, test unit dimensi/perhitungan, test batas skema, dan test edit laporan. Setelah itu verifikasi di staging sebelum mengubah production.

## Batas kesimpulan

Audit ini tidak membuktikan kondisi isi database production, kredensial, konfigurasi Vercel yang menimpa Dockerfile, atau perilaku admin setelah login. Semua temuan produksi yang dinyatakan langsung dibatasi pada route publik yang diperiksa pada tanggal di atas. Riset akademik bersifat terarah untuk memeriksa metodologi dan faktor, bukan systematic review.
