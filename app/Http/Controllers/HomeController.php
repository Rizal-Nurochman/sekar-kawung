<?php

namespace App\Http\Controllers;

use App\Support\EmissionFactors;
use App\Support\ProductCatalog;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'products' => ProductCatalog::all(),
            'categories' => ProductCatalog::categories(),
            'materials' => EmissionFactors::materials(),
            'energySources' => EmissionFactors::energySources(),
            'calculatorDefaults' => EmissionFactors::defaults(),
        ]);
    }
}
