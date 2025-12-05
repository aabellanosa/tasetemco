<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_formula_map', function (Blueprint $table) {
            $table->id();

            // The derived line item this formula belongs to
            $table->foreignId('line_item_id')
                ->constrained('financial_line_items')
                ->cascadeOnDelete();

            // Formula string using item codes: "loan1 + loan2 - allowance"
            $table->text('formula');

            $table->timestamps();

            // Unique: one formula per derived line-item
            $table->unique('line_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_formula_map');
    }
};
