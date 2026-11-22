<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityUnitRequest;
use App\Models\ActivityUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ActivityUnitController extends Controller
{
    public function index(): View
    {
        $units = ActivityUnit::query()->orderBy('id')->paginate(20);

        return view('admin.config.activity-units.index', compact('units'));
    }

    public function create(): View
    {
        return view('admin.config.activity-units.create');
    }

    public function store(ActivityUnitRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['unit_name']);

        $upsert->handle(ActivityUnit::class, $data);

        return redirect()->route('admin.config.activity-units.index')->with('status', 'Satuan aktivitas berhasil ditambahkan.');
    }

    public function edit(ActivityUnit $activityUnit): View
    {
        return view('admin.config.activity-units.edit', ['unit' => $activityUnit]);
    }

    public function update(ActivityUnitRequest $request, ActivityUnit $activityUnit, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['unit_name']);

        $upsert->handle(ActivityUnit::class, $data, $activityUnit);

        return redirect()->route('admin.config.activity-units.index')->with('status', 'Satuan aktivitas berhasil diperbarui.');
    }

    public function destroy(ActivityUnit $activityUnit, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($activityUnit);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.activity-units.index')->with('status', 'Satuan aktivitas berhasil dihapus.');
    }
}
