<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['boundary_id', 'description', 'sort_order'])]
class Process extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'boundary_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function boundary(): BelongsTo
    {
        return $this->belongsTo(Boundary::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class)->orderBy('sort_order');
    }
}
