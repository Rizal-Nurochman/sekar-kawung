<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmissionFactorRequest;
use App\Models\EmissionFactor;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class EmissionFactorController extends Controller
{
    public function index(): View
    {
        $factors = EmissionFactor::query()->with('source')->orderBy('id')->paginate(20);

        return view('admin.config.emission-factors.index', compact('factors'));
    }

    public function create(): View
    {
        $sources = Source::query()->orderBy('id')->get();

        return view('admin.config.emission-factors.create', compact('sources'));
    }

    public function store(EmissionFactorRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['source_id', 'unit_name', 'ef_value']);

        $upsert->handle(EmissionFactor::class, $data);

        return redirect()->route('admin.config.emission-factors.index')->with('status', 'Faktor emisi berhasil ditambahkan.');
    }

    public function edit(EmissionFactor $emissionFactor): View
    {
        $sources = Source::query()->orderBy('id')->get();

        return view('admin.config.emission-factors.edit', ['factor' => $emissionFactor, 'sources' => $sources]);
    }

    public function update(EmissionFactorRequest $request, EmissionFactor $emissionFactor, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['source_id', 'unit_name', 'ef_value']);

        $upsert->handle(EmissionFactor::class, $data, $emissionFactor);

        return redirect()->route('admin.config.emission-factors.index')->with('status', 'Faktor emisi berhasil diperbarui.');
    }

    public function destroy(EmissionFactor $emissionFactor, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($emissionFactor);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.emission-factors.index')->with('status', 'Faktor emisi berhasil dihapus.');
    }
}
