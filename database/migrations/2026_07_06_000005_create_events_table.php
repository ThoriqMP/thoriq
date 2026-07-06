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
        // 1. Re-create events table as the "Planning/Budget" table
        Schema::dropIfExists('event_expenses');
        Schema::dropIfExists('events');

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_event');
            $table->date('tanggal_event')->nullable();
            $table->foreignId('pic_id')->constrained('users')->onDelete('cascade');
            
            // Plannings / Budgets for 3 Categories
            $table->decimal('budget_transportasi', 15, 2)->default(0);
            $table->decimal('budget_akomodasi', 15, 2)->default(0);
            $table->decimal('budget_venue', 15, 2)->default(0);
            
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->timestamps();
        });

        // 2. Create event_expenses table to record actual spent itemized list with quantity and price
        Schema::create('event_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('kategori'); // transportasi, akomodasi, venue
            $table->string('nama_item'); // e.g., Tiket Kereta Argo Bromo, Hotel Santika Room A
            $table->integer('quantity')->default(1);
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->date('tanggal_pengeluaran');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_expenses');
        Schema::dropIfExists('events');
    }
};
