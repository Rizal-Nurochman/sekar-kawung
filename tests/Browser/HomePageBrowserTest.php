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

it('defaults to the product tab and shows the product details', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Intipiyan"]')
        ->assertVisible('#product-modal')
        ->assertVisible('#tab-product')
        ->assertMissing('#tab-impact')
        ->assertSeeIn('#tab-product', 'Tentang material')
        ->assertSeeIn('#tab-product', 'Detail pasokan')
        ->assertSeeIn('#tab-product h3', 'Proses produksi');
});

it('switches to the environmental impact tab', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Intipiyan"]')
        ->assertVisible('#product-modal')
        ->click('#tab-btn-impact')
        ->assertVisible('#tab-impact')
        ->assertMissing('#tab-product')
        ->assertSeeIn('#tab-impact', 'Jejak karbon')
        ->assertSeeIn('#tab-impact', 'Sumber daya & energi')
        ->assertSeeIn('#tab-impact', 'Konsumsi air');
});

it('supports arrow-key navigation between the two tabs', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Intipiyan"]')
        ->assertVisible('#product-modal')
        ->keys('#tab-btn-product', 'ArrowRight')
        ->assertVisible('#tab-impact')
        ->assertMissing('#tab-product')
        ->keys('#tab-btn-impact', 'ArrowLeft')
        ->assertVisible('#tab-product')
        ->assertMissing('#tab-impact');
});

it('resets to the product tab when another product opens', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Intipiyan"]')
        ->assertVisible('#product-modal')
        ->click('#tab-btn-impact')
        ->assertVisible('#tab-impact')
        ->keys('#modal-card', 'Escape')
        ->assertMissing('#product-modal');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Polos Lowo"]')
        ->assertVisible('#product-modal')
        ->assertVisible('#tab-product')
        ->assertMissing('#tab-impact');
});

it('closes the product detail modal with Escape', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Intipiyan"]')
        ->assertVisible('#product-modal')
        ->keys('#modal-card', 'Escape')
        ->assertMissing('#product-modal');
});

it('sends the product emission to the calculator', function () {
    $page = visit('/');

    $page->click('#product-grid article[aria-label="Lihat detail Tenun Seser Lowo"]')
        ->assertVisible('#product-modal')
        ->click('#modal-calculator-button')
        ->assertMissing('#product-modal')
        ->assertValue('#calc-material', '2.1')
        ->assertSeeIn('#kalkulator', '3.325');
});
