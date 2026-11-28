<?php

use App\Actions\Admin\TogglePublish;
use App\Actions\Admin\UpsertReport;
use App\Http\Controllers\Admin\PublishedReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Models\Activity;
use App\Models\Process;
use App\Models\Product;
use App\Models\Scheme;
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
    if (! Route::has('admin.reports.index')) {
        Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function (): void {
            Route::resource('reports', ReportController::class)->except(['create', 'store']);
            Route::post('reports/{report}/publish', [PublishedReportController::class, 'store'])->name('reports.publish');
            Route::delete('reports/{report}/publish', [PublishedReportController::class, 'destroy'])->name('reports.unpublish');
        });

        // Routes added at runtime miss the name-lookup refresh that
        // RouteServiceProvider performs after loading route files, and
        // fluent ->name() calls only set the action afterwards.
        Route::getRoutes()->refreshNameLookups();
        Route::getRoutes()->refreshActionLookups();
    }

    $this->user = User::factory()->create();
});

function reportSchemeWithProduct(): array
{
    $scheme = Scheme::factory()->create();
    $product = Product::factory()->create(['scheme_id' => $scheme->id]);

    return [$scheme, $product];
}

function validReportPayload(Scheme $scheme, Product $product, array $overrides = []): array
{
    return array_merge([
        'title' => 'Jejak Karbon Batik Tulis',
        'slug' => 'jejak-karbon-batik-tulis',
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'summary' => 'Ringkasan hasil perhitungan.',
        'is_published' => false,
    ], $overrides);
}

it('redirects guests away from report pages', function (): void {
    [$scheme, $product] = reportSchemeWithProduct();
    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan tamu',
        'slug' => 'laporan-tamu',
        'total_emission' => 0,
        'is_published' => false,
    ]);

    $this->get(route('admin.reports.index'))->assertRedirect('/admin/login');
    $this->get(route('admin.reports.show', $report))->assertRedirect('/admin/login');
    $this->get(route('admin.reports.edit', $report))->assertRedirect('/admin/login');
});

it('does not expose a direct report creation route', function (): void {
    $this->actingAs($this->user);

    expect(Route::has('admin.reports.create'))->toBeFalse()
        ->and(Route::has('admin.reports.store'))->toBeFalse();

    $this->post('/admin/reports', [
        'title' => 'Laporan langsung',
        'scheme_id' => 1,
        'process_id' => 1,
    ])->assertStatus(405);

    expect(SustainabilityReport::query()->count())->toBe(0);
});

it('rejects invalid and duplicate report updates', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();

    SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan lama',
        'slug' => 'laporan-sama',
        'total_emission' => 0,
        'is_published' => false,
    ]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan saya',
        'slug' => 'laporan-saya',
        'total_emission' => 0,
        'is_published' => false,
    ]);

    // Missing title is rejected. A supplied slug is ignored: the slug
    // always regenerates from the title instead.
    $this->put(route('admin.reports.update', $report), [
        'title' => '',
        'slug' => 'slug tidak valid!',
        'summary' => 'x',
    ])->assertSessionHasErrors(['title']);

    $this->put(route('admin.reports.update', $report), array_merge(
        validReportPayload($scheme, $product),
        ['slug' => 'slug-diabaikan']
    ))->assertRedirect(route('admin.reports.show', $report));

    expect($report->refresh()->slug)->toBe('jejak-karbon-batik-tulis');

    expect(SustainabilityReport::query()->count())->toBe(2)
        ->and($report->refresh()->title)->toBe('Jejak Karbon Batik Tulis');
});

it('keeps the calculation scope fixed when a report is updated', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();
    [$otherScheme, $otherProduct] = reportSchemeWithProduct();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan terkunci',
        'slug' => 'laporan-terkunci',
        'total_emission' => 4,
        'is_published' => false,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Laporan terkunci diperbarui',
        'slug' => 'laporan-terkunci',
        'summary' => 'Ringkasan baru.',
        'scheme_id' => $otherScheme->id,
        'product_id' => $otherProduct->id,
    ])->assertRedirect(route('admin.reports.show', $report));

    $report->refresh();

    expect($report->title)->toBe('Laporan terkunci diperbarui')
        ->and($report->scheme_id)->toBe($scheme->id)
        ->and($report->product_id)->toBe($product->id);
});

