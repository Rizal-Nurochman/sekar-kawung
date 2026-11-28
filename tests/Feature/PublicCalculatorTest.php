<?php

use App\Actions\Public\CalculatorWizard;
use App\Models\Activity;
use App\Models\Boundary;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\SustainabilityReport;
use App\Models\SustainabilityReportItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function publicCalculatorContext(): array
{
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $boundary = Boundary::factory()->create([
        'scheme_id' => $scheme->id,
        'sort_order' => 1,
    ]);
    $process = Process::factory()->create([
        'boundary_id' => $boundary->id,
        'sort_order' => 1,
    ]);

    return [$scheme, $boundary, $process];
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
    [$scheme, $boundary, $process] = publicCalculatorContext();

    $option = Option::factory()->create();
    $transport = Activity::factory()->create(['process_id' => $process->id]);
    $transport->options()->attach($option->id);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 1 dari', false)
        ->assertSee($scheme->description, false)
        ->assertSee($boundary->description, false)
        ->assertSee('Pilih opsi', false)
        ->assertSee($option->description, false)
        ->assertSee('data-calculator-row', false)
        ->assertSee('data-option-select', false)
        ->assertSee('data-unit-pill', false)
        ->assertSee('x-data="adminCalculator(', false)
        ->assertSee('Total sementara', false)
        ->assertDontSee('Simpan', false);
});

it('carries each scheme subtotal into the next step', function () {
    $gedog = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $atbm = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);

    $gedogBoundary = Boundary::factory()->create(['scheme_id' => $gedog->id, 'sort_order' => 1]);
    $atbmBoundary = Boundary::factory()->create(['scheme_id' => $atbm->id, 'sort_order' => 1]);

    $gedogProcess = Process::factory()->create(['boundary_id' => $gedogBoundary->id]);
    Process::factory()->create(['boundary_id' => $atbmBoundary->id]);

    $gedogActivity = Activity::factory()->create([
        'process_id' => $gedogProcess->id,
        'emission_factor_id' => EmissionFactor::factory()->create(['ef_value' => 2.0])->id,
    ]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $gedogBoundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $gedogActivity->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect();

    // On the ATBM step, the Gedog subtotal is carried over (3 × 2 = 6)
    // while the ATBM scheme starts at zero until typed.
    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 2 dari', false)
        ->assertSee('Gedog Tuban', false)
        ->assertSee('Tenun ATBM Pisano', false)
        ->assertSee('baseSchemeTotals', false)
        ->assertSee('currentSchemeId', false);

    $wizard = app(CalculatorWizard::class);
    $atbmFull = $wizard->boundaryWithInventory($atbmBoundary);
    $schemeTotals = collect($wizard->baseTotals($atbmFull, $wizard->state()['entries']))
        ->keyBy('id');

    expect($schemeTotals->get($gedog->id)['total'])->toBe(6.0)
        ->and($schemeTotals->get($atbm->id))->toBeNull();
});

