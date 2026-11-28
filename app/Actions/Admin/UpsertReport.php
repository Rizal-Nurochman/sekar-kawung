<?php

namespace App\Actions\Admin;

use App\Models\SustainabilityReport;
use Illuminate\Support\Facades\DB;

class UpsertReport
{
    public function __construct(protected CalculateEmissions $calculator) {}

    public function handle(array $validated, ?SustainabilityReport $report = null): SustainabilityReport
    {
        return DB::transaction(function () use ($validated, $report): SustainabilityReport {
            $calculation = $this->calculator->handle($validated);

            $report ??= new SustainabilityReport;

            $report->fill([
                'scheme_id' => $validated['scheme_id'],
                'boundary_id' => $validated['boundary_id'],
                'title' => 'Laporan',
                'slug' => 'laporan',
                'summary' => $validated['summary'] ?? null,
                'total_emission' => $calculation['total'],
                'is_active' => (bool) ($validated['is_active'] ?? false),
            ]);
            $report->save();

            $report->update([
                'title' => 'Laporan '.$report->getKey(),
                'slug' => 'laporan-'.$report->getKey(),
            ]);

            if ($report->is_active) {
                SustainabilityReport::query()
                    ->where('id', '!=', $report->getKey())
                    ->update(['is_active' => false]);
            }

            $report->items()->delete();

            if ($calculation['items'] !== []) {
                $report->items()->createMany($calculation['items']);
            }

            return $report->refresh();
        });
    }
}
