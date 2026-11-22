<?php

use App\Models\Activity;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Scheme;
use Database\Seeders\LcaMasterDataSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('seeds every LCA master data table with the expected row count', function () {
    $this->seed(LcaMasterDataSeeder::class);

    $this->assertDatabaseCount('schemes', 2);
    $this->assertDatabaseCount('processes', 4);
    $this->assertDatabaseCount('subprocesses', 17);
    $this->assertDatabaseCount('activity_units', 10);
    $this->assertDatabaseCount('sources', 13);
    $this->assertDatabaseCount('emission_factors', 16);
    $this->assertDatabaseCount('options', 7);
    $this->assertDatabaseCount('activities', 59);
    $this->assertDatabaseCount('activity_option', 54);
});

it('links the seeded scheme hierarchy', function () {
    $this->seed(LcaMasterDataSeeder::class);

    $scheme = Scheme::query()->where('description', 'Gedog Tuban')->firstOrFail();

    expect($scheme->processes)->toHaveCount(2)
        ->and($scheme->processes->first()->subprocesses)->toHaveCount(5);
});

it('attaches the transport and fuel options to the expected activities', function () {
    $this->seed(LcaMasterDataSeeder::class);

    $transportActivity = Activity::query()->findOrFail(18);
    $fuelActivity = Activity::query()->findOrFail(40);

    expect($transportActivity->activity_unit_id)->toBeNull()
        ->and($transportActivity->emission_factor_id)->toBeNull()
        ->and($transportActivity->options)->toHaveCount(5)
        ->and($fuelActivity->options->pluck('description')->all())->toBe(['RON 92', 'RON 98']);
});

it('marks Tosa as an unquantified option', function () {
    $this->seed(LcaMasterDataSeeder::class);

    $tosa = Option::query()->where('description', 'Tosa')->firstOrFail();

    expect($tosa->activity_unit_id)->toBeNull()
        ->and($tosa->emission_factor_id)->toBeNull();
});

it('stores the seeded emission factor with ten decimal places', function () {
    $this->seed(LcaMasterDataSeeder::class);

    expect(EmissionFactor::query()->findOrFail(11)->ef_value)->toBe('0.0028000000');
});

it('orders the seeded schemes by their sort order', function () {
    $this->seed(LcaMasterDataSeeder::class);

    expect(Scheme::query()->orderBy('sort_order')->pluck('description')->all())
        ->toBe(['Gedog Tuban', 'Tenun ATBM Pisano']);
});

it('can be seeded twice without duplicating rows', function () {
    $this->seed(LcaMasterDataSeeder::class);
    $this->seed(LcaMasterDataSeeder::class);

    $this->assertDatabaseCount('schemes', 2);
    $this->assertDatabaseCount('activities', 59);
    $this->assertDatabaseCount('activity_option', 54);
});
