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
        Schema::table('td_fertilizer_distribution', function (Blueprint $table) {
            $table->decimal('total_return', 20, 2)->after('total_loan')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('td_fertilizer_distribution', function (Blueprint $table) {
        });
    }
};
