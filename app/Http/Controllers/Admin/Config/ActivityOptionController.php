<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Option;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityOptionController extends Controller
{
    public function edit(Activity $activity): View
    {
        $activity->load('options');
        $options = Option::query()->orderBy('id')->get();
        $selected = $activity->options->pluck('id')->all();

        return view('admin.config.activity-options.edit', compact('activity', 'options', 'selected'));
    }

    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'option_ids.*' => ['integer', 'exists:options,id'],
        ]);

        $activity->options()->sync($validated['option_ids'] ?? []);

        return redirect()->route('admin.config.activities.index')->with('status', 'Relasi opsi aktivitas berhasil diperbarui.');
    }
}
