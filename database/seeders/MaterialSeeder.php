<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Raw fibers from the prototype.
     */
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Kapas Putih',
                'slug' => 'kapas-putih',
                'scientific_name' => 'Gossypium hirsutum L.',
                'description' => 'Untuk tenun warna putih.',
            ],
            [
                'name' => 'Kapas Cokelat',
                'slug' => 'kapas-cokelat',
                'scientific_name' => 'Gossypium barbadense L.',
                'description' => 'Untuk tenun warna cokelat.',
            ],
        ];

        foreach ($materials as $material) {
            Material::query()->updateOrCreate(
                ['slug' => $material['slug']],
                $material,
            );
        }
    }
}
