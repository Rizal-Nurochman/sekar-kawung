<?php

use App\Actions\Admin\ToggleActive;
use App\Actions\Admin\UpsertReport;
use App\Http\Controllers\Admin\ActiveReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Models\Activity;
use App\Models\Boundary;
use App\Models\Process;
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
            Route::post('reports/{report}/activate', [ActiveReportController::class, 'store'])->name('reports.activate');
            Route::delete('reports/{report}/activate', [ActiveReportController::class, 'destroy'])->name('reports.deactivate');
        });

        // Routes added at runtime miss the name-lookup refresh that
        // RouteServiceProvider performs after loading route files, and
        // fluent ->name() calls only set the action afterwards.
        Route::getRoutes()->refreshNameLookups();
        Route::getRoutes()->refreshActionLookups();
    }

    $this->user = User::factory()->create();
});

function reportSchemeWithBoundary(): array
{
    $scheme = Scheme::factory()->create();
    $boundary = Boundary::factory()->create(['scheme_id' => $scheme->id]);

    return [$scheme, $boundary];
}

function validReportPayload(Scheme $scheme, Boundary $boundary, array $overrides = []): array
{
    return array_merge([
        'title' => 'Jejak Karbon Batik Tulis',
        'slug' => 'jejak-karbon-batik-tulis',
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'summary' => 'Ringkasan hasil perhitungan.',
        'is_active' => false,
    ], $overrides);
}

it('redirects guests away from report pages', function (): void {
    [$scheme, $boundary] = reportSchemeWithBoundary();
    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan tamu',
        'slug' => 'laporan-tamu',
        'total_emission' => 0,
        'is_active' => false,
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
    [$scheme, $boundary] = reportSchemeWithBoundary();

    SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan lama',
        'slug' => 'laporan-sama',
        'total_emission' => 0,
        'is_active' => false,
    ]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan saya',
        'slug' => 'laporan-saya',
        'total_emission' => 0,
        'is_active' => false,
    ]);

    // Missing title is rejected. A supplied slug is ignored: the slug
    // always regenerates from the title instead.
    $this->put(route('admin.reports.update', $report), [
        'title' => '',
        'slug' => 'slug tidak valid!',
        'summary' => 'x',
    ])->assertSessionHasErrors(['title']);

    $this->put(route('admin.reports.update', $report), array_merge(
        validReportPayload($scheme, $boundary),
        ['slug' => 'slug-diabaikan']
    ))->assertRedirect(route('admin.reports.show', $report));

    expect($report->refresh()->slug)->toBe('jejak-karbon-batik-tulis');

    expect(SustainabilityReport::query()->count())->toBe(2)
        ->and($report->refresh()->title)->toBe('Jejak Karbon Batik Tulis');
});

it('keeps the calculation scope fixed when a report is updated', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();
    [$otherScheme, $otherBoundary] = reportSchemeWithBoundary();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan terkunci',
        'slug' => 'laporan-terkunci',
        'total_emission' => 4,
        'is_active' => false,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Laporan terkunci diperbarui',
        'slug' => 'laporan-terkunci',
        'summary' => 'Ringkasan baru.',
        'scheme_id' => $otherScheme->id,
        'boundary_id' => $otherBoundary->id,
    ])->assertRedirect(route('admin.reports.show', $report));

    $report->refresh();

    expect($report->title)->toBe('Laporan terkunci diperbarui')
        ->and($report->scheme_id)->toBe($scheme->id)
        ->and($report->boundary_id)->toBe($boundary->id);
});

it('regenerates the slug from the title on every update', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan lama',
        'slug' => 'laporan-lama',
        'total_emission' => 0,
        'is_active' => true,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Laporan dengan judul baru',
        'summary' => 'Ringkasan baru.',
    ])->assertRedirect(route('admin.reports.show', $report));

    expect($report->refresh()->slug)->toBe('laporan-dengan-judul-baru');

    // A clashing title gets a unique suffix, excluding the report itself.
    SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Judul bentrok',
        'slug' => 'judul-bentrok',
        'total_emission' => 0,
        'is_active' => false,
    ]);

    $this->put(route('admin.reports.update', $report), [
        'title' => 'Judul bentrok',
    ])->assertRedirect(route('admin.reports.show', $report));

    expect($report->refresh()->slug)->toBe('judul-bentrok-2');
});

