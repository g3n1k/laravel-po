<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user to associate with activities
        $user = User::first();

        ActivityLog::create([
            'description' => 'Setup The PO App',
            'user_id' => $user ? $user->id : null,
            'created_at' => now(),
        ]);
    }
}
