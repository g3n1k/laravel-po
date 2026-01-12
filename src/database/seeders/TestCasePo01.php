<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PoCustomer;
use App\Models\StockAdjustment;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\Product;

class TestCasePo01 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data dari Purchase Order, Customer, dan Product yang sudah ada
        $po = PurchaseOrder::first();
        $customers = Customer::all();
        $products = Product::all();

        if (!$po || $customers->isEmpty() || $products->isEmpty()) {
            $this->command->info('Tidak cukup data untuk membuat test case PO 01. Pastikan Purchase Order, Customer, dan Product sudah di-seed.');
            return;
        }

        // Contoh data dari deskripsi: PO Inna Cookies
        // Produk: kue nastar polos, kue nastar keju, sagu keju, putri salju
        // Pelanggan: ibu saibah, Pak Nana, Kakak Nuri
        
        // Pastikan kita punya produk yang sesuai
        $edamame = $products->firstWhere('name', 'Edamame'); // Gunakan produk yang ada
        $kopiCiung = $products->firstWhere('name', 'Kopi Ciung 500 gram');
        $tapePati = $products->firstWhere('name', 'Tape Pati');

        if (!$edamame || !$kopiCiung || !$tapePati) {
            $this->command->info('Produk yang diperlukan tidak ditemukan. Menggunakan produk pertama dari database.');
            $edamame = $products->first();
            $kopiCiung = $products->skip(1)->first() ?? $edamame;
            $tapePati = $products->skip(2)->first() ?? $edamame;
        }

        // Pastikan kita punya pelanggan yang sesuai
        $indra = $customers->first();
        $gilby = $customers->skip(1)->first() ?? $indra;

        // Buat data PoCustomer berdasarkan contoh kasus
        $poCustomer1 = PoCustomer::create([
            'purchase_order_id' => $po->id,
            'customer_id' => $indra->id,
            'product_id' => $edamame->id,
            'item_quantity' => 3,
            'received_quantity' => 0,
            'status' => 'waiting',
            'payment_status' => 'unpaid',
            'ordered_at' => '2025-01-12 12:03:00',
        ]);

        $poCustomer2 = PoCustomer::create([
            'purchase_order_id' => $po->id,
            'customer_id' => $gilby->id,
            'product_id' => $edamame->id,
            'item_quantity' => 4,
            'received_quantity' => 0,
            'status' => 'waiting',
            'payment_status' => 'unpaid',
            'ordered_at' => '2025-01-12 13:05:00',
        ]);

        $poCustomer3 = PoCustomer::create([
            'purchase_order_id' => $po->id,
            'customer_id' => $indra->id,
            'product_id' => $kopiCiung->id,
            'item_quantity' => 1,
            'received_quantity' => 0,
            'status' => 'waiting',
            'payment_status' => 'unpaid',
            'ordered_at' => '2025-01-13 13:05:00',
        ]);

        $poCustomer4 = PoCustomer::create([
            'purchase_order_id' => $po->id,
            'customer_id' => $gilby->id,
            'product_id' => $tapePati->id,
            'item_quantity' => 3,
            'received_quantity' => 0,
            'status' => 'waiting',
            'payment_status' => 'unpaid',
            'ordered_at' => '2025-01-13 17:03:00',
        ]);

        $poCustomer5 = PoCustomer::create([
            'purchase_order_id' => $po->id,
            'customer_id' => $indra->id,
            'product_id' => $tapePati->id,
            'item_quantity' => 3,
            'received_quantity' => 0,
            'status' => 'waiting',
            'payment_status' => 'unpaid',
            'ordered_at' => '2025-01-14 17:03:00',
        ]);

        $poCustomer6 = PoCustomer::create([
            'purchase_order_id' => $po->id,
            'customer_id' => $indra->id,
            'product_id' => $edamame->id,
            'item_quantity' => 3,
            'received_quantity' => 0,
            'status' => 'waiting',
            'payment_status' => 'unpaid',
            'ordered_at' => '2025-01-15 18:03:00',
        ]);

        // Buat data StockAdjustment berdasarkan informasi yang diberikan
        $stockAdjustment1 = StockAdjustment::create([
            'product_id' => 1, // Edamame
            'initial_stock' => 0,
            'adjustment' => 8,
            'final_stock' => 8,
            'reason' => 'turun 8 kilo edamame',
        ]);

        $stockAdjustment2 = StockAdjustment::create([
            'product_id' => 3, // Tape Pati
            'initial_stock' => 0,
            'adjustment' => 5,
            'final_stock' => 5,
            'reason' => 'add 5 tape pati',
        ]);

        // Update stok produk sesuai dengan stock adjustment
        \DB::table('products')
            ->where('id', 1)
            ->update(['stock' => 8, 'updated_at' => now()]);

        \DB::table('products')
            ->where('id', 2)
            ->update(['stock' => 0, 'updated_at' => now()]);

        \DB::table('products')
            ->where('id', 3)
            ->update(['stock' => 5, 'updated_at' => now()]);

        // Tambahkan data down payment sesuai informasi
        \App\Models\DownPayment::create([
            'purchase_order_id' => 1,
            'customer_id' => 1, // Indra
            'amount' => 55000,
            'paid_at' => now(),
            'notes' => 'indra dp 55rb',
        ]);

        $this->command->info('Test Case PO 01 berhasil di-seed.');
    }
}