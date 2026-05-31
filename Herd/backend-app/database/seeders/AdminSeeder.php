<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
              'username' => 'superadmin',
            'email' => 'admin@inklusi.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'approved',
            'phone' => '08123456789',
            'verified_at' => now(),
        ]);
    }
}