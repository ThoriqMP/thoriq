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
        Schema::create('payroll_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('omset_log_id')->constrained('omset_logs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kpi_grade_id')->nullable()->constrained('kpi_grades')->onDelete('set null');
            $table->decimal('nominal_gapok_diterima', 15, 2);
            $table->decimal('nominal_tukin_diterima', 15, 2);
            $table->string('status_pembayaran')->default('pending'); // pending, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_distributions');
    }
};
