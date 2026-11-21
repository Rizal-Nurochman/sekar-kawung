<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['source_id', 'unit_name', 'ef_value'])]
class EmissionFactor extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'ef_value' => 'decimal:10',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
