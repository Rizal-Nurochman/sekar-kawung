<?php

namespace App\Models;

use App\Enums\DataStatus;
use App\Enums\Recyclability;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['textile_id', 'kg_co2e_per_kg', 'system_boundary', 'data_status', 'source_citation', 'water_l_per_kg', 'energy_note', 'sustainability_score', 'recyclability', 'verified_by', 'verified_at'])]
class CarbonFootprint extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'textile_id' => 'integer',
            'kg_co2e_per_kg' => 'decimal:4',
            'data_status' => DataStatus::class,
            'water_l_per_kg' => 'decimal:2',
            'sustainability_score' => 'decimal:1',
            'recyclability' => Recyclability::class,
            'verified_at' => 'datetime',
        ];
    }

    public function textile(): BelongsTo
    {
        return $this->belongsTo(Textile::class);
    }

    public function isVerified(): bool
    {
        return $this->data_status === DataStatus::Verified;
    }

    protected function savingVsBaselinePct(): Attribute
    {
        return Attribute::make(
            get: fn () => once(fn () => $this->resolveSavingVsBaselinePct()),
        );
    }

    private function resolveSavingVsBaselinePct(): ?int
    {
        $baseline = Textile::query()
            ->where('slug', (string) config('carbon.baseline_slug'))
            ->with('carbonFootprint')
            ->first()?->carbonFootprint;

        if ($baseline === null || (float) $baseline->kg_co2e_per_kg <= 0) {
            return null;
        }

        return (int) round((1 - ((float) $this->kg_co2e_per_kg / (float) $baseline->kg_co2e_per_kg)) * 100);
    }
}
