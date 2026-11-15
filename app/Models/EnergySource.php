<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'slug', 'emission_multiplier', 'sort_order', 'is_active'])]
class EnergySource extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'emission_multiplier' => 'decimal:3',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf('%s (Faktor %sx)', $this->name, $this->formattedMultiplier),
        );
    }

    protected function formattedMultiplier(): Attribute
    {
        return Attribute::make(
            get: function () {
                $multiplier = rtrim(rtrim(number_format((float) $this->emission_multiplier, 2, '.', ''), '0'), '.');

                return str_contains($multiplier, '.') ? $multiplier : $multiplier.'.0';
            },
        );
    }
}
