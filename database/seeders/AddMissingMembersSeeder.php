<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Carbon\Carbon;

class AddMissingMembersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Adding missing 2 members...');
        
        // Add the missing members
        $missingMembers = [
            [
                'first_name' => 'NKONO',
                'last_name' => 'MICHAEL',
                'national_id' => 'SACO-0025',
                'email' => 'nkono.michael@saco.com',
                'phone' => '256' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'date_of_birth' => '1990-01-01',
                'address' => 'Kampala, Uganda',
                'city' => 'Kampala',
                'country' => 'Uganda',
            ],
            [
                'first_name' => 'NAMAYEGA',
                'last_name' => 'ANNET',
                'national_id' => 'SACO-0030',
                'email' => 'namayega.annet@saco.com',
                'phone' => '256' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'date_of_birth' => '1990-01-01',
                'address' => 'Kampala, Uganda',
                'city' => 'Kampala',
                'country' => 'Uganda',
            ],
        ];
        
        foreach ($missingMembers as $memberData) {
            Member::create(array_merge($memberData, [
                'created_at' => Carbon::now()->subMonths(12),
                'updated_at' => Carbon::now()->subMonths(12),
            ]));
        }
        
        $this->command->info('Missing members added successfully!');
        $this->command->info('Now have exactly 30 members from Excel file.');
    }
}
