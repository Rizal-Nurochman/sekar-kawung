<?php

namespace App\Actions\Admin;

use App\Models\Activity;
use App\Models\ActivityUnit;
use App\Models\Boundary;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class UpsertMasterRecord
{
    /**
     * @var array<int, class-string<Model>>
     */
    private const ALLOWED = [
        Scheme::class,
        Boundary::class,
        Process::class,
        Activity::class,
        ActivityUnit::class,
        Source::class,
        EmissionFactor::class,
        Option::class,
    ];

    /**
     * Create or update a master record.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $data
     */
    public function handle(string $modelClass, array $data, ?Model $record = null): Model
    {
        if (! in_array($modelClass, self::ALLOWED, true)) {
            throw new InvalidArgumentException("Model [{$modelClass}] tidak diizinkan untuk UpsertMasterRecord.");
        }

        if ($record !== null) {
            if (! $record instanceof $modelClass) {
                throw new InvalidArgumentException('Record tidak cocok dengan model yang diminta.');
            }

            $record->fill($data);
            $record->save();

            return $record;
        }

        /** @var Model $created */
        $created = $modelClass::query()->create($data);

        return $created;
    }
}