it('advances through steps and preserves entries on refresh', function () {
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $first = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1]);
    $second = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $firstProc = Process::factory()->create(['boundary_id' => $first->id]);
    $secondProc = Process::factory()->create(['boundary_id' => $second->id]);
    $activityA = Activity::factory()->create(['process_id' => $firstProc->id]);
    $activityB = Activity::factory()->create(['process_id' => $secondProc->id]);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $first->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activityA->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 2 dari', false);

    $this->post(route('calculator.step'), [
        'boundary_id' => $second->id,
        'direction' => 'back',
        'items' => [
            ['activity_id' => $activityB->id, 'option_id' => null, 'quantity' => 0],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 1 dari', false)
        ->assertSee('value="3"', false);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('shows the full report after the last step without saving', function () {
    [$scheme, $boundary, $process] = publicCalculatorContext();

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'process_id' => $process->id,
        'emission_factor_id' => $factor->id,
    ]);

    $this->post(route('calculator.start'))->assertRedirect(route('calculator'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 4],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Periksa kembali hasil perhitungan Anda', false)
        ->assertSee($scheme->description, false)
        ->assertSee('10', false)
        ->assertSee('kg CO2e', false)
        ->assertSee('Subtotal boundary', false)
        ->assertSee('Rincian per tahap', false)
        ->assertSee('Rincian aktivitas', false)
        ->assertSee($activity->description, false)
        ->assertSee('id="calculator-print"', false)
        ->assertDontSee('Simpan', false);

    expect(SustainabilityReport::query()->count())->toBe(0)
        ->and(SustainabilityReportItem::query()->count())->toBe(0);
});

it('splits the review total and hotspot per scheme', function () {
    $gedog = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $atbm = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);

    $gedogBoundary = Boundary::factory()->create(['scheme_id' => $gedog->id, 'sort_order' => 1]);
    $atbmBoundary = Boundary::factory()->create(['scheme_id' => $atbm->id, 'sort_order' => 1]);

    $gedogProcess = Process::factory()->create(['boundary_id' => $gedogBoundary->id]);
    $atbmProcess = Process::factory()->create(['boundary_id' => $atbmBoundary->id]);

    $gedogFactor = EmissionFactor::factory()->create(['ef_value' => 2.0]);
    $atbmFactor = EmissionFactor::factory()->create(['ef_value' => 5.0]);

    $gedogActivity = Activity::factory()->create([
        'process_id' => $gedogProcess->id,
        'emission_factor_id' => $gedogFactor->id,
        'description' => 'Aktivitas Gedog',
    ]);
    $atbmActivity = Activity::factory()->create([
        'process_id' => $atbmProcess->id,
        'emission_factor_id' => $atbmFactor->id,
        'description' => 'Aktivitas ATBM',
    ]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $gedogBoundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $gedogActivity->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect();

    $this->post(route('calculator.step'), [
        'boundary_id' => $atbmBoundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $atbmActivity->id, 'option_id' => null, 'quantity' => 2],
        ],
    ])->assertRedirect();

    $response = $this->get(route('calculator'))->assertOk()
        ->assertSee('Gedog Tuban', false)
        ->assertSee('Tenun ATBM Pisano', false)
        // Gedog: 3 × 2 = 6, ATBM: 2 × 5 = 10
        ->assertSee('6', false)
        ->assertSee('10', false)
        ->assertSee('Aktivitas Gedog', false)
        ->assertSee('Aktivitas ATBM', false);

    // One hotspot line per scheme.
    expect(substr_count($response->getContent(), 'Kontribusi terbesar'))->toBe(2);
});

it('counts zero-emission rows correctly: blank stays unfilled, tosa and heritage count as filled but emit nothing', function () {
    [, $boundary, $process] = publicCalculatorContext();

    $tosa = Option::factory()->withoutEmissionFactor()->withoutUnit()->create(['description' => 'Tosa']);
    $transport = Activity::factory()->withoutEmissionFactor()->withoutUnit()->create(['process_id' => $process->id]);
    $transport->options()->attach($tosa->id);
    $heritage = Activity::factory()->withoutEmissionFactor()->withoutUnit()->create(['process_id' => $process->id]);
    $blank = Activity::factory()->withoutEmissionFactor()->withoutUnit()->create(['process_id' => $process->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $transport->id, 'option_id' => $tosa->id, 'quantity' => 5],
            ['activity_id' => $heritage->id, 'option_id' => null, 'quantity' => 9],
            ['activity_id' => $blank->id, 'option_id' => null, 'quantity' => null],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Tahap manual tanpa emisi', false);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects an option that does not belong to the activity', function () {
    [, $boundary, $process] = publicCalculatorContext();

    $activity = Activity::factory()->create(['process_id' => $process->id]);
    $foreignOption = Option::factory()->create();

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => $foreignOption->id, 'quantity' => 5],
        ],
    ])->assertSessionHasErrors('items.0.option_id');

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects an activity outside the current process', function () {
    [$scheme, $boundary] = publicCalculatorContext();
    $otherBoundary = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $otherProc = Process::factory()->create(['boundary_id' => $otherBoundary->id]);
    $foreignActivity = Activity::factory()->create(['process_id' => $otherProc->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $foreignActivity->id, 'option_id' => null, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('items.0.activity_id');
});

it('rejects a negative quantity', function () {
    [, $boundary, $process] = publicCalculatorContext();
    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => -1],
        ],
    ])->assertSessionHasErrors('items.0.quantity');
});

