<?php

use App\Models\Activity;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\Subprocess;
use App\Models\SustainabilityReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function calculatorContext(): array
{
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $process = Process::factory()->create([
        'scheme_id' => $scheme->id,
        'sort_order' => 1,
    ]);
    $subprocess = Subprocess::factory()->create([
        'process_id' => $process->id,
        'sort_order' => 1,
    ]);

    return [$scheme, $process, $subprocess];
}

it('redirects guests away from the calculator to login', function () {
    $this->get(route('admin.calculator.edit'))->assertRedirect('/admin/login');
    $this->post(route('admin.calculator.preview'))->assertRedirect('/admin/login');
    $this->post(route('admin.calculator.store'))->assertRedirect('/admin/login');
});

it('renders the calculator form for authenticated admins', function () {
    [$scheme, $process, $subprocess] = calculatorContext();

    Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => null,
        'activity_unit_id' => null,
    ]);

    $this->actingAs(User::factory()->create());

    $this->get(route('admin.calculator.edit', [
        'scheme_id' => $scheme->id,
        'process_id' => $process->id,
    ]))->assertOk()->assertSee('Kalkulator', false);
});

it('rejects an option that does not belong to the activity', function () {
    [$scheme, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $activity = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
    ]);
    $foreignOption = Option::factory()->create();

    $this->postJson(route('admin.calculator.preview'), [
        'scheme_id' => $scheme->id,
        'process_id' => $process->id,
        'title' => 'Uji validasi',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => $foreignOption->id, 'quantity' => 1],
        ],
    ])->assertUnprocessable()->assertJsonValidationErrors('items.0.option_id');

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects a process outside the selected scheme', function () {
    [$scheme] = calculatorContext();
    $otherScheme = Scheme::factory()->create();
    $otherProcess = Process::factory()->create(['scheme_id' => $otherScheme->id]);
    $this->actingAs(User::factory()->create());

    $this->postJson(route('admin.calculator.preview'), [
        'scheme_id' => $scheme->id,
        'process_id' => $otherProcess->id,
        'title' => 'Uji batas',
        'items' => [],
    ])->assertUnprocessable()->assertJsonValidationErrors('process_id');
});

it('calculates preview totals from option and activity factors', function () {
    [$scheme, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $activityFactor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $standalone = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => $activityFactor->id,
    ]);

    $optionFactor = EmissionFactor::factory()->create(['ef_value' => 3.0]);
    $option = Option::factory()->create(['emission_factor_id' => $optionFactor->id]);
    $transport = Activity::factory()->withoutEmissionFactor()->create([
        'subprocess_id' => $subprocess->id,
    ]);
    $transport->options()->attach($option->id);

    $response = $this->postJson(route('admin.calculator.preview'), [
        'scheme_id' => $scheme->id,
        'process_id' => $process->id,
        'title' => 'Uji hitung',
        'items' => [
            ['activity_id' => $standalone->id, 'option_id' => null, 'quantity' => 4],
            ['activity_id' => $transport->id, 'option_id' => $option->id, 'quantity' => 2],
        ],
    ]);

    $response->assertOk();
    expect((float) $response->json('total'))->toBe(16.0);
    expect((float) $response->json('items.0.emission_result'))->toBe(10.0);
    expect((float) $response->json('items.1.emission_result'))->toBe(6.0);
});

it('treats heritage activities without factors as zero emission', function () {
    [$scheme, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $heritage = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => null,
        'activity_unit_id' => null,
    ]);

    $response = $this->postJson(route('admin.calculator.preview'), [
        'scheme_id' => $scheme->id,
        'process_id' => $process->id,
        'title' => 'Uji heritage',
        'items' => [
            ['activity_id' => $heritage->id, 'option_id' => null, 'quantity' => 9],
        ],
    ]);

    $response->assertOk();
    expect((float) $response->json('total'))->toBe(0.0);
});

it('stores a report with items and cached total', function () {
    [$scheme, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => $factor->id,
    ]);

    $response = $this->post(route('admin.calculator.store'), [
        'scheme_id' => $scheme->id,
        'process_id' => $process->id,
        'title' => 'Kain Batik Gedog Tuban — Batch 2026',
        'summary' => 'Batch uji.',
        'is_published' => false,
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 4],
        ],
    ]);

    $response->assertRedirect()->assertSessionHas('status');

    $report = SustainabilityReport::query()->firstOrFail();
    expect($report->title)->toBe('Kain Batik Gedog Tuban — Batch 2026')
        ->and($report->slug)->not->toBeEmpty()
        ->and((float) $report->total_emission)->toBe(10.0)
        ->and($report->items)->toHaveCount(1)
        ->and((float) $report->items->first()->emission_result)->toBe(10.0)
        ->and((float) $report->items->first()->quantity)->toBe(4.0);
});
