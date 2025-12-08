<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_formula_maps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('financial_line_item_id')
                ->constrained('financial_line_items')
                ->onDelete('cascade');

            $table->text('formula')->nullable();
                // e.g. "regular_loans + associates + micro_project + past_due - allowance_for_probable_losses"

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_formula_maps');
    }
};