it('redirects to the saved position when posting out of order', function () {
    [$scheme, $boundary] = publicCalculatorContext();
    $otherBoundary = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $otherProc = Process::factory()->create(['boundary_id' => $otherBoundary->id]);
    $foreignActivity = Activity::factory()->create(['process_id' => $otherProc->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $otherBoundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $foreignActivity->id, 'option_id' => null, 'quantity' => 2],
        ],
    ])->assertRedirect(route('calculator'))->assertSessionHasErrors('general');

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 1 dari', false);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('restarts cleanly after reset with no persisted rows', function () {
    [, $boundary, $process] = publicCalculatorContext();

    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 7],
        ],
    ])->assertRedirect();

    $this->post(route('calculator.reset'))->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Mulai Menghitung', false)
        ->assertDontSee('Boundary 1 dari', false);

    expect(SustainabilityReport::query()->count())->toBe(0)
        ->and(SustainabilityReportItem::query()->count())->toBe(0);
});

it('walks only the selected scheme when one is chosen', function () {
    $gedog = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $atbm = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);

    $gedogBoundary = Boundary::factory()->create(['scheme_id' => $gedog->id, 'sort_order' => 1, 'description' => 'Boundary Gedog']);
    $atbmBoundary = Boundary::factory()->create(['scheme_id' => $atbm->id, 'sort_order' => 1, 'description' => 'Boundary ATBM']);

    $gedogProcess = Process::factory()->create(['boundary_id' => $gedogBoundary->id]);
    Process::factory()->create(['boundary_id' => $atbmBoundary->id]);

    $gedogActivity = Activity::factory()->create([
        'process_id' => $gedogProcess->id,
        'emission_factor_id' => EmissionFactor::factory()->create(['ef_value' => 2.0])->id,
    ]);

    $this->post(route('calculator.start'), ['scheme_id' => $gedog->id])->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 1 dari 1', false)
        ->assertSee('Boundary Gedog', false)
        ->assertDontSee('Boundary ATBM', false);

    $this->post(route('calculator.step'), [
        'boundary_id' => $gedogBoundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $gedogActivity->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect();

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Periksa kembali hasil perhitungan Anda', false)
        ->assertSee('Gedog Tuban', false)
        ->assertDontSee('Boundary ATBM', false);
});

it('walks every scheme when Semua skema is selected', function () {
    $gedog = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $atbm = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);

    Boundary::factory()->create(['scheme_id' => $gedog->id, 'sort_order' => 1]);
    Boundary::factory()->create(['scheme_id' => $atbm->id, 'sort_order' => 1]);

    $this->post(route('calculator.start'), ['scheme_id' => ''])->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 1 dari 2', false);
});

it('resets progress when the scheme is changed mid-run', function () {
    $gedog = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $atbm = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);

    $gedogBoundary = Boundary::factory()->create(['scheme_id' => $gedog->id, 'sort_order' => 1, 'description' => 'Boundary Gedog']);
    $atbmBoundary = Boundary::factory()->create(['scheme_id' => $atbm->id, 'sort_order' => 1, 'description' => 'Boundary ATBM']);

    $gedogProcess = Process::factory()->create(['boundary_id' => $gedogBoundary->id]);
    $atbmProcess = Process::factory()->create(['boundary_id' => $atbmBoundary->id]);

    $gedogActivity = Activity::factory()->create(['process_id' => $gedogProcess->id]);

    $this->post(route('calculator.start'));

    $this->post(route('calculator.step'), [
        'boundary_id' => $gedogBoundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $gedogActivity->id, 'option_id' => null, 'quantity' => 5],
        ],
    ])->assertRedirect();

    $this->post(route('calculator.start'), ['scheme_id' => $atbm->id])->assertRedirect(route('calculator'));

    $this->get(route('calculator'))->assertOk()
        ->assertSee('Boundary 1 dari 1', false)
        ->assertSee('Boundary ATBM', false);

    $wizard = app(CalculatorWizard::class);
    expect($wizard->state()['scheme_id'])->toBe($atbm->id)
        ->and($wizard->state()['entries'])->toBe([]);
});
