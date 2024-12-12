<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'super-admin',
        ]);

        // Create Tester user
        User::create([
            'name' => 'Tester',
            'email' => 'tester@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);
    }
}
