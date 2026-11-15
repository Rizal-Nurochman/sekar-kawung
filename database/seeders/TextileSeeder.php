<?php

namespace Database\Seeders;

use App\Enums\DataStatus;
use App\Models\CarbonFootprint;
use App\Models\Material;
use App\Models\Textile;
use Illuminate\Database\Seeder;

class TextileSeeder extends Seeder
{
    /**
     * The 13 prototype textiles, in catalog order. All are calculator-selectable.
     * Generic textiles have no material rows; the weaves attach Kapas Cokelat
     * and/or Kapas Putih. Blend shares stay null until real ratios exist.
     *
     * @var array<int, array{name: string, slug: string, kg_co2e_per_kg: float, materials: list<string>}>
     */
    private const array TEXTILES = [
        ['name' => 'Tenun Intipiyan', 'slug' => 'tenun-intipiyan', 'kg_co2e_per_kg' => 3.8, 'materials' => ['kapas-cokelat', 'kapas-putih']],
        ['name' => 'Tenun Jangan Menir', 'slug' => 'tenun-jangan-menir', 'kg_co2e_per_kg' => 4.2, 'materials' => ['kapas-cokelat', 'kapas-putih']],
        ['name' => 'Tenun Kembang Jati', 'slug' => 'tenun-kembang-jati', 'kg_co2e_per_kg' => 2.9, 'materials' => ['kapas-cokelat', 'kapas-putih']],
        ['name' => 'Tenun Polos Lowo', 'slug' => 'tenun-polos-lowo', 'kg_co2e_per_kg' => 3.5, 'materials' => ['kapas-cokelat']],
        ['name' => 'Tenun Seser Lowo', 'slug' => 'tenun-seser-lowo', 'kg_co2e_per_kg' => 2.1, 'materials' => ['kapas-cokelat']],
        ['name' => 'Tenun Seser Putih', 'slug' => 'tenun-seser-putih', 'kg_co2e_per_kg' => 4.0, 'materials' => ['kapas-putih']],
        ['name' => 'Katun Daur Ulang', 'slug' => 'katun-daur-ulang', 'kg_co2e_per_kg' => 3.8, 'materials' => []],
        ['name' => 'Katun Konvensional', 'slug' => 'katun-konvensional', 'kg_co2e_per_kg' => 8.5, 'materials' => []],
        ['name' => 'Polyester Daur Ulang', 'slug' => 'polyester-daur-ulang', 'kg_co2e_per_kg' => 4.2, 'materials' => []],
        ['name' => 'Polyester Virgin', 'slug' => 'polyester-virgin', 'kg_co2e_per_kg' => 9.8, 'materials' => []],
        ['name' => 'Linen Organik', 'slug' => 'linen-organik', 'kg_co2e_per_kg' => 2.9, 'materials' => []],
        ['name' => 'Tencel / Lyocell', 'slug' => 'tencel-lyocell', 'kg_co2e_per_kg' => 3.5, 'materials' => []],
        ['name' => 'Serat Hemp / Rami', 'slug' => 'serat-hemp-rami', 'kg_co2e_per_kg' => 2.1, 'materials' => []],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::TEXTILES as $sortOrder => $attributes) {
            $textile = Textile::query()->updateOrCreate(
                ['slug' => $attributes['slug']],
                [
                    'name' => $attributes['name'],
                    'is_calculable' => true,
                    'sort_order' => $sortOrder,
                ],
            );

            CarbonFootprint::query()->updateOrCreate(
                ['textile_id' => $textile->id],
                [
                    'kg_co2e_per_kg' => $attributes['kg_co2e_per_kg'],
                    'data_status' => DataStatus::Placeholder,
                ],
            );

            $textile->materials()->sync(
                Material::query()->whereIn('slug', $attributes['materials'])->pluck('id'),
            );
        }
    }
}
