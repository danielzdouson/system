<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing admin user with this email
        User::where('email', 'admin@saco.com')->delete();
        
        // Create new admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@saco.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
        
        $this->command->info('Admin user created successfully.');
        $this->command->info('Email: admin@saco.com');
        $this->command->info('Password: admin123');
    }
}
