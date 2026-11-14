<?php

it('forwards Alpine bindings to the select', function () {
    $this->blade('<x-form.select id="calc-material" label="Material" x-model.number="material" x-ref="materialSelect"><option value="3.8">Material</option></x-form.select>')
        ->assertSee('x-model.number="material"', false)
        ->assertSee('x-ref="materialSelect"', false);
});

it('forwards Alpine bindings and the unit suffix to the input', function () {
    $this->blade('<x-form.input id="calc-quantity" type="number" min="1" label="Jumlah" hint="Kg" unit="Kg" x-model.number="quantity" />')
        ->assertSee('x-model.number="quantity"', false)
        ->assertSee('Kg</span>', false)
        ->assertSee('min="1"', false);
});
