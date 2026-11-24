<?php

use App\Http\Controllers\Admin\Config\ActivityController;
use App\Http\Controllers\Admin\Config\ActivityOptionController;
use App\Http\Controllers\Admin\Config\ActivityUnitController;
use App\Http\Controllers\Admin\Config\ConfigController;
use App\Http\Controllers\Admin\Config\EmissionFactorController;
use App\Http\Controllers\Admin\Config\OptionController;
use App\Http\Controllers\Admin\Config\ProcessController;
use App\Http\Controllers\Admin\Config\ProductController;
use App\Http\Controllers\Admin\Config\SchemeController;
use App\Http\Controllers\Admin\Config\SourceController;
use App\Models\Activity;
use App\Models\ActivityUnit;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Product;
use App\Models\Scheme;
use App\Models\Source;
use App\Models\SustainabilityReport;
use App\Models\SustainabilityReportItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

/*
 * These routes mirror the snippet to add to routes/web.php (see task
 * summary). They are registered here so this slice stays testable before
 * the wiring lands; once web.php contains them, this guard is a no-op.
 */
beforeEach(function (): void {
    if (! Route::has('admin.config.schemes.index')) {
        Route::middleware(['web', 'auth'])->prefix('admin/config')->name('admin.config.')->group(function (): void {
            Route::get('/', [ConfigController::class, 'index'])->name('index');
            Route::resource('schemes', SchemeController::class)->except(['show']);
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('processes', ProcessController::class)->except(['show']);
            Route::resource('activities', ActivityController::class)->except(['show']);
            Route::resource('activity-units', ActivityUnitController::class)->except(['show'])->parameters(['activity-units' => 'activityUnit']);
            Route::resource('sources', SourceController::class)->except(['show']);
            Route::resource('emission-factors', EmissionFactorController::class)->except(['show'])->parameters(['emission-factors' => 'emissionFactor']);
            Route::resource('options', OptionController::class)->except(['show']);
            Route::get('activities/{activity}/options/edit', [ActivityOptionController::class, 'edit'])->name('activity-options.edit');
            Route::put('activities/{activity}/options', [ActivityOptionController::class, 'update'])->name('activity-options.update');
        });

        // Routes registered at runtime (after the RouteServiceProvider boot)
        // need an explicit lookup refresh, otherwise route()/Route::has()
        // only see names baked into the action (e.g. Route::resource).
        Route::getRoutes()->refreshNameLookups();
    }

    $this->user = User::factory()->create();
});

it('redirects guests away from config pages', function (): void {
    $this->get(route('admin.config.schemes.index'))->assertRedirect('/admin/login');
    $this->get(route('admin.config.sources.create'))->assertRedirect('/admin/login');
    $this->get(route('admin.config.index'))->assertRedirect('/admin/login');
});

