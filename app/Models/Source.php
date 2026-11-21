<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'link_reference'])]
class Source extends Model
{
    use HasFactory;

    public function emissionFactors(): HasMany
    {
        return $this->hasMany(EmissionFactor::class);
    }
}