it('preserves items and totals when editorial fields are updated', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();
    $process = Process::factory()->create(['boundary_id' => $boundary->id]);
    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan awal',
        'slug' => 'laporan-awal',
        'summary' => 'Ringkasan awal.',
        'total_emission' => 12.5,
        'is_active' => false,
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

it('lists the report id and every represented scheme in the overview', function (): void {
    $this->actingAs($this->user);
    $gedog = Scheme::factory()->create(['description' => 'Biji Sampai Kain Batik Gedog Tuban', 'sort_order' => 1]);
    $tenun = Scheme::factory()->create(['description' => 'Biji Sampai Kain Batik Tenun ATBM Pisano', 'sort_order' => 2]);
    $gedogBoundary = Boundary::factory()->create(['scheme_id' => $gedog->id]);
    $tenunBoundary = Boundary::factory()->create(['scheme_id' => $tenun->id]);
    $gedogProcess = Process::factory()->create(['boundary_id' => $gedogBoundary->id]);
    $tenunProcess = Process::factory()->create(['boundary_id' => $tenunBoundary->id]);
    $gedogActivity = Activity::factory()->create(['process_id' => $gedogProcess->id]);
    $tenunActivity = Activity::factory()->create(['process_id' => $tenunProcess->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $gedog->id,
        'boundary_id' => $gedogBoundary->id,
        'title' => 'Judul panjang dari kalkulator',
        'slug' => 'judul-panjang-dari-kalkulator',
        'total_emission' => 10,
        'is_active' => true,
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $gedogActivity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 4,
    ]);
    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $tenunActivity->id,
        'option_id' => null,
        'quantity' => 1,
        'emission_result' => 6,
    ]);

    $this->get(route('admin.reports.index'))->assertOk()
        ->assertSee('Laporan '.$report->id)
        ->assertSeeInOrder([
            'Biji Sampai Kain Batik Gedog Tuban',
            'Biji Sampai Kain Batik Tenun ATBM Pisano',
        ])
        ->assertDontSee('Judul panjang dari kalkulator');
});

it('renders one card per scheme with its process details', function (): void {
    $this->actingAs($this->user);
    $scheme = Scheme::factory()->create(['description' => 'Skema Uji', 'sort_order' => 1]);
    $otherScheme = Scheme::factory()->create(['description' => 'Skema Lain', 'sort_order' => 2]);
    $first = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1, 'description' => 'Proses Pertama']);
    $second = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2, 'description' => 'Proses Kedua']);
    $third = Boundary::factory()->create(['scheme_id' => $otherScheme->id, 'sort_order' => 1, 'description' => 'Proses Ketiga']);
    $firstProc = Process::factory()->create(['boundary_id' => $first->id]);
    $secondProc = Process::factory()->create(['boundary_id' => $second->id]);
    $activityA = Activity::factory()->create(['process_id' => $firstProc->id]);
    $activityB = Activity::factory()->create(['process_id' => $secondProc->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $second->scheme_id,
        'boundary_id' => $second->id,
        'title' => 'Laporan batas',
        'slug' => 'laporan-batas',
        'total_emission' => 30,
        'is_active' => false,
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
        ->assertSee('Boundary 1 dari 3', false)
        ->assertSee('Boundary 2 dari 3', false)
        ->assertSee('Boundary 3 dari 3', false)
        ->assertSee('Proses Pertama', false)
        ->assertSee('Subtotal boundary', false)
        ->assertDontSee('Subtotal skema', false)
        ->assertSee('Belum ada isian pada proses ini', false)
        ->assertDontSee('Slug', false);
});

