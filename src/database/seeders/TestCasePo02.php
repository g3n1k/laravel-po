<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PoCustomer;
use App\Models\StockAdjustment;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Product;

class TestCasePo02 extends Seeder
{
    /**
     * lanjutan dari TestCasePo01
     * - barang sudah di distribusikan
     * - request po pertama sudah di bayarkan
     */
    public function run(): void
    {
        
        # transaksi pertama di selesaikan
        \App\Models\TransactionSummary::create([
            'purchase_order_id' => 1,
            'customer_id' => 1, // Indra
            'total_bill' => 96000,
            'total_dp' => 55000,
            'additional_payment' => 41000,
            'remaining_payment' => 0,
            'status' => 'complete',
            'notes' => 'transaksi 1 an indra selesai',
        ]);

        # pendistribusian barang
        $this->command->info('Test Case PO 02: start seed distribusi barang.');
        $_distribute_barang = [
            1=> [3, 'complete', 'paid', 57000, 19000, 1],
            2=> [4, 'complete', 'unpaid', 0, 0, null],
            3=> [0, 'out_of_stock', 'paid', 0, 35000, 1],
            4=> [3, 'complete', 'unpaid', 0, 0, null],
            5=> [2, 'not_complete', 'paid', 20000, 10000, 1],
            6=> [1, 'not_complete', 'paid', 19000, 19000, 1],
        ];
        foreach($_distribute_barang as $id => $value) {
            \DB::table('po_customers')
                ->where('id', $id)
                ->update([
                    'received_quantity' => $value[0],
                    'status' => $value[1],
                    'payment_status' => $value[2],
                    'payment_amount' => $value[3],
                    'payment_product_price' => $value[4],
                    'transaction_summary_id' => $value[5],
                    'updated_at' => now()
                ]);
        }

        # update down payment untuk menghubungkan dengan transaction summary
        \DB::table('down_payments')
            ->where('id', 1)
            ->update([
                'transaction_summary_id' => 1,
                'updated_at' => now()
            ]);

        

        $this->command->info('Test Case PO 02 berhasil di-seed.');
    }
}