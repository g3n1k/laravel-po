<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Edamame',
                'price' => 19000,
                'stock' => 0,
                'description' => 'kacang edamame'
            ],
            [
                'name' => 'Kopi Ciung 500 gram',
                'price' => 35000,
                'stock' => 0,
                'description' => 'kopi'
            ],
            [
                'name' => 'Tape Pati',
                'price' => 10000,
                'stock' => 0,
                'description' => 'tape pati'
            ]
        ];

        foreach ($products as $productData) {
            Product::updateOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }
    }
}