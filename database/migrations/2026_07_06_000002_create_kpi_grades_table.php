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
        Schema::create('kpi_grades', function (Blueprint $table) {
            $table->id();
            $table->string('grade_name')->unique(); // A, B, C
            $table->decimal('weight_percentage', 5, 2); // 14.00, 9.00, 4.50
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_grades');
    }
};
