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
        Schema::create('td_farmer_planned', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_th_farmer_planned');
            $table->bigInteger('id_master_fertilizer');
            $table->decimal('quantity_planned', 20, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('td_farmer_planned');
    }
};
