<?php

use App\Http\Controllers\FinancialController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});



Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('financial.show', [
            'year' => 2025,
            'month' => 1,
        ]);
    })->name('dashboard');

    Route::get('/financial/{year}/{month}', [FinancialController::class, 'show'])->name('financial.show');
    Route::get('/financial/{year}/{month}/print', [FinancialController::class, 'print'])->name('financial.print');
    Route::post('/financial/{year}/{month}', [FinancialController::class, 'update'])->name('financial.update');

    Route::post('/financial/recalc', [FinancialController::class, 'recalc']);
    Route::post('/financial/save', [FinancialController::class, 'save'])->name('financial.save');
    Route::post('/financial/rollover', [FinancialController::class, 'rollover']);
});


require __DIR__.'/auth.php';
