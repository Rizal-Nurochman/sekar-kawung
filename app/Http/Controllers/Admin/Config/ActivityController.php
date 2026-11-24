<?php

namespace App\Http\Controllers\Admin\Config;

use App\Actions\Admin\DeleteMasterRecord;
use App\Actions\Admin\UpsertMasterRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityRequest;
use App\Models\Activity;
use App\Models\ActivityUnit;
use App\Models\EmissionFactor;
use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class ActivityController extends Controller
{
    public function index(): View
    {
        $activities = Activity::query()
            ->with(['process', 'activityUnit', 'emissionFactor'])
            ->withCount('options')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('admin.config.activities.index', compact('activities'));
    }

    public function create(): View
    {
        $processes = Process::query()->orderBy('sort_order')->orderBy('id')->get();
        $units = ActivityUnit::query()->orderBy('id')->get();
        $factors = EmissionFactor::query()->orderBy('id')->get();

        return view('admin.config.activities.create', compact('processes', 'units', 'factors'));
    }

    public function store(ActivityRequest $request, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['process_id', 'activity_unit_id', 'emission_factor_id', 'description', 'sort_order']);

        $upsert->handle(Activity::class, $data);

        return redirect()->route('admin.config.activities.index')->with('status', 'Aktivitas berhasil ditambahkan.');
    }

    public function edit(Activity $activity): View
    {
        $processes = Process::query()->orderBy('sort_order')->orderBy('id')->get();
        $units = ActivityUnit::query()->orderBy('id')->get();
        $factors = EmissionFactor::query()->orderBy('id')->get();

        return view('admin.config.activities.edit', compact('activity', 'processes', 'units', 'factors'));
    }

    public function update(ActivityRequest $request, Activity $activity, UpsertMasterRecord $upsert): RedirectResponse
    {
        $data = $request->safe()->only(['process_id', 'activity_unit_id', 'emission_factor_id', 'description', 'sort_order']);

        $upsert->handle(Activity::class, $data, $activity);

        return redirect()->route('admin.config.activities.index')->with('status', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy(Activity $activity, DeleteMasterRecord $delete): RedirectResponse
    {
        try {
            $delete->handle($activity);
        } catch (RuntimeException $e) {
            return back()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()->route('admin.config.activities.index')->with('status', 'Aktivitas berhasil dihapus.');
    }
}
