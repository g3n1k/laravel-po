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
        Schema::table('po_customers', function (Blueprint $table) {
            $table->foreign('transaction_summary_id')->references('id')->on('transaction_summaries')->onDelete('set null');
        });

        Schema::table('down_payments', function (Blueprint $table) {
            $table->foreign('transaction_summary_id')->references('id')->on('transaction_summaries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_customers', function (Blueprint $table) {
            $table->dropForeign(['transaction_summary_id']);
        });

        Schema::table('down_payments', function (Blueprint $table) {
            $table->dropForeign(['transaction_summary_id']);
        });
    }
};
