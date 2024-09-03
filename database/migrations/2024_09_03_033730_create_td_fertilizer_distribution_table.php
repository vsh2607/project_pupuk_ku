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
        Schema::create('td_fertilizer_distribution', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_th_fertilizer_distribution');
            $table->bigInteger('id_farmer_borrower');
            $table->bigInteger('id_farmer_lender');
            $table->decimal('total_loan', 8, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('td_fertilizer_distribution');
    }
};
