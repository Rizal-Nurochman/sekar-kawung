<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['subprocess_id', 'activity_unit_id', 'emission_factor_id', 'sort_order', 'description'])]
class Activity extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'subprocess_id' => 'integer',
            'activity_unit_id' => 'integer',
            'emission_factor_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function subprocess(): BelongsTo
    {
        return $this->belongsTo(Subprocess::class);
    }

    public function activityUnit(): BelongsTo
    {
        return $this->belongsTo(ActivityUnit::class);
    }

    public function emissionFactor(): BelongsTo
    {
        return $this->belongsTo(EmissionFactor::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'activity_option')
            ->orderBy('options.id');
    }
}
