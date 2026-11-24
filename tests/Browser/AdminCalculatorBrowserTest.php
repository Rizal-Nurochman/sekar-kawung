<?php

use App\Models\Activity;
use App\Models\EmissionFactor;
use App\Models\Option;
use App\Models\Process;
use App\Models\Product;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedWizardTwoSteps(): array
{
    $scheme = Scheme::factory()->create(['description' => 'Gedog Tuban', 'sort_order' => 1]);
    $first = Product::factory()->create([
        'scheme_id' => $scheme->id,
        'sort_order' => 1,
        'description' => 'Produk Produksi dari Biji Sampai Jadi Kain',
    ]);
    $second = Product::factory()->create([
        'scheme_id' => $scheme->id,
        'sort_order' => 2,
        'description' => 'Produk Produksi dari Biji Sampai Jadi Kain Batik',
    ]);

    $firstProc = Process::factory()->create(['product_id' => $first->id, 'description' => 'Mengolah Tanah']);
    $secondProc = Process::factory()->create(['product_id' => $second->id, 'description' => 'Membatik Tulis']);

    $factor = EmissionFactor::factory()->create(['ef_value' => 2.5]);
    $standalone = Activity::factory()->create([
        'process_id' => $firstProc->id,
        'emission_factor_id' => $factor->id,
        'description' => 'Pupuk organik mandiri',
    ]);

    $optionFactor = EmissionFactor::factory()->create(['ef_value' => 3.0]);
    $option = Option::factory()->create([
        'emission_factor_id' => $optionFactor->id,
        'description' => 'Sepeda Motor',
    ]);
    $transport = Activity::factory()->withoutEmissionFactor()->create([
        'process_id' => $secondProc->id,
        'description' => 'Transport ke Temanggung',
    ]);
    $transport->options()->attach($option->id);

    return [$scheme, $first, $second, $standalone, $transport, $option];
}

function loginAsAdmin(string $email = 'admin@example.com'): void
{
    User::factory()->create([
        'email' => $email,
        'password' => bcrypt('password'),
    ]);
}

it('walks the full wizard from start to published report', function () {
    seedWizardTwoSteps();
    loginAsAdmin();

    $page = visit('/admin/login');

    $page->fill('email', 'admin@example.com')
        ->fill('password', 'password')
        ->click('text=Masuk')
        ->assertPathIs('/admin')
        ->assertNoJavaScriptErrors();

    $page = visit('/admin/kalkulator');

    // Start state: single centered action, no identity fields.
    $page->assertSee('Mulai Menghitung')
        ->assertVisible('#start-calculator')
        ->assertMissing('Judul laporan')
        ->assertMissing('Simpan & terbitkan')
        ->assertNoJavaScriptErrors();

    $page->click('#start-calculator')
        ->assertSee('Produk 1 dari 2')
        ->assertSee('Gedog Tuban')
        ->assertSee('Total sementara')
        ->assertMissing('Simpan & terbitkan')
        ->assertNoJavaScriptErrors();

    // Type the quantity with real keystrokes so Alpine input events fire.
    $page->click('input[data-quantity-input]')
        ->keys('input[data-quantity-input]', '4')
        ->assertSeeIn('#total-sementara', '10')
        ->click('#wizard-next')
        ->assertSee('Produk 2 dari 2')
        ->assertSee('Membatik Tulis')
        ->assertNoJavaScriptErrors();

    // Refresh resumes the same step (session persistence).
    $page = visit('/admin/kalkulator');
    $page->assertSee('Produk 2 dari 2')
        ->assertNoJavaScriptErrors();

    // Second step has an option dropdown; pick it and fill quantity.
    $page->select('select[data-option-select]', 'Sepeda Motor')
        ->click('input[data-quantity-input]')
        ->keys('input[data-quantity-input]', '2')
        ->assertSeeIn('#total-sementara', '16')
        ->click('#wizard-next')
        ->assertSee('Periksa kembali sebelum menerbitkan')
        ->assertSee('Produk 1 dari 2')
        ->assertSee('Produk 2 dari 2')
        ->assertSee('Rincian per tahap')
        ->assertSee('Rincian aktivitas')
        ->assertVisible('#wizard-save')
        ->assertNoJavaScriptErrors();

    $page->click('#wizard-save')
        ->assertSee('Laporan keberlanjutan berhasil disimpan.')
        ->assertNoJavaScriptErrors();
});

it('keeps the temporary total visible without a save action on steps', function () {
    seedWizardTwoSteps();
    loginAsAdmin('step@example.com');

    $page = visit('/admin/login');

    $page->fill('email', 'step@example.com')
        ->fill('password', 'password')
        ->click('text=Masuk')
        ->assertPathIs('/admin');

    $page = visit('/admin/kalkulator')
        ->click('#start-calculator')
        ->assertSee('Produk 1 dari 2')
        ->assertVisible('#total-sementara')
        ->assertMissing('Simpan & terbitkan')
        ->click('input[data-quantity-input]')
        ->keys('input[data-quantity-input]', '2')
        ->assertSeeIn('#total-sementara', '5')
        ->assertNoJavaScriptErrors();
});
