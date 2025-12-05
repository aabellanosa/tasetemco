<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_line_items', function (Blueprint $table) {
            $table->id();

            // Unique normalized code (e.g., "cash_on_hand")
            $table->string('code')->unique();

            // Client-facing label (e.g., "Cash on Hand")
            $table->string('label');

            // Top-level grouping: assets, liabilities, equity
            $table->string('section');

            // Sub-sections: current_assets, non_current_assets, etc
            $table->string('sub_section')->nullable();

            // Whether user can edit this value
            $table->boolean('is_editable')->default(true);

            // Controls ordering inside the UI
            $table->integer('display_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_line_items');
    }
};
