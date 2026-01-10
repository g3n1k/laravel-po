<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Indra',
                'phone' => '081234567890',
                'email' => 'indra@example.com',
                'address' => 'Jakarta'
            ],
            [
                'name' => 'Gilby',
                'phone' => '082345678901',
                'email' => 'gilby@example.com',
                'address' => 'Bandung'
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );
        }
    }
}