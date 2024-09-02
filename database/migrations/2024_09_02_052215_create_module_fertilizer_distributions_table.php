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
        Schema::create('module_fertilizer_distributions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_master_farmer_borrower'); // Id Peminjam
            $table->bigInteger('id_master_farmer_borrowed'); // Id yang dipinjam
            $table->decimal('fertilizer_quantity_borrowed', 8, 2); // Total Dipinjam
            $table->decimal('fertilizer_quantity_returned', 8, 2); // Total Dikembalikan
            $table->integer('periode');
            $table->tinyInteger('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_fertilizer_distributions');
    }
};
