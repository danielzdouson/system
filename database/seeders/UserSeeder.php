<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@sacco.com'], [
            'name' => 'Admin User',
            'email' => 'admin@sacco.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        User::updateOrCreate(['email' => 'loans@saco.com'], [
            'name' => 'Loans Officer',
            'email' => 'loans@saco.com',
            'password' => Hash::make('password'),
            'role' => 'loans_officer',
        ]);

        User::updateOrCreate(['email' => 'treasurer@saco.com'], [
            'name' => 'Treasurer',
            'email' => 'treasurer@saco.com',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
        ]);

        User::updateOrCreate(['email' => 'member@saco.com'], [
            'name' => 'Test Member',
            'email' => 'member@saco.com',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);
    }
}
