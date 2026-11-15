<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Carbon Calculator
    |--------------------------------------------------------------------------
    |
    | Constants for the emission calculator and derived footprint metrics.
    |
    */

    // Textile slug used as the savings baseline (Katun Konvensional, 8.5 kg CO2e/kg).
    'baseline_slug' => 'katun-konvensional',

    // Calculator defaults. The prototype starts at Katun Daur Ulang and grid electricity.
    'default_textile_slug' => 'katun-daur-ulang',
    'default_energy_source_slug' => 'grid',
    'default_quantity_kg' => 1000,
    'default_distance_km' => 250,

    // Manufacturing energy, kg CO2e per kg produced (before the energy source multiplier).
    'energy_base_kg_per_kg' => 1.2,

    // Logistics, kg CO2e per kg per km.
    'logistics_kg_per_kg_km' => 0.0001,

    // Industry benchmark, kg CO2e per kg.
    'industry_benchmark' => 10,

    'recommend' => [
        // Suggest solar when the energy source multiplier is above this value.
        'energy_multiplier_above' => 0.5,

        // Suggest recycled/organic material when the footprint is above this value.
        'material_ef_above' => 5.0,
    ],

];
