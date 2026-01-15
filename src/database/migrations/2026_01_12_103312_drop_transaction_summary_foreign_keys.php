<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus foreign key constraint secara manual karena tidak bisa menggunakan Blueprint
        DB::statement('ALTER TABLE po_customers DROP CONSTRAINT IF EXISTS po_customers_transaction_summary_id_foreign;');
        DB::statement('ALTER TABLE down_payments DROP CONSTRAINT IF EXISTS down_payments_transaction_summary_id_foreign;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu direverse karena akan ditangani oleh migrasi lain
    }
};
