<?php

use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the new landing view with schemes and process cards', function () {
    $this->seed();

    $this->get('/')->assertOk()
        ->assertViewIs('landing')
        ->assertViewHas('schemes')
        ->assertViewHas('processCards');
});

it('shows the hero copy, navigation, and calculator call to action', function () {
    $this->seed();

    $this->get('/')->assertOk()
        ->assertSee('Merawat biodiversitas melalui budaya untuk mencapai ekonomi lestari.')
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

it('renders four process cards from the database', function () {
    $this->seed();

    $response = $this->get('/')->assertOk();

    $cards = $response->viewData('processCards');

    expect($cards)->toHaveCount(4);

    $this->get('/')->assertOk()
        ->assertSee('From Seed to Tuban Gedog Fabric')
        ->assertSee('From Seed to Tuban Gedog Batik Fabric')
        ->assertSee('From Seed to Pisano Handwoven Fabric')
        ->assertSee('From Seed to Pisano Handwoven Batik Fabric')
        ->assertSee('Proses Produksi dari Biji Sampai Jadi Kain Gedog Tuban');
});

it('falls back to static cards when the database is empty', function () {
    $this->get('/')->assertOk()
        ->assertSee('From Seed to Tuban Gedog Fabric')
        ->assertSee('Data proses belum tersedia.');
});

it('exposes the fallback card definition', function () {
    expect(HomeController::processCards())->toHaveCount(4);
});

it('serves the methodology page with calculation examples', function () {
    $this->get('/methodology')->assertOk()
        ->assertSee('Understanding Carbon Emissions and Product Footprints')
        ->assertSee('Electricity Use')
        ->assertSee('Research and References')
        ->assertDontSee('Segera Hadir');
});

it('serves the remaining separate routes', function () {
    $this->get('/calculator')->assertOk()->assertSee('Public Calculator');
    $this->get('/about')->assertOk()->assertSee('About');
});
