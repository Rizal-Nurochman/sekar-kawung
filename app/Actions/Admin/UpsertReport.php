<?php

namespace App\Actions\Admin;

use App\Models\SustainabilityReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpsertReport
{
    public function __construct(protected CalculateEmissions $calculator) {}

    public function handle(array $validated, ?SustainabilityReport $report = null): SustainabilityReport
    {
        return DB::transaction(function () use ($validated, $report): SustainabilityReport {
            $calculation = $this->calculator->handle($validated);

            $report ??= new SustainabilityReport;

            $slug = $validated['slug'] ?? null;

            if (! is_string($slug) || trim($slug) === '') {
                $slug = $report->exists && is_string($report->slug) && $report->slug !== ''
                    ? $report->slug
                    : (string) ($validated['title'] ?? 'laporan');
            }

            $slug = $this->uniqueSlug($slug, $report->getKey());

            $report->fill([
                'scheme_id' => $validated['scheme_id'],
                'product_id' => $validated['product_id'],
                'title' => $validated['title'],
                'slug' => $slug,
                'summary' => $validated['summary'] ?? null,
                'total_emission' => $calculation['total'],
                'is_published' => (bool) ($validated['is_published'] ?? false),
            ]);
            $report->save();

            $report->items()->delete();

            if ($calculation['items'] !== []) {
                $report->items()->createMany($calculation['items']);
            }

            return $report->refresh();
        });
    }

    private function uniqueSlug(string $slug, mixed $ignoreId = null): string
    {
        $base = Str::slug($slug) !== '' ? Str::slug($slug) : 'laporan';
        $candidate = $base;
        $suffix = 2;

        while (
            SustainabilityReport::query()
                ->where('slug', $candidate)
                ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
