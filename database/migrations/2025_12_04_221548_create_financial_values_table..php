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

            $table->foreignId('financial_line_item_id')
                ->constrained('financial_line_items')
                ->onDelete('cascade');

            $table->integer('year');     
            $table->integer('month');    

            $table->decimal('value', 15, 2)
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_values');
    }
};
