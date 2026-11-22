<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sustainability_report_id', 'activity_id', 'option_id', 'quantity', 'emission_result'])]
class SustainabilityReportItem extends Model
{
    protected function casts(): array
    {
        return [
            'sustainability_report_id' => 'integer',
            'activity_id' => 'integer',
            'option_id' => 'integer',
            'quantity' => 'decimal:4',
            'emission_result' => 'decimal:4',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(SustainabilityReport::class, 'sustainability_report_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