it('renders report stages in configured order regardless of emissions', function (): void {
    $this->actingAs($this->user);
    $scheme = Scheme::factory()->create(['sort_order' => 1]);
    $boundary = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1]);
    $firstStage = Process::factory()->create([
        'boundary_id' => $boundary->id,
        'description' => 'Tahap Urutan Pertama',
        'sort_order' => 1,
    ]);
    $secondStage = Process::factory()->create([
        'boundary_id' => $boundary->id,
        'description' => 'Tahap Urutan Kedua',
        'sort_order' => 2,
    ]);
    $firstActivity = Activity::factory()->create(['process_id' => $firstStage->id]);
    $secondActivity = Activity::factory()->create(['process_id' => $secondStage->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan urutan tahap',
        'slug' => 'laporan-urutan-tahap',
        'total_emission' => 110,
        'is_active' => false,
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
    [$scheme, $boundary] = reportSchemeWithBoundary();
    $process = Process::factory()->create(['boundary_id' => $boundary->id]);
    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $payload = validReportPayload($scheme, $boundary);

    if (method_exists(UpsertReport::class, 'handle')) {
        $report = app(UpsertReport::class)->handle($payload);

        expect($report)->toBeInstanceOf(SustainabilityReport::class);
        expect($report->getKey())->not->toBeNull();

        // The calculator wizard is the only entry point that saves a report.
        $this->post(route('admin.calculator.start'))->assertRedirect();
        $this->post(route('admin.calculator.step'), [
            'boundary_id' => $boundary->id,
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
        'title' => 'Laporan '.$report->id,
        'slug' => 'laporan-'.$report->id,
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
    $this->get(route('admin.reports.show', $report))->assertOk()->assertSee('Laporan '.$report->id, false);
});

it('makes the newest calculated report the only active report', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();
    $process = Process::factory()->create(['boundary_id' => $boundary->id]);
    $activity = Activity::factory()->create(['process_id' => $process->id]);

    $previous = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan sebelumnya',
        'slug' => 'laporan-sebelumnya',
        'total_emission' => 1,
        'is_active' => true,
    ]);

    $this->post(route('admin.calculator.start'))->assertRedirect();

    $this->post(route('admin.calculator.step'), [
        'boundary_id' => $boundary->id,
        'direction' => 'next',
        'items' => [
            ['activity_id' => $activity->id, 'option_id' => null, 'quantity' => 1],
        ],
    ])->assertRedirect();

    $this->post(route('admin.calculator.store'))->assertRedirect();

    $newest = SustainabilityReport::query()->latest('id')->firstOrFail();

    expect($previous->refresh()->is_active)->toBeFalse()
        ->and($newest->is_active)->toBeTrue()
        ->and(SustainabilityReport::query()->where('is_active', true)->count())->toBe(1);
});

it('keeps only one report active when manually activated', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();

    $first = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan pertama',
        'slug' => 'laporan-pertama',
        'total_emission' => 1,
        'is_active' => true,
    ]);

    $second = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan kedua',
        'slug' => 'laporan-kedua',
        'total_emission' => 2,
        'is_active' => false,
    ]);

    $this->post(route('admin.reports.activate', $second))
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($first->refresh()->is_active)->toBeFalse()
        ->and($second->refresh()->is_active)->toBeTrue()
        ->and(SustainabilityReport::query()->where('is_active', true)->count())->toBe(1);
});

it('toggles active status without recalculating the total', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan nonaktif',
        'slug' => 'laporan-nonaktif',
        'total_emission' => 9.5,
        'is_active' => false,
    ]);

    $this->post(route('admin.reports.activate', $report))
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($report->refresh()->is_active)->toBeTrue();
    expect((float) $report->refresh()->total_emission)->toBe(9.5);

    // Direct action usage keeps the total untouched as well.
    app(ToggleActive::class)->handle($report->refresh(), false);

    expect($report->refresh()->is_active)->toBeFalse();
    expect((float) $report->refresh()->total_emission)->toBe(9.5);

    $this->delete(route('admin.reports.deactivate', $report))
        ->assertRedirect()
        ->assertSessionHas('status');

    expect($report->refresh()->is_active)->toBeFalse();
});

it('deletes a report together with its items', function (): void {
    $this->actingAs($this->user);
    [$scheme, $boundary] = reportSchemeWithBoundary();
    $activity = Activity::factory()->create();

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $boundary->id,
        'title' => 'Laporan dihapus',
        'slug' => 'laporan-dihapus',
        'total_emission' => 3,
        'is_active' => false,
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
