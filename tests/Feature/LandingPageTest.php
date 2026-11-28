<?php

use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the new landing view with schemes and boundary cards', function () {
    $this->seed();

    $this->get('/')->assertOk()
        ->assertViewIs('landing')
        ->assertViewHas('schemes')
        ->assertViewHas('boundaryCards');
});

it('shows the hero copy, navigation, and calculator call to action', function () {
    $this->seed();

    $this->get('/')->assertOk()
        ->assertSee('Carbon Footprint Emissions Calculator')
        ->assertSee('Nurturing biodiversity through culture to achieve a sustainable economy.')
        ->assertSee('Start Carbon Calculator')
        ->assertSee('Mulai Kenali Jejak Karbon Anda.')
        ->assertSee('Mulai Menghitung');
});

it('does not leak old design components into the landing', function () {
    $this->seed();

    $this->get('/')->assertOk()
        ->assertDontSee('<x-old.', false)
        ->assertDontSee('x-data="catalog(', false)
        ->assertDontSee('x-data="productModal"', false);
});

it('renders four boundary cards from the database', function () {
    $this->seed();

    $response = $this->get('/')->assertOk();

    $cards = $response->viewData('boundaryCards');

    expect($cards)->toHaveCount(4);

    $this->get('/')->assertOk()
        ->assertSee('Production Boundaries')
        ->assertSee('From Seed to Tuban Gedog Fabric')
        ->assertSee('From Tuban Gedog Fabric to Tuban Gedog Batik Fabric')
        ->assertSee('From Seed to Pisano Handwoven Fabric')
        ->assertSee('From Pisano Handwoven Fabric to Pisano Handwoven Batik Fabric')
        ->assertSee('Proses Produksi dari Biji Sampai Jadi Kain Gedog Tuban')
        ->assertDontSee('From Seed to Tuban Gedog Batik Fabric')
        ->assertDontSee('From Seed to Pisano Handwoven Batik Fabric');
});

it('falls back to static cards when the database is empty', function () {
    $this->get('/')->assertOk()
        ->assertSee('From Seed to Tuban Gedog Fabric')
        ->assertSee('Data proses belum tersedia.');
});

it('exposes the fallback card definition', function () {
    expect(HomeController::boundaryCards())->toHaveCount(4);
});

it('serves the methodology page with calculation examples', function () {
    $this->get('/methodology')->assertOk()
        ->assertSee('Understanding Carbon Emissions and Product Footprints')
        ->assertSee('Electricity Use')
        ->assertSee('Research and References')
        ->assertDontSee('Segera Hadir');
});

it('shows each production boundary with its own scope on the about page', function () {
    $this->get('/about')->assertOk()
        ->assertSee('Four Production Boundaries')
        ->assertSee('From Seed to Fabric')
        ->assertSee('From Fabric to Batik Fabric')
        ->assertDontSee('From Seed to Finished Fabric');
});

it('serves the remaining separate routes', function () {
    $this->get('/calculator')->assertOk()->assertSee('Public Calculator');
    $this->get('/about')->assertOk()->assertSee('About');
});
