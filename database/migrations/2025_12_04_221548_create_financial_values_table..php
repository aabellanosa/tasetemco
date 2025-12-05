<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_values', function (Blueprint $table) {
            $table->id();

            // FK to line items
            $table->foreignId('line_item_id')
                ->constrained('financial_line_items')
                ->cascadeOnDelete();

            // Example: year = 2025, month = 1..12
            $table->integer('year')->default(2025);
            $table->integer('month'); // 1 = Jan ... 12 = Dec

            // Editable numeric value (derived items won't be stored)
            $table->decimal('value', 15, 2)->default(0);

            $table->timestamps();

            // Speed optimizations
            $table->index(['line_item_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_values');
    }
};
