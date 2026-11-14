<?php

it('filters the catalog by material category', function () {
    $page = visit('/');

    $page->assertSee('Koleksi Material Tekstil')
        ->assertVisible('#product-grid article:has-text("Tenun Intipiyan")');

    $page->click('#filter-container button:has-text("Kapas Cokelat")')
        ->assertVisible('#product-grid article:has-text("Tenun Polos Lowo")')
        ->assertVisible('#product-grid article:has-text("Tenun Seser Lowo")')
        ->assertMissing('#product-grid article:has-text("Tenun Intipiyan")')
        ->assertMissing('#product-grid article:has-text("Tenun Seser Putih")');
});

it('updates the emission total when the production quantity changes', function () {
    $page = visit('/');

    $page->assertSee('5.025')
        ->fill('#calc-quantity', '2000')
        ->assertSee('10.050');
});

it('updates the material emission when the material changes', function () {
    $page = visit('/');

    $page->select('#calc-material', '9.8')
        ->assertSee('9.800')
        ->assertSee('11.025');
});

it('opens the product detail modal from a catalog card', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Intipiyan"]')
        ->assertVisible('#product-modal')
        ->assertSeeIn('#modal-title', 'Tenun Intipiyan');
});
