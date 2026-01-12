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
        Schema::create('transaction_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->decimal('total_bill', 10, 2)->default(0); // Total tagihan
            $table->decimal('total_dp', 10, 2)->default(0); // Total DP
            $table->decimal('additional_payment', 10, 2)->default(0); // Pembayaran tambahan
            $table->decimal('remaining_payment', 10, 2)->default(0); // Sisa pembayaran
            $table->string('status')->default('pending'); // Status transaksi (pending, completed, etc.)
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamp('completed_at')->nullable(); // Waktu transaksi selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_summaries');
    }
};
