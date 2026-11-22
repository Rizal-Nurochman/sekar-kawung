<?php

use App\Models\Activity;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\Source;
use App\Models\Subprocess;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(LazilyRefreshDatabase::class);

it('cascades scheme deletion through processes, subprocesses, and activities', function () {
    $scheme = Scheme::factory()->create();
    $process = Process::factory()->for($scheme)->create();
    $subprocess = Subprocess::factory()->for($process)->create();
    Activity::factory()->for($subprocess)->create();

    $scheme->delete();

    expect(Process::count())->toBe(0)
        ->and(Subprocess::count())->toBe(0)
        ->and(Activity::count())->toBe(0);
});

it('attaches multiple options to an activity', function () {
    $activity = Activity::factory()->create();
    $options = Option::factory()->count(2)->create();

    $activity->options()->attach($options->modelKeys());

    expect($activity->options()->count())->toBe(2);
});

it('removes pivot rows when an activity or option is deleted', function () {
    $activity = Activity::factory()->create();
    $options = Option::factory()->count(2)->create();

    $activity->options()->attach($options->modelKeys());

    $options->first()->delete();

    expect($activity->fresh()->options()->count())->toBe(1);

    $activity->delete();

    expect(DB::table('activity_option')->count())->toBe(0);
});

it('allows activities without a unit or emission factor', function () {
    $activity = Activity::factory()
        ->withoutUnit()
        ->withoutEmissionFactor()
        ->create();

    $activity->refresh();

    expect($activity->activity_unit_id)->toBeNull()
        ->and($activity->emission_factor_id)->toBeNull()
        ->and($activity->activityUnit)->toBeNull()
        ->and($activity->emissionFactor)->toBeNull();
});

it('keeps emission factors when their source is deleted', function () {
    $source = Source::factory()->create();
    $factor = EmissionFactor::factory()->for($source)->create();

    $source->delete();

    expect($factor->fresh()->source_id)->toBeNull()
        ->and($factor->fresh()->source)->toBeNull();
});

it('orders activities by sort order within a subprocess', function () {
    $subprocess = Subprocess::factory()->create();

    Activity::factory()->for($subprocess)->create([
        'sort_order' => 2,
        'description' => 'Second',
    ]);
    Activity::factory()->for($subprocess)->create([
        'sort_order' => 1,
        'description' => 'First',
    ]);

    expect($subprocess->activities()->pluck('description')->all())
        ->toBe(['First', 'Second']);
});

it('stores emission factors with ten decimal places', function () {
    $factor = EmissionFactor::factory()->create([
        'ef_value' => '0.0000001234',
    ]);

    $stored = $factor->fresh()->ef_value;

    expect($stored)->toBeString()
        ->and($stored)->toBe('0.0000001234');
});
