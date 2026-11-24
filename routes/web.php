<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CalculatorController as AdminCalculatorController;
use App\Http\Controllers\Admin\Config\ActivityController;
use App\Http\Controllers\Admin\Config\ActivityOptionController;
use App\Http\Controllers\Admin\Config\ActivityUnitController;
use App\Http\Controllers\Admin\Config\ConfigController;
use App\Http\Controllers\Admin\Config\EmissionFactorController;
use App\Http\Controllers\Admin\Config\OptionController;
use App\Http\Controllers\Admin\Config\ProcessController;
use App\Http\Controllers\Admin\Config\ProductController;
use App\Http\Controllers\Admin\Config\SchemeController;
use App\Http\Controllers\Admin\Config\SourceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PublishedReportController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OldHomeController;
use App\Http\Controllers\Public\CalculatorController as PublicCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/old', OldHomeController::class)->name('oldHome');
Route::redirect('/v2', '/');

Route::get('/calculator', [PublicCalculatorController::class, 'edit'])->name('calculator');
Route::post('/calculator/mulai', [PublicCalculatorController::class, 'start'])->name('calculator.start');
Route::post('/calculator/langkah', [PublicCalculatorController::class, 'step'])->name('calculator.step');
Route::post('/calculator/ulasan/kembali', [PublicCalculatorController::class, 'backFromReview'])->name('calculator.backFromReview');
Route::post('/calculator/ulang', [PublicCalculatorController::class, 'reset'])->name('calculator.reset');
Route::view('/methodology', 'landing.methodology')->name('methodology');
Route::view('/about', 'landing.about')->name('about');

Route::get('/health', function () {
    return response()->json([
        'status' => 'OP',
        'message' => 'Laravel is OP',
    ]);
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    });

    Route::middleware('auth')->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/kalkulator', [AdminCalculatorController::class, 'edit'])->name('calculator.edit');
        Route::post('/kalkulator/mulai', [AdminCalculatorController::class, 'start'])->name('calculator.start');
        Route::post('/kalkulator/langkah', [AdminCalculatorController::class, 'step'])->name('calculator.step');
        Route::post('/kalkulator/ulasan/kembali', [AdminCalculatorController::class, 'backFromReview'])->name('calculator.backFromReview');
        Route::post('/kalkulator/ulang', [AdminCalculatorController::class, 'reset'])->name('calculator.reset');
        Route::post('/kalkulator', [AdminCalculatorController::class, 'store'])->name('calculator.store');
        Route::resource('reports', ReportController::class)->except(['create', 'store']);
        Route::post('reports/{report}/publish', [PublishedReportController::class, 'store'])->name('reports.publish');
        Route::delete('reports/{report}/publish', [PublishedReportController::class, 'destroy'])->name('reports.unpublish');
        Route::prefix('config')->name('config.')->group(function (): void {
            Route::get('/', [ConfigController::class, 'index'])->name('index');
            Route::resource('schemes', SchemeController::class)->except(['show']);
            Route::resource('products', ProductController::class)->except(['show']);
            Route::resource('processes', ProcessController::class)->except(['show']);
            Route::resource('activities', ActivityController::class)->except(['show']);
            Route::resource('activity-units', ActivityUnitController::class)->except(['show'])->parameters(['activity-units' => 'activityUnit']);
            Route::resource('sources', SourceController::class)->except(['show']);
            Route::resource('emission-factors', EmissionFactorController::class)->except(['show'])->parameters(['emission-factors' => 'emissionFactor']);
            Route::resource('options', OptionController::class)->except(['show']);
            Route::get('activities/{activity}/options/edit', [ActivityOptionController::class, 'edit'])->name('activity-options.edit');
            Route::put('activities/{activity}/options', [ActivityOptionController::class, 'update'])->name('activity-options.update');
        });
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
