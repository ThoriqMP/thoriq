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
        Schema::table('omset_logs', function (Blueprint $table) {
            $table->unsignedTinyInteger('tahun')->default(1)->after('nominal_omset');
            $table->decimal('alokasi_development', 15, 2)->default(0.00)->after('alokasi_perusahaan');
            $table->decimal('alokasi_partnership', 15, 2)->default(0.00)->after('alokasi_development');
            $table->decimal('alokasi_penasehat', 15, 2)->default(0.00)->after('alokasi_partnership');
            $table->decimal('alokasi_saham', 15, 2)->default(0.00)->after('alokasi_penasehat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('omset_logs', function (Blueprint $table) {
            $table->dropColumn(['tahun', 'alokasi_development', 'alokasi_partnership', 'alokasi_penasehat', 'alokasi_saham']);
        });
    }
};
