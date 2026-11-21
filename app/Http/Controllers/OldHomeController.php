<?php

namespace App\Http\Controllers;

use App\Support\EmissionFactors;
use App\Support\ProductCatalog;
use Illuminate\Http\Response;

class OldHomeController extends Controller
{
    public function __invoke(): Response
    {
        return response()
            ->view('old.home', [
                'products' => ProductCatalog::all(),
                'categories' => ProductCatalog::categories(),
                'materials' => EmissionFactors::materials(),
                'energySources' => EmissionFactors::energySources(),
                'calculatorDefaults' => EmissionFactors::defaults(),
            ])
            ->header('Cache-Control', 'public, max-age=0, s-maxage=3600, stale-while-revalidate=300');
    }
}
