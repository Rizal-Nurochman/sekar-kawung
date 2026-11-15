<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['textile_id', 'description', 'image_path', 'moq_quantity', 'moq_unit', 'certification', 'compliance_standard', 'chemical_info', 'production_stages', 'sort_order', 'is_published'])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'textile_id' => 'integer',
            'moq_quantity' => 'decimal:2',
            'production_stages' => 'array',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function textile(): BelongsTo
    {
        return $this->belongsTo(Textile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function primaryAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_primary', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function scopeMaterial(Builder $query, string $materialSlug): Builder
    {
        return $query->whereHas(
            'textile.materials',
            fn (Builder $materials) => $materials->where('materials.slug', $materialSlug),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->textile->name,
        );
    }
}
