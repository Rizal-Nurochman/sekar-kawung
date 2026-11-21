<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OldHomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/old', OldHomeController::class)->name('oldHome');
Route::redirect('/v2', '/');

Route::view('/calculator', 'landing.coming-soon', ['section' => 'Public Calculator'])->name('calculator');
Route::view('/methodology', 'landing.coming-soon', ['section' => 'Methodology'])->name('methodology');
Route::view('/about', 'landing.coming-soon', ['section' => 'About'])->name('about');

Route::get('/health', function () {
    return response()->json([
        'status' => 'OP',
        'message' => 'Laravel is OP',
    ]);
});
