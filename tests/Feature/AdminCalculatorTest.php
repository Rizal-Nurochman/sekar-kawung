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
    $this->post(route('admin.calculator.start'))->assertRedirect('/admin/login');
    $this->post(route('admin.calculator.step'))->assertRedirect('/admin/login');
    $this->post(route('admin.calculator.backFromReview'))->assertRedirect('/admin/login');
    $this->post(route('admin.calculator.reset'))->assertRedirect('/admin/login');
    $this->post(route('admin.calculator.store'))->assertRedirect('/admin/login');
});

it('shows a single start button without report identity fields', function () {
    calculatorContext();
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Mulai Menghitung', false)
        ->assertSee('id="start-calculator"', false)
        ->assertDontSee('Judul laporan', false)
        ->assertDontSee('Ringkasan', false)
        ->assertDontSee('Simpan &amp; terbitkan', false)
        ->assertDontSee('Simpan & terbitkan', false);
});

it('starts the wizard and shows the first process with schema name', function () {
    [$scheme, $process] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Proses 1 dari 1', false)
        ->assertSee($process->description, false)
        ->assertSee($scheme->description, false)
        ->assertSee('Total sementara', false)
        ->assertDontSee('Simpan &amp; terbitkan', false);
});

it('advances through steps and preserves entries on refresh', function () {
    $this->actingAs(User::factory()->create());

    $scheme = Scheme::factory()->create(['description' => 'Skema Uji']);
    $first = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1, 'description' => 'Proses Pertama']);
    $second = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2, 'description' => 'Proses Kedua']);
    $firstSub = Subprocess::factory()->create(['process_id' => $first->id, 'description' => 'Tahap A']);
    $secondSub = Subprocess::factory()->create(['process_id' => $second->id, 'description' => 'Tahap B']);
    $activityA = Activity::factory()->create(['subprocess_id' => $firstSub->id]);
    $activityB = Activity::factory()->create(['subprocess_id' => $secondSub->id]);

    $this->post(route('admin.calculator.start'))->assertRedirect();

    $this->post(route('admin.calculator.step'), [
        'process_id' => $first->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activityA->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect(route('admin.calculator.edit'));

    // Refresh resumes the second step with the first step saved.
    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Proses 2 dari', false)
        ->assertSee($second->description, false);

    // Go back, the first quantity is still there.
    $this->post(route('admin.calculator.step'), [
        'process_id' => $second->id,
        'direction' => 'back',
        'items' => [
            ['activity_id' => $activityB->id, 'option_id' => null, 'quantity' => 0],
        ],
    ])->assertRedirect();

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Proses 1 dari', false)
        ->assertSee('value="3"', false);
});

it('renders a dropdown and unit pill for activities with options', function () {
    [, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $option = Option::factory()->create();
    $transport = Activity::factory()->withoutEmissionFactor()->create([
        'subprocess_id' => $subprocess->id,
    ]);
    $transport->options()->attach($option->id);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Pilih opsi', false)
        ->assertSee($option->description, false)
        ->assertSee('data-option-select', false)
        ->assertSee('data-unit-pill', false);
});

it('rejects an option that does not belong to the activity', function () {
    [, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $activity = Activity::factory()->create(['subprocess_id' => $subprocess->id]);
    $foreignOption = Option::factory()->create();

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => $foreignOption->id, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('items.0.option_id');

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects an activity outside the current process', function () {
    [$scheme, $process] = calculatorContext();
    $otherProcess = Process::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $otherSub = Subprocess::factory()->create(['process_id' => $otherProcess->id]);
    $foreignActivity = Activity::factory()->create(['subprocess_id' => $otherSub->id]);
    $this->actingAs(User::factory()->create());

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $foreignActivity->id, 'option_id' => null, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('items.0.activity_id');
});

it('rejects a negative quantity', function () {
    [, $process, $subprocess] = calculatorContext();
    $activity = Activity::factory()->create(['subprocess_id' => $subprocess->id]);
    $this->actingAs(User::factory()->create());

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => -1],
        ],
    ])->assertSessionHasErrors('items.0.quantity');
});

it('shows a comprehensive review only at the end with save action', function () {
    [$scheme, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => $factor->id,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 4],
        ],
    ])->assertRedirect();

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Periksa kembali sebelum menerbitkan', false)
        ->assertSee('Rincian per proses', false)
        ->assertSee('Rincian aktivitas', false)
        ->assertSee('id="wizard-save"', false)
        ->assertSee($activity->description, false);
});

it('stores a report with auto title, items, and cached total', function () {
    [$scheme, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => $factor->id,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 4],
        ],
    ])->assertRedirect();

    $response = $this->post(route('admin.calculator.store'));
    $response->assertRedirect();
    $response->assertSessionHas('status');

    $report = SustainabilityReport::query()->firstOrFail();
    expect($report->title)->not->toBeEmpty()
        ->and($report->slug)->not->toBeEmpty()
        ->and($report->is_published)->toBeTrue()
        ->and((float) $report->total_emission)->toBe(10.0)
        ->and($report->items)->toHaveCount(1)
        ->and((float) $report->items->first()->emission_result)->toBe(10.0)
        ->and((float) $report->items->first()->quantity)->toBe(4.0);

    // Wizard is cleared after save.
    $this->get(route('admin.calculator.edit'))->assertOk()->assertSee('Mulai Menghitung', false);
});

it('treats heritage activities without factors as zero emission in review', function () {
    [, $process, $subprocess] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $heritage = Activity::factory()->create([
        'subprocess_id' => $subprocess->id,
        'emission_factor_id' => null,
        'activity_unit_id' => null,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'process_id' => $process->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $heritage->id, 'option_id' => null, 'quantity' => 9],
        ],
    ])->assertRedirect();

    $this->post(route('admin.calculator.store'))->assertRedirect();

    expect((float) SustainabilityReport::query()->firstOrFail()->total_emission)->toBe(0.0);
});
