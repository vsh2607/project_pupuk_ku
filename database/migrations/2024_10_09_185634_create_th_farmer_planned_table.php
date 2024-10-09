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
        Schema::create('th_farmer_planned', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_master_farmer');
            $table->date('planned_date');
            $table->string('land_area');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('th_farmer_planned');
    }
};
