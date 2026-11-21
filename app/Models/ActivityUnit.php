<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['unit_name'])]
class ActivityUnit extends Model
{
    use HasFactory;

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}
