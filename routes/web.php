<?php

use App\Http\Controllers\FinancialController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return redirect()->route('financial.show', [
        'year' => 2025,
        'month' => 1
    ]);
});

Route::get('/financial/{year}/{month}', [FinancialController::class, 'show'])->name('financial.show');
Route::post('/financial/{year}/{month}', [FinancialController::class, 'update'])->name('financial.update');

Route::post('/financial/recalc', [FinancialController::class, 'recalc']);
Route::post('/financial/save', [FinancialController::class, 'save'])->name('financial.save');
Route::post('/financial/rollover', [FinancialController::class, 'rollover']);
