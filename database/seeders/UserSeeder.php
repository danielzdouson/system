<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sacco.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create loans officer
        User::create([
            'name' => 'Loans Officer',
            'email' => 'loans@saco.com',
            'password' => Hash::make('password'),
            'role' => 'loans_officer',
        ]);

        // Create treasurer
        User::create([
            'name' => 'Treasurer',
            'email' => 'treasurer@saco.com',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
        ]);

        // Create regular member
        User::create([
            'name' => 'Test Member',
            'email' => 'member@saco.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
    }
}
