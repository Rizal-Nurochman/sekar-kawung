<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['scheme_id', 'description', 'sort_order'])]
class Process extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'scheme_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(Scheme::class);
    }

    public function subprocesses(): HasMany
    {
        return $this->hasMany(Subprocess::class)->orderBy('sort_order');
    }
}
