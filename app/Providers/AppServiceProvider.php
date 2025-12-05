<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FinancialFormulaEvaluator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FinancialFormulaEvaluator::class, function ($app) {
            //default max recursion depth: 50
            return new FinancialFormulaEvaluator(50);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
