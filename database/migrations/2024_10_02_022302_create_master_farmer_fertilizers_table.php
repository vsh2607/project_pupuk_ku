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
        Schema::create('master_farmer_fertilizers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_master_farmer');
            $table->bigInteger('id_master_fertilizer');
            $table->decimal('quantity_owned', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_farmer_fertilizers');
    }
};
