<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing member
        $member = Member::first();
        
        if ($member && !User::where('email', $member->email)->exists()) {
            // Create admin user for existing member
            User::create([
                'name' => $member->first_name . ' ' . $member->last_name,
                'email' => $member->email,
                'password' => bcrypt('password'),
                'member_id' => $member->id,
                'role' => 'admin',
            ]);
        }
    }
}