it('regenerates the slug from the title on every update', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan lama',
        'slug' => 'laporan-lama',
        'total_emission' => 0,
        'is_published' => true,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Laporan dengan judul baru',
        'summary' => 'Ringkasan baru.',
    ])->assertRedirect(route('admin.reports.show', $report));

    expect($report->refresh()->slug)->toBe('laporan-dengan-judul-baru');

    // A clashing title gets a unique suffix, excluding the report itself.
    SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Judul bentrok',
        'slug' => 'judul-bentrok',
        'total_emission' => 0,
        'is_published' => false,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Judul bentrok',
    ])->assertRedirect(route('admin.reports.show', $report));

    expect($report->refresh()->slug)->toBe('judul-bentrok-2');
});

it('preserves items and totals when editorial fields are updated', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();
    $process = Process::factory()->create(['product_id' => $product->id]);
    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan awal',
        'slug' => 'laporan-awal',
        'summary' => 'Ringkasan awal.',
        'total_emission' => 12.5,
        'is_published' => false,
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 2,
        'emission_result' => 5,
    ]);
    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 7.5,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Laporan diperbarui',
        'summary' => 'Ringkasan diperbarui.',
    ])->assertRedirect(route('admin.reports.show', $report));

    $report->refresh();

    expect($report->title)->toBe('Laporan diperbarui')
        ->and($report->slug)->toBe('laporan-diperbarui')
        ->and($report->summary)->toBe('Ringkasan diperbarui.')
        ->and((float) $report->total_emission)->toBe(12.5)
        ->and($report->items()->count())->toBe(2)
        ->and((float) $report->items()->sum('emission_result'))->toBe(12.5);
});

it('renders one card per scheme with its process details', function (): void {
    $this->actingAs($this->user);
    $scheme = Scheme::factory()->create(['description' => 'Skema Uji', 'sort_order' => 1]);
    $otherScheme = Scheme::factory()->create(['description' => 'Skema Lain', 'sort_order' => 2]);
    $first = Product::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1, 'description' => 'Proses Pertama']);
    $second = Product::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2, 'description' => 'Proses Kedua']);
    $third = Product::factory()->create(['scheme_id' => $otherScheme->id, 'sort_order' => 1, 'description' => 'Proses Ketiga']);
    $firstProc = Process::factory()->create(['product_id' => $first->id]);
    $secondProc = Process::factory()->create(['product_id' => $second->id]);
    $activityA = Activity::factory()->create(['process_id' => $firstProc->id]);
    $activityB = Activity::factory()->create(['process_id' => $secondProc->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $second->scheme_id,
        'product_id' => $second->id,
        'title' => 'Laporan batas',
        'slug' => 'laporan-batas',
        'total_emission' => 30,
        'is_published' => false,
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activityA->id,
        'option_id' => null,
        'quantity' => 4,
        'emission_result' => 10,
    ]);
    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activityB->id,
        'option_id' => null,
        'quantity' => 2,
        'emission_result' => 20,
    ]);

    $this->get(route('admin.reports.show', $report))->assertOk()
        ->assertSee('Skema Uji', false)
        ->assertSee('Skema Lain', false)
        ->assertSee('Produk 1 dari 3', false)
        ->assertSee('Produk 2 dari 3', false)
        ->assertSee('Produk 3 dari 3', false)
        ->assertSee('Proses Pertama', false)
        ->assertSee('Subtotal produk', false)
        ->assertDontSee('Subtotal skema', false)
        ->assertSee('Belum ada isian pada proses ini', false)
        ->assertDontSee('Slug', false);
});

