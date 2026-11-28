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
        ->assertSee('Kalkulator Emisi Jejak Karbon')
        ->assertSee('Merawat biodiversitas melalui budaya untuk mencapai ekonomi lestari.')
        ->assertSee('Mulai Kalkulator Karbon')
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
        ->assertSee('Boundary Produksi')
        ->assertSee('Dari Biji Kapas hingga Kain Gedog Tuban')
        ->assertSee('Dari Kain Gedog Tuban hingga Kain Batik Gedog Tuban')
        ->assertSee('Dari Biji Kapas hingga Kain Tenun Pisano')
        ->assertSee('Dari Kain Tenun Pisano hingga Kain Batik Tenun Pisano')
        ->assertSee('Proses Produksi dari Biji Sampai Jadi Kain Gedog Tuban')
        ->assertDontSee('From Seed to Tuban Gedog Batik Fabric')
        ->assertDontSee('From Seed to Pisano Handwoven Batik Fabric');
});

it('falls back to static cards when the database is empty', function () {
    $this->get('/')->assertOk()
        ->assertSee('Dari Biji Kapas hingga Kain Gedog Tuban')
        ->assertSee('Data proses belum tersedia.');
});

it('exposes the fallback card definition', function () {
    expect(HomeController::boundaryCards())->toHaveCount(4);
});

it('serves the methodology page with calculation examples', function () {
    $this->get('/methodology')->assertOk()
        ->assertSee('Memahami Emisi Karbon dan Jejak Produk')
        ->assertSee('Penggunaan Listrik')
        ->assertSee('Riset dan Referensi')
        ->assertDontSee('Segera Hadir');
});

it('shows each production boundary with its own scope on the about page', function () {
    $this->get('/about')->assertOk()
        ->assertSee('Empat Boundary Produksi')
        ->assertSee('Dari Biji Kapas hingga Kain')
        ->assertSee('Dari Kain hingga Kain Batik')
        ->assertDontSee('From Seed to Finished Fabric');
});

it('serves the remaining separate routes', function () {
    $this->get('/calculator')->assertOk()->assertSee('Kalkulator Publik');
    $this->get('/about')->assertOk()->assertSee('Tentang');
});
