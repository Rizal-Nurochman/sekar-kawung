<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'slug', 'is_calculable', 'sort_order'])]
class Textile extends Model
{
    use HasFactory;

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withPivot('share_percent')
            ->withTimestamps();
    }

    public function carbonFootprint(): HasOne
    {
        return $this->hasOne(CarbonFootprint::class);
    }

    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    public function scopeCalculable(Builder $query): Builder
    {
        return $query->where('is_calculable', true)->orderBy('sort_order');
    }

    protected function spec(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->materials
                ->sortBy('slug')
                ->filter(fn (Material $material) => filled($material->scientific_name))
                ->map(fn (Material $material) => sprintf('%s: %s', $material->name, $material->scientific_name))
                ->implode('; ') ?: null,
        );
    }
}
