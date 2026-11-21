<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OldHomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', OldHomeController::class)->name('oldHome');
Route::get('/v2', HomeController::class)->name('home');

Route::get('/health', function () {
    return response()->json([
        'status' => 'OP',
        'message' => 'Laravel is OP',
    ]);
});
