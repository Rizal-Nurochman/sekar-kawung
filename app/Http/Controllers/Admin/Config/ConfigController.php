<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityUnit;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\Source;
use App\Models\Subprocess;
use Illuminate\View\View;

class ConfigController extends Controller
{
    public function index(): View
    {
        $structure = [
            ['name' => 'Skema', 'description' => 'Jalur produk yang dihitung, misalnya Gedog Tuban.', 'route' => 'admin.config.schemes.index', 'icon' => 'layers', 'count' => Scheme::query()->count(), 'unit' => 'skema'],
            ['name' => 'Proses', 'description' => 'Batas sistem di dalam skema, dari kain hingga kain batik.', 'route' => 'admin.config.processes.index', 'icon' => 'workflow', 'count' => Process::query()->count(), 'unit' => 'proses'],
            ['name' => 'Subproses', 'description' => 'Tahapan kerja di dalam satu batas sistem.', 'route' => 'admin.config.subprocesses.index', 'icon' => 'list-tree', 'count' => Subprocess::query()->count(), 'unit' => 'subproses'],
            ['name' => 'Aktivitas', 'description' => 'Baris inventaris yang diisi kuantitasnya di kalkulator.', 'route' => 'admin.config.activities.index', 'icon' => 'list-checks', 'count' => Activity::query()->count(), 'unit' => 'aktivitas'],
            ['name' => 'Opsi', 'description' => 'Pilihan jawaban untuk aktivitas, misalnya moda transportasi.', 'route' => 'admin.config.options.index', 'icon' => 'circle-dot', 'count' => Option::query()->count(), 'unit' => 'opsi'],
        ];

        $reference = [
            ['name' => 'Satuan aktivitas', 'description' => 'Satuan yang dipakai aktivitas dan opsi.', 'route' => 'admin.config.activity-units.index', 'icon' => 'ruler', 'count' => ActivityUnit::query()->count(), 'unit' => 'satuan'],
            ['name' => 'Faktor emisi', 'description' => 'Nilai emisi per satuan, dipakai untuk menghitung hasil.', 'route' => 'admin.config.emission-factors.index', 'icon' => 'flask-conical', 'count' => EmissionFactor::query()->count(), 'unit' => 'faktor'],
            ['name' => 'Sumber', 'description' => 'Referensi ilmiah di balik setiap faktor emisi.', 'route' => 'admin.config.sources.index', 'icon' => 'book-open', 'count' => Source::query()->count(), 'unit' => 'sumber'],
        ];

        return view('admin.config.index', [
            'structure' => $structure,
            'reference' => $reference,
            'missingFactors' => Activity::query()->whereNull('emission_factor_id')->whereDoesntHave('options')->count(),
        ]);
    }
}
