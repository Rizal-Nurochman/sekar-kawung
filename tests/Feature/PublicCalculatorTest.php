<?php

use App\Models\Activity;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\Subprocess;
use App\Models\SustainabilityReport;
use App\Models\SustainabilityReportItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publicCalculatorContext(): array
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

it('lets guests start the public wizard with no login and no report rows', function () {
    publicCalculatorContext();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Public Calculator', false)
        ->assertSee('Mulai Menghitung', false)
        ->assertSee('id="start-calculator"', false)
        ->assertDontSee('Simpan', false);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    expect(SustainabilityReport::query()->count())->toBe(0)
        ->and(SustainabilityReportItem::query()->count())->toBe(0);
});

it('shows process 1 with option dropdown, unit pill, and live-total wiring', function () {
    [, , $subprocess] = publicCalculatorContext();

    $option = Option::factory()->create();
    $transport = Activity::factory()->create(['subprocess_id' => $subprocess->id]);
    $transport->options()->attach($option->id);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Proses 1 dari', false)
        ->assertSee('Pilih opsi', false)
        ->assertSee($option->description, false)
        ->assertSee('data-calculator-row', false)
        ->assertSee('data-option-select', false)
        ->assertSee('data-unit-pill', false)
        ->assertSee('x-data="adminCalculator(', false)
        ->assertSee('Total sementara', false)
        ->assertDontSee('Simpan', false);
});

it('advances through steps and preserves entries on refresh', function () {
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $first = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1]);
    $second = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $firstSub = Subprocess::factory()->create(['process_id' => $first->id]);
    $secondSub = Subprocess::factory()->create(['process_id' => $second->id]);
    $activityA = Activity::factory()->create(['subprocess_id' => $firstSub->id]);
    $activityB = Activity::factory()->create(['subprocess_id' => $secondSub->id]);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    $this->post(route('calculator.step'), [
        'process_id' => $first->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activityA->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Proses 2 dari', false);

    $this->post(route('calculator.step'), [
        'process_id' => $second->id,
        'direction' => 'back',
        'items' => [
            ['activity_id' => $activityB->id, 'option_id' => null, 'quantity' => 0],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Proses 1 dari', false)
        ->assertSee('value="3"', false);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('shows the full report after the last step without saving', function () {
    [, $process, $subprocess] = publicCalculatorContext();

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => $factor->id,
    ]);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    $this->post(route('calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 4],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Periksa kembali hasil perhitungan Anda', false)
        ->assertSee('Total emisi', false)
        ->assertSee('10', false)
        ->assertSee('kg CO2e', false)
        ->assertSee('Subtotal proses', false)
        ->assertSee('Rincian per tahap', false)
        ->assertSee('Rincian aktivitas', false)
        ->assertSee($activity->description, false)
        ->assertSee('id="calculator-print"', false)
        ->assertDontSee('Simpan', false);

    expect(SustainabilityReport::query()->count())->toBe(0)
        ->and(SustainabilityReportItem::query()->count())->toBe(0);
});

it('counts zero-emission rows correctly: blank stays unfilled, tosa and heritage count as filled but emit nothing', function () {
    [, $process, $subprocess] = publicCalculatorContext();

    $tosa = Option::factory()->withoutEmissionFactor()->withoutUnit()->create(['description' => 'Tosa']);
    $transport = Activity::factory()->withoutEmissionFactor()->withoutUnit()->create(['subprocess_id' => $subprocess->id]);
    $transport->options()->attach($tosa->id);
    $heritage = Activity::factory()->withoutEmissionFactor()->withoutUnit()->create(['subprocess_id' => $subprocess->id]);
    $blank = Activity::factory()->withoutEmissionFactor()->withoutUnit()->create(['subprocess_id' => $subprocess->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $transport->id, 'option_id' => $tosa->id, 'quantity' => 5],
            ['activity_id' => $heritage->id, 'option_id' => null, 'quantity' => 9],
            ['activity_id' => $blank->id, 'option_id' => null, 'quantity' => null],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('2 dari 3 aktivitas terisi', false)
        ->assertSee('Tahap manual tanpa emisi', false);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects an option that does not belong to the activity', function () {
    [, $process, $subprocess] = publicCalculatorContext();

    $activity = Activity::factory()->create(['subprocess_id' => $subprocess->id]);
    $foreignOption = Option::factory()->create();

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => $foreignOption->id, 'quantity' => 5],
        ],
    ])->assertSessionHasErrors('items.0.option_id');

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects an activity outside the current process', function () {
    [$scheme, $process] = publicCalculatorContext();
    $otherProcess = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $otherSub = Subprocess::factory()->create(['process_id' => $otherProcess->id]);
    $foreignActivity = Activity::factory()->create(['subprocess_id' => $otherSub->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $foreignActivity->id, 'option_id' => null, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('items.0.activity_id');
});

it('rejects a negative quantity', function () {
    [, $process, $subprocess] = publicCalculatorContext();
    $activity = Activity::factory()->create(['subprocess_id' => $subprocess->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => -1],
        ],
    ])->assertSessionHasErrors('items.0.quantity');
});

it('redirects to the saved position when posting out of order', function () {
    [$scheme, $process] = publicCalculatorContext();
    $otherProcess = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $otherSub = Subprocess::factory()->create(['process_id' => $otherProcess->id]);
    $foreignActivity = Activity::factory()->create(['subprocess_id' => $otherSub->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'process_id' => $otherProcess->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $foreignActivity->id, 'option_id' => null, 'quantity' => 2],
        ],
    ])->assertRedirect(route('calculator'))->assertSessionHasErrors('general');

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Proses 1 dari', false);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('restarts cleanly after reset with no persisted rows', function () {
    [, $process, $subprocess] = publicCalculatorContext();

    $activity = Activity::factory()->create(['subprocess_id' => $subprocess->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 7],
        ],
    ])->assertRedirect();

    $this->post(route('calculator.reset'))->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Mulai Menghitung', false)
        ->assertDontSee('Proses 1 dari', false);

    expect(SustainabilityReport::query()->count())->toBe(0)
        ->and(SustainabilityReportItem::query()->count())->toBe(0);
});
