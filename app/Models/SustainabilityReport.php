<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['scheme_id', 'boundary_id', 'title', 'slug', 'summary', 'total_emission', 'is_active'])]
class SustainabilityReport extends Model
{
    protected function casts(): array
    {
        return [
            'scheme_id' => 'integer',
            'boundary_id' => 'integer',
            'total_emission' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function boundary(): BelongsTo
    {
        return $this->belongsTo(Boundary::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SustainabilityReportItem::class);
    }
}
