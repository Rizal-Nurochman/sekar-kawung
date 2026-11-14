<?php

namespace App\Support;

class EmissionFactors
{
    /**
     * @return array<int, array{value: float, label: string}>
     */
    public static function materials(): array
    {
        return [
            ['value' => 3.8, 'label' => 'Katun Daur Ulang (~3.8 kg CO2e/kg)'],
            ['value' => 8.5, 'label' => 'Katun Konvensional (~8.5 kg CO2e/kg)'],
            ['value' => 4.2, 'label' => 'Polyester Daur Ulang (~4.2 kg CO2e/kg)'],
            ['value' => 9.8, 'label' => 'Polyester Virgin (~9.8 kg CO2e/kg)'],
            ['value' => 2.9, 'label' => 'Linen Organik (~2.9 kg CO2e/kg)'],
            ['value' => 3.5, 'label' => 'Tencel / Lyocell (~3.5 kg CO2e/kg)'],
            ['value' => 2.1, 'label' => 'Serat Hemp / Rami (~2.1 kg CO2e/kg)'],
            ['value' => 4.0, 'label' => 'Tenun Seser Putih (~4.0 kg CO2e/kg; data prototipe)'],
        ];
    }

    /**
     * @return array<int, array{value: float, label: string}>
     */
    public static function energySources(): array
    {
        return [
            ['value' => 1.0, 'label' => 'Grid Listrik Standar (Faktor 1.0x)'],
            ['value' => 0.25, 'label' => 'Energi Terbarukan / Panel Surya (Faktor 0.25x)'],
            ['value' => 1.4, 'label' => 'Campuran Batubara & Listrik (Faktor 1.4x)'],
        ];
    }

    /**
     * @return array{material: float, energy: float, quantity: int, distance: int}
     */
    public static function defaults(): array
    {
        return [
            'material' => 3.8,
            'energy' => 1.0,
            'quantity' => 1000,
            'distance' => 250,
        ];
    }
}
