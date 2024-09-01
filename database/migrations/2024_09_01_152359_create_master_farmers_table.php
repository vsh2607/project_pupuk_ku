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
        Schema::create('master_farmers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('land_type'); // Owned or Leased
            $table->string('handphone_number');
            $table->string('land_area');
            $table->string('land_location')->nullable();
            $table->decimal('fertilizer_quantity_owned', 8, 2)->nullable();
            $table->decimal('fertilizer_quantity_needed', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_farmers');
    }
};
