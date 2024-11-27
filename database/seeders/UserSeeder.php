<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'company_id' => 1, // Sesuaikan dengan kebutuhan
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password123'), // Gunakan Hash untuk keamanan
            'phone_number' => '1234567890',
            'address' => 'Super Admin Address',
            'role' => 'super_admin',
        ]);
    }
}
