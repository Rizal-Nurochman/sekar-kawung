<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OptionRequest;
use App\Models\ActivityUnit;
use App\Models\EmissionFactor;
use App\Models\Option;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class OptionController extends Controller
{
    public function index(): View
    {
        $options = Option::query()->with(['activityUnit', 'emissionFactor'])->orderBy('id')->paginate(20);

        return view('admin.config.options.index', compact('options'));
    }

    public function create(): View
    {
        $units = ActivityUnit::query()->orderBy('id')->get();
        $factors = EmissionFactor::query()->orderBy('id')->get();

        return view('admin.config.options.create', compact('units', 'factors'));
    }

    public function store(OptionRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['activity_unit_id', 'emission_factor_id', 'description']);

        $upsert->handle(Option::class, $data);

        return redirect()->route('admin.config.options.index')->with('status', 'Opsi berhasil ditambahkan.');
    }

    public function edit(Option $option): View
    {
        $units = ActivityUnit::query()->orderBy('id')->get();
        $factors = EmissionFactor::query()->orderBy('id')->get();

        return view('admin.config.options.edit', compact('option', 'units', 'factors'));
    }

    public function update(OptionRequest $request, Option $option, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['activity_unit_id', 'emission_factor_id', 'description']);

        $upsert->handle(Option::class, $data, $option);

        return redirect()->route('admin.config.options.index')->with('status', 'Opsi berhasil diperbarui.');
    }

    public function destroy(Option $option, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($option);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.options.index')->with('status', 'Opsi berhasil dihapus.');
    }
}
