<?php

use App\Http\Controllers\FinancialController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/financial/{year}/{month}', [FinancialController::class, 'edit'])->name('financial.edit');
Route::post('/financial/{year}/{month}', [FinancialController::class, 'update'])->name('financial.update');