it('creates the full master chain through config endpoints', function (): void {
    $this->actingAs($this->user);

    $this->get(route('admin.config.index'))->assertOk()->assertSee('Skema', false);

    // Scheme.
    $this->post(route('admin.config.schemes.store'), [
        'description' => 'Skema Batik',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.config.schemes.index'));

    $scheme = Scheme::query()->where('description', 'Skema Batik')->firstOrFail();

    // Validation: description is required.
    $this->post(route('admin.config.schemes.store'), [
        'description' => '',
        'sort_order' => 0,
    ])->assertSessionHasErrors('description');

    // Activity unit + uniqueness.
    $this->post(route('admin.config.activity-units.store'), [
        'unit_name' => 'kWh',
    ])->assertRedirect(route('admin.config.activity-units.index'));

    $this->post(route('admin.config.activity-units.store'), [
        'unit_name' => 'kWh',
    ])->assertSessionHasErrors('unit_name');

    $unit = ActivityUnit::query()->where('unit_name', 'kWh')->firstOrFail();

    // Source.
    $this->post(route('admin.config.sources.store'), [
        'title' => 'IPCC 2006',
        'link_reference' => 'https://example.com/ipcc',
    ])->assertRedirect(route('admin.config.sources.index'));

    $source = Source::query()->where('title', 'IPCC 2006')->firstOrFail();

    // Emission factor.
    $this->post(route('admin.config.emission-factors.store'), [
        'source_id' => $source->id,
        'unit_name' => 'kg CO2e/kWh',
        'ef_value' => 0.85,
    ])->assertRedirect(route('admin.config.emission-factors.index'));

    $factor = EmissionFactor::query()->where('unit_name', 'kg CO2e/kWh')->firstOrFail();

    // Option.
    $this->post(route('admin.config.options.store'), [
        'activity_unit_id' => $unit->id,
        'emission_factor_id' => $factor->id,
        'description' => 'Listrik PLN',
    ])->assertRedirect(route('admin.config.options.index'));

    $option = Option::query()->where('description', 'Listrik PLN')->firstOrFail();

    // Product → process → activity chain.
    $this->post(route('admin.config.products.store'), [
        'scheme_id' => $scheme->id,
        'description' => 'Produk Pewarnaan',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.config.products.index'));

    $product = Product::query()->where('description', 'Produk Pewarnaan')->firstOrFail();

    $this->post(route('admin.config.processes.store'), [
        'product_id' => $product->id,
        'description' => 'Proses Pencelupan',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.config.processes.index'));

    $process = Process::query()->where('description', 'Proses Pencelupan')->firstOrFail();

    $this->post(route('admin.config.activities.store'), [
        'process_id' => $process->id,
        'activity_unit_id' => $unit->id,
        'emission_factor_id' => $factor->id,
        'description' => 'Pemakaian listrik pencelupan',
        'sort_order' => 1,
    ])->assertRedirect(route('admin.config.activities.index'));

    $activity = Activity::query()->where('description', 'Pemakaian listrik pencelupan')->firstOrFail();

    expect($activity->activity_unit_id)->toBe($unit->id)
        ->and($activity->emission_factor_id)->toBe($factor->id);

    $this->get(route('admin.config.activities.index'))->assertOk()->assertSee('Pemakaian listrik pencelupan', false);
    $this->get(route('admin.config.activity-units.index'))->assertOk()->assertSee('kWh', false);

    // Update works through the same Request.
    $this->put(route('admin.config.schemes.update', $scheme), [
        'description' => 'Skema Batik Revisi',
        'sort_order' => 2,
    ])->assertRedirect(route('admin.config.schemes.index'));

    expect($scheme->refresh()->description)->toBe('Skema Batik Revisi');

    // Deleting an unreferenced option succeeds.
    $this->delete(route('admin.config.options.destroy', $option))
        ->assertRedirect(route('admin.config.options.index'));

    $this->assertDatabaseMissing('options', ['id' => $option->id]);
});

it('syncs the activity-option pivot', function (): void {
    $this->actingAs($this->user);

    $activity = Activity::factory()->create();
    $optionA = Option::factory()->create();
    $optionB = Option::factory()->create();

    $this->get(route('admin.config.activity-options.edit', $activity))->assertOk();

    $this->put(route('admin.config.activity-options.update', $activity), [
        'option_ids' => [$optionA->id, $optionB->id],
    ])->assertRedirect(route('admin.config.activities.index'));

    expect($activity->refresh()->options->pluck('id')->sort()->values()->all())
        ->toBe(collect([$optionA->id, $optionB->id])->sort()->values()->all());

    // Unknown option ids are rejected and the pivot is untouched.
    $this->put(route('admin.config.activity-options.update', $activity), [
        'option_ids' => [999999],
    ])->assertSessionHasErrors('option_ids.0');

    expect($activity->refresh()->options)->toHaveCount(2);

    // Syncing an empty list detaches everything.
    $this->put(route('admin.config.activity-options.update', $activity), [
        'option_ids' => [],
    ])->assertRedirect(route('admin.config.activities.index'));

    expect($activity->refresh()->options)->toHaveCount(0);
});

it('blocks deletion of a record still referenced by a report', function (): void {
    $this->actingAs($this->user);

    // NOTE: master-table FKs themselves use cascade/nullOnDelete, so the only
    // genuine FK-violation path is via sustainability reports/items which use
    // restrictOnDelete. This exercises DeleteMasterRecord's friendly error.
    $scheme = Scheme::factory()->create();
    $product = Product::factory()->create(['scheme_id' => $scheme->id]);
    $activity = Activity::factory()->create();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan Terkait',
        'slug' => 'laporan-terkait',
        'total_emission' => 1,
        'is_published' => false,
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 1,
    ]);

    $this->delete(route('admin.config.schemes.destroy', $scheme))
        ->assertSessionHasErrors('general');

    $this->assertDatabaseHas('schemes', ['id' => $scheme->id]);

    $this->delete(route('admin.config.activities.destroy', $activity))
        ->assertSessionHasErrors('general');

    $this->assertDatabaseHas('activities', ['id' => $activity->id]);
});
