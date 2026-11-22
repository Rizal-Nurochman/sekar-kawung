<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CalculatorWizard;
use App\Actions\Admin\UpsertReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CalculatorStepRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function edit(CalculatorWizard $wizard): View
    {
        $ordered = $wizard->orderedProcesses();
        $totalSteps = $ordered->count();

        if ($totalSteps === 0) {
            return view('admin.calculator.edit', [
                'mode' => 'empty',
                'totalSteps' => 0,
            ]);
        }

        $state = $wizard->state();

        if ($state === null) {
            $schemes = $ordered->pluck('scheme')->filter()->unique('id')->values();

            return view('admin.calculator.edit', [
                'mode' => 'start',
                'totalSteps' => $totalSteps,
                'schemes' => $schemes,
                'ordered' => $ordered,
            ]);
        }

        $current = (int) $state['current'];

        if ($current >= $totalSteps) {
            $review = $wizard->reviewData();

            return view('admin.calculator.edit', [
                'mode' => 'review',
                'totalSteps' => $totalSteps,
                'review' => $review,
                'ordered' => $ordered,
            ]);
        }

        $currentProcess = $ordered->values()->get($current);

        if ($currentProcess === null) {
            $wizard->reset();

            return view('admin.calculator.edit', [
                'mode' => 'empty',
                'totalSteps' => 0,
            ]);
        }

        $process = $wizard->processWithInventory($currentProcess);
        $entries = $state['entries'] ?? [];
        $base = $wizard->baseTotals($process, $entries);

        $currentIds = $process->subprocesses
            ->flatMap(fn ($subprocess) => $subprocess->activities->pluck('id'))
            ->map(fn ($id): string => (string) (int) $id)
            ->all();
        $currentSet = array_flip($currentIds);

        $currentFilled = 0;
        foreach ($entries as $activityId => $entry) {
            if (isset($currentSet[(string) $activityId]) && (float) ($entry['quantity'] ?? 0) > 0) {
                $currentFilled++;
            }
        }

        return view('admin.calculator.edit', [
            'mode' => 'step',
            'totalSteps' => $totalSteps,
            'currentIndex' => $current,
            'process' => $process,
            'entries' => $entries,
            'baseTotal' => (float) $base['total'],
            'baseFilled' => (int) $base['filled'],
            'currentFilled' => $currentFilled,
            'totalActivities' => $wizard->totalActivityCount(),
            'ordered' => $ordered,
        ]);
    }

    public function start(CalculatorWizard $wizard): RedirectResponse
    {
        $wizard->start();

        return redirect()->route('admin.calculator.edit');
    }

    public function step(CalculatorStepRequest $request, CalculatorWizard $wizard): RedirectResponse
    {
        $validated = $request->validated();
        $ordered = $wizard->orderedProcesses();
        $totalSteps = $ordered->count();

        $state = $wizard->state();

        if ($state === null || $totalSteps === 0) {
            return redirect()->route('admin.calculator.edit');
        }

        $current = (int) $state['current'];
        $currentProcess = $ordered->values()->get($current);

        if ($currentProcess === null) {
            $wizard->reset();

            return redirect()->route('admin.calculator.edit');
        }

        if ((int) $validated['process_id'] !== (int) $currentProcess->getKey()) {
            return redirect()->route('admin.calculator.edit')->withErrors([
                'general' => 'Langkah tidak sesuai urutan. Lanjutkan dari posisi tersimpan.',
            ]);
        }

        $fullProcess = $wizard->processWithInventory($currentProcess);
        $wizard->saveStep($fullProcess, $validated['items'] ?? []);

        $direction = $validated['direction'] === 'back' ? -1 : 1;
        $wizard->moveTo($current + $direction, $totalSteps);

        return redirect()->route('admin.calculator.edit');
    }

    public function backFromReview(CalculatorWizard $wizard): RedirectResponse
    {
        $ordered = $wizard->orderedProcesses();
        $state = $wizard->state();

        if ($state === null || $ordered->isEmpty()) {
            return redirect()->route('admin.calculator.edit');
        }

        $wizard->moveTo($ordered->count() - 1, $ordered->count());

        return redirect()->route('admin.calculator.edit');
    }

    public function reset(CalculatorWizard $wizard): RedirectResponse
    {
        $wizard->reset();

        return redirect()->route('admin.calculator.edit');
    }

    public function store(CalculatorWizard $wizard, UpsertReport $upsert): RedirectResponse
    {
        $ordered = $wizard->orderedProcesses();
        $state = $wizard->state();

        if ($ordered->isEmpty() || $state === null || (int) $state['current'] < $ordered->count()) {
            return redirect()->route('admin.calculator.edit')->withErrors([
                'general' => 'Selesaikan seluruh proses sebelum menyimpan.',
            ]);
        }

        $payload = $wizard->buildStorePayload();
        $report = $upsert->handle($payload);
        $wizard->reset();

        return redirect()
            ->route('admin.reports.show', $report)
            ->with('status', 'Laporan keberlanjutan berhasil disimpan.');
    }
}
