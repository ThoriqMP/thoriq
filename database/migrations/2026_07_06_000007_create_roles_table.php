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
        // 1. Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Treasury, Head, Sales, Developer, HRD, etc.
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Modify users table to use foreignId to roles
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_organisasi')) {
                $table->dropColumn('role_organisasi');
            }
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->string('role_organisasi')->default('Developer');
        });

        Schema::dropIfExists('roles');
    }
};
