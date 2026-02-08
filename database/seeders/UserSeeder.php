<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // The Owner (Admin)
\App\Models\User::create([
    'name' => 'Wenshen Owner',
    'email' => 'admin@wenshen.com',
    'password' => bcrypt('password'), // You can change this later
    'role' => 'admin',
]);

// The Receptionist (Staff)
\App\Models\User::create([
    'name' => 'Receptionist',
    'email' => 'staff@wenshen.com',
    'password' => bcrypt('password'),
    'role' => 'staff',
]);
    }
}
