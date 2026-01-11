<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Product;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil produk yang sudah ada di database
        $products = Product::all();
        
        if ($products->count() > 0) {
            $purchaseOrder = PurchaseOrder::create([
                'title' => 'OPEN PO Inna Cookies',
                'description' => 'Purchase order untuk produk Inna Cookies',
                'start_date' => now()->addDays(10), // 10 hari dari sekarang
                'end_date' => now()->addDays(40),  // 40 hari dari sekarang
                'status' => 'open'
            ]);
            
            // Hubungkan produk dengan PO ini
            foreach ($products as $product) {
                $purchaseOrder->products()->attach($product->id, ['quantity' => $product->stock]);
            }
            
            // Log activity when creating the purchase order
            tulis_log_activity("membuat purcase order \"" . $purchaseOrder->title . "\"", PurchaseOrder::class, $purchaseOrder->id);
        }
    }
}