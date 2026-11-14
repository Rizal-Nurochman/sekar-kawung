<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/health', function () {
    return response()->json([
        'status' => 'OP',
        'message' => 'Laravel is OP'
    ]);
});
