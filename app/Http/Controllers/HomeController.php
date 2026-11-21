<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * @return array<int, array{title: string, body: string}>
     */
    public static function processCards(): array
    {
        return [
            ['title' => 'From Seed to Tuban Gedog Fabric', 'body' => 'Data proses belum tersedia.'],
            ['title' => 'From Seed to Tuban Gedog Batik Fabric', 'body' => 'Data proses belum tersedia.'],
            ['title' => 'From Seed to Pisano Handwoven Fabric', 'body' => 'Data proses belum tersedia.'],
            ['title' => 'From Seed to Pisano Handwoven Batik Fabric', 'body' => 'Data proses belum tersedia.'],
        ];
    }

    public function __invoke(): Response
    {
        $schemes = Scheme::with(['processes' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $titles = [
            1 => 'From Seed to Tuban Gedog Fabric',
            2 => 'From Seed to Tuban Gedog Batik Fabric',
            3 => 'From Seed to Pisano Handwoven Fabric',
            4 => 'From Seed to Pisano Handwoven Batik Fabric',
        ];

        $cards = $schemes
            ->flatMap(fn (Scheme $scheme) => $scheme->processes)
            ->map(fn ($process) => [
                'title' => $titles[$process->id] ?? $process->description,
                'body' => $process->description,
            ])
            ->values()
            ->all();

        return response()
            ->view('landing', [
                'schemes' => $schemes,
                'processCards' => $cards !== [] ? $cards : self::processCards(),
            ])
            ->header('Cache-Control', 'public, max-age=0, s-maxage=3600, stale-while-revalidate=300');
    }
}
