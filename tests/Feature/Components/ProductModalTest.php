<?php

it('renders an accessible product dialog with two tabbed sections', function () {
    $this->blade('<x-old.product.modal />')
        ->assertSee('role="dialog"', false)
        ->assertSee('aria-modal="true"', false)
        ->assertSee('aria-labelledby="modal-title"', false)
        ->assertSee('aria-describedby="modal-description"', false)
        ->assertSee('role="tablist"', false)
        ->assertSee('Tentang produk')
        ->assertSee('Dampak lingkungan')
        ->assertSee('aria-controls="tab-product"', false)
        ->assertSee('aria-controls="tab-impact"', false)
        ->assertSee('id="tab-product"', false)
        ->assertSee('id="tab-impact"', false)
        ->assertSee('Tutup detail produk')
        ->assertSee('Hitung di kalkulator');
});
