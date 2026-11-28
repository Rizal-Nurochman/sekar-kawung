<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * @return array<int, array{title: string, body: string}>
     */
    public static function boundaryCards(): array
    {
        return [
            ['title' => 'Dari Biji Kapas hingga Kain Gedog Tuban', 'body' => 'Data proses belum tersedia.'],
            ['title' => 'Dari Kain Gedog Tuban hingga Kain Batik Gedog Tuban', 'body' => 'Data proses belum tersedia.'],
            ['title' => 'Dari Biji Kapas hingga Kain Tenun Pisano', 'body' => 'Data proses belum tersedia.'],
            ['title' => 'Dari Kain Tenun Pisano hingga Kain Batik Tenun Pisano', 'body' => 'Data proses belum tersedia.'],
        ];
    }

    public function __invoke(): Response
    {
        $schemes = Scheme::with(['boundaries' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $titles = [
            1 => 'Dari Biji Kapas hingga Kain Gedog Tuban',
            2 => 'Dari Kain Gedog Tuban hingga Kain Batik Gedog Tuban',
            3 => 'Dari Biji Kapas hingga Kain Tenun Pisano',
            4 => 'Dari Kain Tenun Pisano hingga Kain Batik Tenun Pisano',
        ];

        $cards = $schemes
            ->flatMap(fn (Scheme $scheme) => $scheme->boundaries)
            ->map(fn ($boundary) => [
                'title' => $titles[$boundary->id] ?? $boundary->description,
                'body' => $boundary->description,
            ])
            ->values()
            ->all();

        return response()
            ->view('landing', [
                'schemes' => $schemes,
                'boundaryCards' => $cards !== [] ? $cards : self::boundaryCards(),
            ])
            ->header('Cache-Control', 'public, max-age=0, s-maxage=3600, stale-while-revalidate=300');
    }
}
