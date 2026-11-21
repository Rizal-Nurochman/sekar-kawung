<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['activity_unit_id', 'emission_factor_id', 'description'])]
class Option extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'activity_unit_id' => 'integer',
            'emission_factor_id' => 'integer',
        ];
    }

    public function activityUnit(): BelongsTo
    {
        return $this->belongsTo(ActivityUnit::class);
    }

    public function emissionFactor(): BelongsTo
    {
        return $this->belongsTo(EmissionFactor::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_option');
    }
}
