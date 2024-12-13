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
            'name' => 'John Doe',
            'email' => 'johndoe1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'janesmith1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Alice Johnson',
            'email' => 'alicejohnson1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Bob Brown',
            'email' => 'bobbrown1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Charlie Davis',
            'email' => 'charliedavis1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Diana Evans',
            'email' => 'dianaevans1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Edward Green',
            'email' => 'edwardgreen1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Fiona Harris',
            'email' => 'fionaharris1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'George Ives',
            'email' => 'georgeives1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Hannah James',
            'email' => 'hannahjames1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Isaac King',
            'email' => 'isaacking1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);

        User::create([
            'name' => 'Jackie Lee',
            'email' => 'jackielee1@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'tester',
        ]);
    }
}
