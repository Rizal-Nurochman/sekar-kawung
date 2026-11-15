<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Product;
use App\Models\Textile;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * The six sellable weaves. Known values only: certification, compliance,
     * MOQ, chemical info, production stages, scores, water, and energy notes
     * stay null until real data exists ("Belum tersedia" is UI copy, not data).
     *
     * @var array<int, array{slug: string, description: string, image_path: string}>
     */
    private const array PRODUCTS = [
        ['slug' => 'tenun-intipiyan', 'description' => 'Pola garis berulang membentuk susunan geometris dengan perpaduan warna cokelat dan putih. Terbuat dari kapas cokelat (Gossypium barbadense L.) dan kapas putih (Gossypium hirsutum L.).', 'image_path' => 'images/products/tenun-intipiyan.webp'],
        ['slug' => 'tenun-jangan-menir', 'description' => 'Perpaduan garis halus dan bidang bertekstur dengan aksen garis melintang pada bagian kain. Terbuat dari kapas cokelat (Gossypium barbadense L.) dan kapas putih (Gossypium hirsutum L.).', 'image_path' => 'images/products/tenun-jangan-menir.webp'],
        ['slug' => 'tenun-kembang-jati', 'description' => 'Motif kecil berulang tersusun dalam barisan teratur dengan kontras putih di atas dasar cokelat. Terbuat dari kapas cokelat (Gossypium barbadense L.) dan kapas putih (Gossypium hirsutum L.).', 'image_path' => 'images/products/tenun-kembang-jati.webp'],
        ['slug' => 'tenun-polos-lowo', 'description' => 'Tenun polos berwarna cokelat dengan tekstur benang yang tampak alami dan tidak seragam. Terbuat dari kapas cokelat (Gossypium barbadense L.).', 'image_path' => 'images/products/tenun-polos-lowo.webp'],
        ['slug' => 'tenun-seser-lowo', 'description' => 'Tenun bernuansa cokelat dengan garis putih halus yang membentuk tekstur berirama. Terbuat dari kapas cokelat (Gossypium barbadense L.).', 'image_path' => 'images/products/tenun-seser-lowo.webp'],
        ['slug' => 'tenun-seser-putih', 'description' => 'Tenun berwarna putih dengan susunan benang dan garis halus yang memperlihatkan tekstur kain. Terbuat dari kapas putih (Gossypium hirsutum L.).', 'image_path' => 'images/products/tenun-seser-putih.webp'],
    ];

    private const array ADDRESS = [
        'label' => 'Desa Kerek, Tuban',
        'village' => 'Kerek',
        'regency' => 'Tuban',
        'province' => 'Jawa Timur',
        'country' => 'Indonesia',
        'is_primary' => true,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::PRODUCTS as $sortOrder => $attributes) {
            $textile = Textile::query()->where('slug', $attributes['slug'])->first();

            if ($textile === null) {
                throw new \RuntimeException("Textile [{$attributes['slug']}] not found; run TextileSeeder first.");
            }

            $product = Product::query()->updateOrCreate(
                ['textile_id' => $textile->id],
                [
                    'description' => $attributes['description'],
                    'image_path' => $attributes['image_path'],
                    'sort_order' => $sortOrder,
                    'is_published' => true,
                ],
            );

            Address::query()->updateOrCreate(
                ['product_id' => $product->id, 'label' => self::ADDRESS['label']],
                self::ADDRESS,
            );
        }
    }
}
