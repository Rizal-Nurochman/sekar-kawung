<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'scientific_name', 'description'])]
class Material extends Model
{
    use HasFactory;

    public function textiles(): BelongsToMany
    {
        return $this->belongsToMany(Textile::class)
            ->withPivot('share_percent')
            ->withTimestamps();
    }
}
