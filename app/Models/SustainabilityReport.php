<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['scheme_id', 'product_id', 'title', 'slug', 'summary', 'total_emission', 'is_published'])]
class SustainabilityReport extends Model
{
    protected function casts(): array
    {
        return [
            'scheme_id' => 'integer',
            'product_id' => 'integer',
            'total_emission' => 'decimal:4',
            'is_published' => 'boolean',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SustainabilityReportItem::class);
    }
}
