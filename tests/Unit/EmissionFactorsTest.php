<?php

use App\Support\EmissionFactors;

it('provides material and energy options with numeric values', function () {
    foreach ([EmissionFactors::materials(), EmissionFactors::energySources()] as $options) {
        expect($options)->not->toBeEmpty();

        foreach ($options as $option) {
            expect($option)->toHaveKeys(['value', 'label']);
            expect($option['value'])->toBeNumeric();
        }
    }
});

it('calculator defaults point to real options', function () {
    $defaults = EmissionFactors::defaults();
    $materialValues = array_column(EmissionFactors::materials(), 'value');
    $energyValues = array_column(EmissionFactors::energySources(), 'value');

    expect($defaults)->toHaveKeys(['material', 'energy', 'quantity', 'distance']);
    expect($materialValues)->toContain($defaults['material']);
    expect($energyValues)->toContain($defaults['energy']);
    expect($defaults['quantity'])->toBeGreaterThan(0);
    expect($defaults['distance'])->toBeGreaterThanOrEqual(0);
});
