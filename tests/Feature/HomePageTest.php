<?php

it('uses Alpine for the mobile navigation', function () {
    $this->get('/')->assertOk()
        ->assertSee('x-data="navigation"', false)
        ->assertSee('x-on:click="toggleNav()"', false)
        ->assertSee('x-bind:aria-expanded="navOpen"', false);
});

it('server-renders catalog products and calculator options', function () {
    $this->get('/')->assertOk()
        ->assertSee('Tenun Intipiyan')
        ->assertSee('Semua Jenis')
        ->assertSee('Katun Daur Ulang (~3.8 kg CO2e/kg)')
        ->assertSee('Tenun Seser Putih (~4.0 kg CO2e/kg; data prototipe)');
});

it('initializes the Alpine catalog, modal, and calculator', function () {
    $this->get('/')->assertOk()
        ->assertSee('x-data="catalog(', false)
        ->assertSee('x-data="productModal"', false)
        ->assertSee('x-data="calculator(', false);
});

it('passes catalog data to the home view', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertViewHas('products')
        ->assertViewHas('categories')
        ->assertViewHas('materials')
        ->assertViewHas('energySources')
        ->assertViewHas('calculatorDefaults');
});
