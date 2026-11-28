<?php

use App\Models\Activity;
use App\Models\Boundary;
use App\Models\Process;
use App\Models\Scheme;
use App\Models\SustainabilityReport;
use App\Models\SustainabilityReportItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('swaps the detail section when a scheme card is clicked', function () {
    $scheme = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $otherScheme = Scheme::factory()->create(['description' => 'Tenun ATBM Pisano', 'sort_order' => 2]);
    $first = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 1, 'description' => 'Boundary Kain Gedog']);
    $second = Boundary::factory()->create(['scheme_id' => $scheme->id, 'sort_order' => 2, 'description' => 'Boundary Batik Gedog']);
    $third = Boundary::factory()->create(['scheme_id' => $otherScheme->id, 'sort_order' => 1, 'description' => 'Boundary Kain Pisano']);
    $firstProc = Process::factory()->create(['boundary_id' => $first->id]);
    $activity = Activity::factory()->create(['process_id' => $firstProc->id]);

    $report = SustainabilityReport::query()->create([
        'scheme_id' => $scheme->id,
        'boundary_id' => $second->id,
        'title' => 'Laporan Skema',
        'slug' => 'laporan-skema',
        'total_emission' => 10,
        'is_published' => false,
    ]);

    SustainabilityReportItem::query()->create([
        'sustainability_report_id' => $report->id,
        'activity_id' => $activity->id,
        'option_id' => null,
        'quantity' => 4,
        'emission_result' => 10,
    ]);

    User::factory()->create(['email' => 'card@example.com', 'password' => bcrypt('password')]);

    $page = visit('/admin/login')
        ->fill('email', 'card@example.com')
        ->fill('password', 'password')
        ->click('text=Masuk')
        ->assertPathIs('/admin');

    $page = visit(route('admin.reports.show', $report));

    // Both cards render; first scheme detail visible, other hidden.
    $page->assertSee('Gedog Tuban')
        ->assertSee('Tenun ATBM Pisano')
        ->assertVisible('#skema-'.$scheme->id.'-detail')
        ->assertMissing('#skema-'.$otherScheme->id.'-detail')
        ->assertNoJavaScriptErrors();

    // Clicking the other card swaps the visible detail.
    $page->click('[data-scheme-card="'.$otherScheme->id.'"]')
        ->assertVisible('#skema-'.$otherScheme->id.'-detail')
        ->assertMissing('#skema-'.$scheme->id.'-detail')
        ->assertSee('Boundary Kain Pisano')
        ->assertSee('Belum ada isian pada proses ini')
        ->assertNoJavaScriptErrors();
});