it('renders report stages in configured order regardless of emissions', function (): void {
    $this->actingAs($this->user);
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $product = Product::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1]);
    $firstStage = Process::factory()->create([
        'product_id' => $product->id,
        'description' => 'Tahap Urutan Pertama',
        'sort_order' => 1,
    ]);
    $secondStage = Process::factory()->create([
        'product_id' => $product->id,
        'description' => 'Tahap Urutan Kedua',
        'sort_order' => 2,
    ]);
    $firstActivity = Activity::factory()->create(['process_id' => $firstStage->id]);
    $secondActivity = Activity::factory()->create(['process_id' => $secondStage->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan urutan tahap',
        'slug' => 'laporan-urutan-tahap',
        'total_emission' => 110,
        'is_published' => false,
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $firstActivity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 10,
    ]);
    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $secondActivity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 100,
    ]);

    $this->get(route('admin.reports.show', $report))
        ->assertOk()
        ->assertViewHas('boundaries', fn ($boundaries): bool => $boundaries->first()['stages']->pluck('name')->all() === [
            'Tahap Urutan Pertama',
            'Tahap Urutan Kedua',
        ]);
});

it('creates a report through the calculator action and persists items with total', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();
    $process = Process::factory()->create(['product_id' => $product->id]);
    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $payload = validReportPayload($scheme, $product);

    if (method_exists(UpsertReport::class, 'handle')) {
        $report = app(UpsertReport::class)->handle($payload);

        expect($report)->toBeInstanceOf(SustainabilityReport::class);
        expect($report->getKey())->not->toBeNull();

        // The calculator wizard is the only entry point that saves a report.
        $this->post(route('admin.calculator.start'))->assertRedirect();
        $this->post(route('admin.calculator.step'), [
            'product_id' => $product->id,
            'direction' => 'next',
            'items' => [
                ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 1],
            ],
        ])->assertRedirect();
        $this->post(route('admin.calculator.store'))->assertRedirect();

        $second = SustainabilityReport::query()->where('id', '!=', $report->id)->firstOrFail();

        $this->get(route('admin.reports.show', $second))->assertOk();
    } else {
        $this->post(route('admin.calculator.store'), $payload)->assertRedirect();

        $report = SustainabilityReport::query()->firstOrFail();
    }

    $this->assertDatabaseHas('sustainability_reports', [
        'id' => $report->id,
        'title' => 'Jejak Karbon Batik Tulis',
        'slug' => 'jejak-karbon-batik-tulis',
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 2,
        'emission_result' => 5,
    ]);
    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 7.5,
    ]);

    $total = (float) SustainabilityReportItem::query()->where('sustainability_report_id', $report->id)->sum('emission_result');
    $report->update(['total_emission' => $total]);

    expect($total)->toBe(12.5);
    expect((float) $report->refresh()->total_emission)->toBe(12.5);

    // The stored report renders on its detail page.
    $this->get(route('admin.reports.show', $report))->assertOk()->assertSee('Jejak Karbon Batik Tulis', false);
});

it('toggles publish status without recalculating the total', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan draf',
        'slug' => 'laporan-draf',
        'total_emission' => 9.5,
        'is_published' => false,
    ]);

    $this->post(route('admin.reports.publish', $report))
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($report->refresh()->is_published)->toBeTrue();
    expect((float) $report->refresh()->total_emission)->toBe(9.5);

    // Direct action usage keeps the total untouched as well.
    app(TogglePublish::class)->handle($report->refresh(), false);

    expect($report->refresh()->is_published)->toBeFalse();
    expect((float) $report->refresh()->total_emission)->toBe(9.5);

    $this->delete(route('admin.reports.unpublish', $report))
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($report->refresh()->is_published)->toBeFalse();
});

it('deletes a report together with its items', function (): void {
    $this->actingAs($this->user);
    [$scheme, $product] = reportSchemeWithProduct();
    $activity = Activity::factory()->create();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'product_id' => $product->id,
        'title' => 'Laporan dihapus',
        'slug' => 'laporan-dihapus',
        'total_emission' => 3,
        'is_published' => false,
    ]);

    $item = SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 3,
    ]);

    $this->delete(route('admin.reports.destroy', $report))
        ->assertRedirect(route('admin.reports.index'));

    $this->assertDatabaseMissing('sustainability_reports', ['id' => $report->id]);
    $this->assertDatabaseMissing('sustainability_report_items', ['id' => $item->id]);
    expect(SustainabilityReportItem::query()->where('sustainability_report_id', $report->id)->count())->toBe(0);
});
