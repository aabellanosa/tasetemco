<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('financial_formula_map');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
