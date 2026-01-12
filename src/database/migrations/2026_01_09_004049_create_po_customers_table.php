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
        Schema::create('po_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('item_quantity');
            $table->integer('received_quantity')->default(0);
            $table->enum('status', ['waiting', 'complete', 'out_of_stock', 'not_complete', 'cancel'])->default('waiting');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->decimal('payment_product_price', 10, 2)->default(0);
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_customers');
    }
};
