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

            $table->string('code')->unique();  
                // e.g. "cash_on_hand", "loan_associates"

            $table->string('label');            
                // UI text label (what appears on column 1)

            $table->integer('display_order');   
                // exact row order in PDF

            $table->unsignedTinyInteger('pdf_column')
                ->default(4);
                // 2,3,4 = where editable or derived value sits
                // 1 = title/subtitle/header (no numbers)

            $table->boolean('is_editable')
                ->default(true);
                // false = derived totals/subtotals

            $table->unsignedTinyInteger('indent_level')
                ->default(0);
                // 0 = main, 1 = subsection, 2 = sub-item

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_line_items');
    }
};
