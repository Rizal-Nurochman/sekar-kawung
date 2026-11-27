<?php

use App\Models\Activity;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Product;
use App\Models\Scheme;
use App\Models\SustainabilityReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function calculatorContext(): array
{
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $product = Product::factory()->create([
        'scheme_id' => $scheme->id,
        'sort_order' => 1,
    ]);
    $process = Process::factory()->create([
        'product_id' => $product->id,
        'sort_order' => 1,
    ]);

    return [$scheme, $product, $process];
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
    [$scheme, $product] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Produk 1 dari 1', false)
        ->assertSee($product->description, false)
        ->assertSee($scheme->description, false)
        ->assertSee('Total sementara', false)
        ->assertDontSee('Simpan &amp; terbitkan', false);
});

it('advances through steps and preserves entries on refresh', function () {
    $this->actingAs(User::factory()->create());

    $scheme = Scheme::factory()->create(['description' => 'Skema Uji']);
    $first = Product::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1, 'description' => 'Proses Pertama']);
    $second = Product::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2, 'description' => 'Proses Kedua']);
    $firstProc = Process::factory()->create(['product_id' => $first->id, 'description' => 'Tahap A']);
    $secondProc = Process::factory()->create(['product_id' => $second->id, 'description' => 'Tahap B']);
    $activityA = Activity::factory()->create(['process_id' => $firstProc->id]);
    $activityB = Activity::factory()->create(['process_id' => $secondProc->id]);

    $this->post(route('admin.calculator.start'))->assertRedirect();

    $this->post(route('admin.calculator.step'), [
        'product_id' => $first->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activityA->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect(route('admin.calculator.edit'));

    // Refresh resumes the second step with the first step saved.
    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Produk 2 dari', false)
        ->assertSee($second->description, false);

    // Go back, the first quantity is still there.
    $this->post(route('admin.calculator.step'), [
        'product_id' => $second->id,
        'direction' => 'back',
        'items' => [
            ['activity_id' => $activityB->id, 'option_id' => null, 'quantity' => 0],
        ],
    ])->assertRedirect();

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Produk 1 dari', false)
        ->assertSee('value="3"', false);
});

it('renders a dropdown and unit pill for activities with options', function () {
    [, $product, $process] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $option = Option::factory()->create();
    $transport = Activity::factory()->withoutEmissionFactor()->create([
        'process_id' => $process->id,
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
    [, $product, $process] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $activity = Activity::factory()->create(['process_id' => $process->id]);
    $foreignOption = Option::factory()->create();

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $product->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => $foreignOption->id, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('items.0.option_id');

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects an activity outside the current process', function () {
    [$scheme, $product] = calculatorContext();
    $otherProduct = Product::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2]);
    $otherProc = Process::factory()->create(['product_id' => $otherProduct->id]);
    $foreignActivity = Activity::factory()->create(['process_id' => $otherProc->id]);
    $this->actingAs(User::factory()->create());

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $product->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $foreignActivity->id, 'option_id' => null, 'quantity' => 1],
        ],
    ])->assertSessionHasErrors('items.0.activity_id');
});

it('rejects a negative quantity', function () {
    [, $product, $process] = calculatorContext();
    $activity = Activity::factory()->create(['process_id' => $process->id]);
    $this->actingAs(User::factory()->create());

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $product->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => -1],
        ],
    ])->assertSessionHasErrors('items.0.quantity');
});

it('shows a comprehensive review only at the end with save action', function () {
    [$scheme, $product, $process] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'process_id' => $process->id,
        'emission_factor_id' => $factor->id,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $product->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 4],
        ],
    ])->assertRedirect();

    $this->get(route('admin.calculator.edit'))->assertOk()
        ->assertSee('Periksa kembali sebelum menerbitkan', false)
        ->assertSee('Produk 1 dari 1', false)
        ->assertSee('Subtotal produk', false)
        ->assertSee('Rincian per tahap', false)
        ->assertSee('Rincian aktivitas', false)
        ->assertSee('id="wizard-save"', false)
        ->assertSee($activity->description, false);
});

it('splits the review total and hotspot per scheme', function () {
    $this->actingAs(User::factory()->create());

    $gedog = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $atbm = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);

    $gedogProduct = Product::factory()->create(['scheme_id' => $gedog->id, 'sort_order' => 1]);
    $atbmProduct = Product::factory()->create(['scheme_id' => $atbm->id, 'sort_order' => 1]);

    $gedogProcess = Process::factory()->create(['product_id' => $gedogProduct->id]);
    $atbmProcess = Process::factory()->create(['product_id' => $atbmProduct->id]);

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

    $this->post(route('admin.calculator.start'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $gedogProduct->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $gedogActivity->id, 'option_id' => null, 'quantity' => 3],
        ],
    ])->assertRedirect();

    $this->post(route('admin.calculator.step'), [
        'product_id' => $atbmProduct->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $atbmActivity->id, 'option_id' => null, 'quantity' => 2],
        ],
    ])->assertRedirect();

    $response = $this->get(route('admin.calculator.edit'))->assertOk()
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

it('stores a report with auto title, items, and cached total', function () {
    [$scheme, $product, $process] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $activity = Activity::factory()->create([
        'process_id' => $process->id,
        'emission_factor_id' => $factor->id,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $product->id,
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
    [, $product, $process] = calculatorContext();
    $this->actingAs(User::factory()->create());

    $heritage = Activity::factory()->create([
        'process_id' => $process->id,
        'emission_factor_id' => null,
        'activity_unit_id' => null,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect(route('admin.calculator.edit'));

    $this->post(route('admin.calculator.step'), [
        'product_id' => $product->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $heritage->id, 'option_id' => null, 'quantity' => 9],
        ],
    ])->assertRedirect();

    $this->post(route('admin.calculator.store'))->assertRedirect();

    expect((float) SustainabilityReport::query()->firstOrFail()->total_emission)->toBe(0.0);
});
