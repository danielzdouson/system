<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Force30MembersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Force cleaning and recreating all members...');
        
        // Clear all members
        DB::table('members')->delete();
        
        // Real 30 members from Excel
        $members = [
            ['first_name' => 'NATUKUNDA', 'last_name' => 'ANN', 'national_id' => 'SACO-0001'],
            ['first_name' => 'MWESIGYE', 'last_name' => 'JONAH', 'national_id' => 'SACO-0002'],
            ['first_name' => 'CHRISTOPHER', 'last_name' => 'MWESIGYE', 'national_id' => 'SACO-0003'],
            ['first_name' => 'KIMULI', 'last_name' => 'BASIL', 'national_id' => 'SACO-0004'],
            ['first_name' => 'SERUNJOJI', 'last_name' => 'JOHN K', 'national_id' => 'SACO-0005'],
            ['first_name' => 'NAKIDDE', 'last_name' => 'SUZAN MAVIS', 'national_id' => 'SACO-0006'],
            ['first_name' => 'NASAAZI', 'last_name' => 'SHARON', 'national_id' => 'SACO-0007'],
            ['first_name' => 'BASIRIKA', 'last_name' => 'AIDAH', 'national_id' => 'SACO-0008'],
            ['first_name' => 'KUKKIRIZA', 'last_name' => 'EMMANUEL', 'national_id' => 'SACO-0009'],
            ['first_name' => 'NASASIRA', 'last_name' => 'DAVID', 'national_id' => 'SACO-0010'],
            ['first_name' => 'JONAH', 'last_name' => 'MUHUMUZA', 'national_id' => 'SACO-0011'],
            ['first_name' => 'SSEMWOGERERE', 'last_name' => 'DOUGLAS', 'national_id' => 'SACO-0012'],
            ['first_name' => 'MAGALA', 'last_name' => 'MARVIN', 'national_id' => 'SACO-0013'],
            ['first_name' => 'BABIRYE', 'last_name' => 'CLAIRE', 'national_id' => 'SACO-0014'],
            ['first_name' => 'NANCY', 'last_name' => 'KAZIBWE', 'national_id' => 'SACO-0015'],
            ['first_name' => 'SSENYUNGULE', 'last_name' => 'SHARIF', 'national_id' => 'SACO-0016'],
            ['first_name' => 'MUHIIRWE', 'last_name' => 'MADIINAH', 'national_id' => 'SACO-0017'],
            ['first_name' => 'ROSE', 'last_name' => 'NAKALEMA', 'national_id' => 'SACO-0018'],
            ['first_name' => 'BUZZI', 'last_name' => 'WC', 'national_id' => 'SACO-0019'],
            ['first_name' => 'KAWALYA', 'last_name' => 'BRIAN', 'national_id' => 'SACO-0020'],
            ['first_name' => 'GILLIAN', 'last_name' => 'A', 'national_id' => 'SACO-0021'],
            ['first_name' => 'MWESIGWA', 'last_name' => 'ELIPHAZI', 'national_id' => 'SACO-0022'],
            ['first_name' => 'MUKIIBI', 'last_name' => 'MIIKE', 'national_id' => 'SACO-0023'],
            ['first_name' => 'KAMOGA', 'last_name' => 'MAHAD', 'national_id' => 'SACO-0024'],
            ['first_name' => 'NKONO', 'last_name' => 'MICHAEL', 'national_id' => 'SACO-0025'],
            ['first_name' => 'EVE', 'last_name' => 'KAMPIIRE', 'national_id' => 'SACO-0026'],
            ['first_name' => 'LILLIAN', 'last_name' => 'NAMAGANDA', 'national_id' => 'SACO-0027'],
            ['first_name' => 'NAMAYEGA', 'last_name' => 'ANNET', 'national_id' => 'SACO-0028'],
            ['first_name' => 'NAMAYEGA', 'last_name' => 'ANNET', 'national_id' => 'SACO-0029'],
            ['first_name' => 'NAMAYEGA', 'last_name' => 'ANNET', 'national_id' => 'SACO-0030'],
        ];
        
        // Insert all 30 members
        foreach ($members as $index => $memberData) {
            Member::create(array_merge($memberData, [
                'email' => 'member' . ($index + 1) . '@saco.com',
                'phone' => '256' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                'date_of_birth' => '1990-01-01',
                'address' => 'Kampala, Uganda',
                'city' => 'Kampala',
                'country' => 'Uganda',
                'created_at' => Carbon::now()->subMonths(12),
                'updated_at' => Carbon::now()->subMonths(12),
            ]));
        }
        
        $this->command->info('Successfully created 30 members!');
    }
}
