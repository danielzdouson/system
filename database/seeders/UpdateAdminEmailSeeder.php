<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateAdminEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update admin email from admin@saco.com to admin@sacco.com
        User::where('email', 'admin@saco.com')->update(['email' => 'admin@sacco.com']);
        
        $this->command->info('Admin email updated to admin@sacco.com');
    }
}
