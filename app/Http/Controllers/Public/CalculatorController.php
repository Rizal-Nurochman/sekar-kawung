<?php

namespace App\Http\Controllers\Public;

use App\Actions\Public\CalculatorWizard;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CalculatorStepRequest;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalculatorController extends Controller
{
    public function edit(CalculatorWizard $wizard): View
    {
        $ordered = $wizard->orderedBoundaries();
        $totalSteps = $ordered->count();
        $state = $wizard->state();
        $selectedSchemeId = $state['scheme_id'] ?? null;
        $schemes = Scheme::query()->orderBy('sort_order')->orderBy('id')->get();

        if ($totalSteps === 0) {
            return view('landing.calculator.edit', [
                'mode' => 'empty',
                'totalSteps' => 0,
                'schemes' => $schemes,
                'selectedSchemeId' => $selectedSchemeId,
            ]);
        }

        if ($state === null) {
            return view('landing.calculator.edit', [
                'mode' => 'start',
                'totalSteps' => $totalSteps,
                'schemes' => $schemes,
                'selectedSchemeId' => $selectedSchemeId,
                'ordered' => $ordered,
            ]);
        }

        $current = (int) $state['current'];

        if ($current >= $totalSteps) {
            $review = $wizard->reviewData();

            return view('landing.calculator.edit', [
                'mode' => 'review',
                'totalSteps' => $totalSteps,
                'review' => $review,
                'schemes' => $schemes,
                'selectedSchemeId' => $selectedSchemeId,
                'ordered' => $ordered,
            ]);
        }

        $currentBoundary = $ordered->values()->get($current);

        if ($currentBoundary === null) {
            $wizard->reset();

            return view('landing.calculator.edit', [
                'mode' => 'empty',
                'totalSteps' => 0,
            ]);
        }

        $boundary = $wizard->boundaryWithInventory($currentBoundary);
        $entries = $state['entries'] ?? [];
        $baseByScheme = collect($wizard->baseTotals($boundary, $entries))->keyBy('id');

        $schemeTotals = $ordered->pluck('scheme')
            ->filter()
            ->unique('id')
            ->map(function ($scheme) use ($baseByScheme): array {
                $base = $baseByScheme->get($scheme->getKey());

                return [
                    'id' => (int) $scheme->getKey(),
                    'name' => (string) $scheme->description,
                    'total' => (float) ($base['total'] ?? 0),
                ];
            })
            ->values();

        return view('landing.calculator.edit', [
            'mode' => 'step',
            'totalSteps' => $totalSteps,
            'currentIndex' => $current,
            'boundary' => $boundary,
            'entries' => $entries,
            'schemeTotals' => $schemeTotals,
            'currentSchemeId' => (int) $boundary->scheme_id,
            'schemes' => $schemes,
            'selectedSchemeId' => $selectedSchemeId,
            'ordered' => $ordered,
        ]);
    }

    public function start(Request $request, CalculatorWizard $wizard): RedirectResponse
    {
        $validated = $request->validate([
            'scheme_id' => ['nullable', 'integer', 'exists:schemes,id'],
        ]);

        $schemeId = isset($validated['scheme_id']) ? (int) $validated['scheme_id'] : null;

        $wizard->start($schemeId);

        return redirect()->route('calculator');
    }

    public function step(CalculatorStepRequest $request, CalculatorWizard $wizard): RedirectResponse
    {
        $validated = $request->validated();
        $ordered = $wizard->orderedBoundaries();
        $totalSteps = $ordered->count();

        $state = $wizard->state();

        if ($state === null || $totalSteps === 0) {
            return redirect()->route('calculator');
        }

        $current = (int) $state['current'];
        $currentBoundary = $ordered->values()->get($current);

        if ($currentBoundary === null) {
            $wizard->reset();

            return redirect()->route('calculator');
        }

        if ((int) $validated['boundary_id'] !== (int) $currentBoundary->getKey()) {
            return redirect()->route('calculator')->withErrors([
                'general' => 'Langkah tidak sesuai urutan. Lanjutkan dari posisi tersimpan.',
            ]);
        }

        $fullBoundary = $wizard->boundaryWithInventory($currentBoundary);
        $wizard->saveStep($fullBoundary, $validated['items'] ?? []);

        $direction = $validated['direction'] === 'back' ? -1 : 1;
        $wizard->moveTo($current + $direction, $totalSteps);

        return redirect()->route('calculator');
    }

    public function backFromReview(CalculatorWizard $wizard): RedirectResponse
    {
        $ordered = $wizard->orderedBoundaries();
        $state = $wizard->state();

        if ($state === null || $ordered->isEmpty()) {
            return redirect()->route('calculator');
        }

        $wizard->moveTo($ordered->count() - 1, $ordered->count());

        return redirect()->route('calculator');
    }

    public function reset(CalculatorWizard $wizard): RedirectResponse
    {
        $wizard->reset();

        return redirect()->route('calculator');
    }
}
