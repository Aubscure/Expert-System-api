<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seeds if no admin exists — idempotent
        if (Admin::count() === 0) {
            Admin::create([
                'name'     => 'System Administrator',
                'email'    => env('ADMIN_EMAIL'),   // set in .env, never hardcoded
                'password' => Hash::make(env('ADMIN_PASSWORD')), // strong random password
            ]);
        }
    }
}
