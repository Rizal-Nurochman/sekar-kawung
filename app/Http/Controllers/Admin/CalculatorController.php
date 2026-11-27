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
        $ordered = $wizard->orderedProducts();
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

        $currentProduct = $ordered->values()->get($current);

        if ($currentProduct === null) {
            $wizard->reset();

            return view('admin.calculator.edit', [
                'mode' => 'empty',
                'totalSteps' => 0,
            ]);
        }

        $product = $wizard->productWithInventory($currentProduct);
        $entries = $state['entries'] ?? [];
        $baseByScheme = collect($wizard->baseTotals($product, $entries))->keyBy('id');

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

        return view('admin.calculator.edit', [
            'mode' => 'step',
            'totalSteps' => $totalSteps,
            'currentIndex' => $current,
            'product' => $product,
            'entries' => $entries,
            'schemeTotals' => $schemeTotals,
            'currentSchemeId' => (int) $product->scheme_id,
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
        $ordered = $wizard->orderedProducts();
        $totalSteps = $ordered->count();

        $state = $wizard->state();

        if ($state === null || $totalSteps === 0) {
            return redirect()->route('admin.calculator.edit');
        }

        $current = (int) $state['current'];
        $currentProduct = $ordered->values()->get($current);

        if ($currentProduct === null) {
            $wizard->reset();

            return redirect()->route('admin.calculator.edit');
        }

        if ((int) $validated['product_id'] !== (int) $currentProduct->getKey()) {
            return redirect()->route('admin.calculator.edit')->withErrors([
                'general' => 'Langkah tidak sesuai urutan. Lanjutkan dari posisi tersimpan.',
            ]);
        }

        $fullProduct = $wizard->productWithInventory($currentProduct);
        $wizard->saveStep($fullProduct, $validated['items'] ?? []);

        $direction = $validated['direction'] === 'back' ? -1 : 1;
        $wizard->moveTo($current + $direction, $totalSteps);

        return redirect()->route('admin.calculator.edit');
    }

    public function backFromReview(CalculatorWizard $wizard): RedirectResponse
    {
        $ordered = $wizard->orderedProducts();
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
        $ordered = $wizard->orderedProducts();
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
