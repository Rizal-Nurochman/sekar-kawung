# Sekar Kawung

Platform Life Cycle Assessment (LCA) dan jejak karbon untuk tenun Gedog Tuban serta Tenun ATBM Pisano. Aplikasi ini menggabungkan kalkulator publik tanpa penyimpanan data dengan CMS admin untuk menyusun dan menerbitkan laporan keberlanjutan produk.

## Teknologi

- PHP 8.3+ dan Laravel 13
- Blade, Alpine.js, Tailwind CSS 4, dan Vite 8
- Pest 5 untuk pengujian fitur, unit, dan browser
- MySQL/TiDB (TLS) untuk data master dan laporan

## Fitur

- Landing page berisi penjelasan emisi karbon, boundary produksi, dan metodologi.
- Kalkulator publik stateless: pengunjung mengisi aktivitas tahap demi tahap lalu melihat total emisi tanpa data disimpan.
- CMS admin: login, dasbor, kalkulator, laporan, dan konfigurasi data master LCI.
- Laporan keberlanjutan dengan status aktif/nonaktif, rincian per tahap, hotspot emisi, dan sitasi sumber.
- Data master: skema, boundary, proses, aktivitas, satuan, opsi, faktor emisi, dan sumber.

## Instalasi

```bash
composer setup
npm run dev
php artisan serve
```

Salin `.env.example` menjadi `.env` bila belum ada, lalu sesuaikan kredensial database dan `APP_KEY`.

## Perintah Penting

```bash
composer test          # jalankan seluruh test Pest
composer test:browser  # test browser Playwright
npm run build          # build aset produksi
```

## Struktur Singkat

```text
app/Actions            logika kalkulator, penyimpanan laporan, dan CRUD master
app/Http/Controllers   controller publik dan admin
app/Models             model domain LCI dan laporan
resources/views        tampilan landing, kalkulator, dan admin
resources/js           modul Alpine.js
database/seeders       data master LCI awal
tests                  test fitur, unit, dan browser
```

## Lisensi

MIT.
