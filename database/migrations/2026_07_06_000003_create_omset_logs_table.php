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
        Schema::create('omset_logs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('nominal_omset', 15, 2); // A
            $table->decimal('alokasi_gaji', 15, 2); // B (70% A)
            $table->decimal('alokasi_perusahaan', 15, 2); // C (30% A)
            $table->decimal('gaji_pokok_pool', 15, 2); // D (70% B)
            $table->decimal('tukin_pool', 15, 2); // E (30% B)
            $table->foreignId('sales_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('omset_logs');
    }
};
