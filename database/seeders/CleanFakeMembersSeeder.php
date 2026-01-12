<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\MonthlySaving;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanFakeMembersSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Cleaning fake members and updating with real data...');
        
        // Real members from Excel file (2024/2025 loan status)
        $realMembers = [
            ['first_name' => 'NATUKUNDA', 'last_name' => 'ANN'],
            ['first_name' => 'MWESIGYE', 'last_name' => 'JONAH'],
            ['first_name' => 'CHRISTOPHER', 'last_name' => 'MWESIGYE'],
            ['first_name' => 'KIMULI', 'last_name' => 'BASIL'],
            ['first_name' => 'SERUNJOJI', 'last_name' => 'JOHN K'],
            ['first_name' => 'NAKIDDE', 'last_name' => 'SUZAN MAVIS'],
            ['first_name' => 'NASAAZI', 'last_name' => 'SHARON'],
            ['first_name' => 'BASIRIKA', 'last_name' => 'AIDAH'],
            ['first_name' => 'KUKKIRIZA', 'last_name' => 'EMMANUEL'],
            ['first_name' => 'NASASIRA', 'last_name' => 'DAVID'],
            ['first_name' => 'JONAH', 'last_name' => 'MUHUMUZA'],
            ['first_name' => 'SSEMWOGERERE', 'last_name' => 'DOUGLAS'],
            ['first_name' => 'MAGALA', 'last_name' => 'MARVIN'],
            ['first_name' => 'BABIRYE', 'last_name' => 'CLAIRE'],
            ['first_name' => 'NANCY', 'last_name' => 'KAZIBWE'],
            ['first_name' => 'SSENYUNGULE', 'last_name' => 'SHARIF'],
            ['first_name' => 'MUHIIRWE', 'last_name' => 'MADIINAH'],
            ['first_name' => 'ROSE', 'last_name' => 'NAKALEMA'],
            ['first_name' => 'BUZZI', 'last_name' => 'WC'],
            ['first_name' => 'KAWALYA', 'last_name' => 'BRIAN'],
            ['first_name' => 'GILLIAN', 'last_name' => 'A'],
            ['first_name' => 'MWESIGWA', 'last_name' => 'ELIPHAZI'],
            ['first_name' => 'MUKIIBI', 'last_name' => 'MIIKE'],
            ['first_name' => 'KAMOGA', 'last_name' => 'MAHAD'],
            ['first_name' => 'NKONO', 'last_name' => 'MICHAEL'],
            ['first_name' => 'EVE', 'last_name' => 'KAMPIIRE'],
            ['first_name' => 'LILLIAN', 'last_name' => 'NAMAGANDA'],
            ['first_name' => 'NAMAYEGA', 'last_name' => 'ANNET'],
        ];
        
        // Delete all fake members (those with LOAN- prefix)
        DB::table('members')->where('national_id', 'LIKE', 'LOAN-%')->delete();
        
        // Update existing real members with proper names
        foreach ($realMembers as $index => $memberData) {
            $memberId = $index + 1; // IDs 1-30
            
            $member = Member::find($memberId);
            if ($member) {
                $member->update([
                    'first_name' => $memberData['first_name'],
                    'last_name' => $memberData['last_name'],
                    'national_id' => 'SACO-' . str_pad($memberId, 4, '0', STR_PAD_LEFT),
                    'email' => strtolower($memberData['first_name'] . '.' . $memberData['last_name']) . '@saco.com',
                    'phone' => '256' . str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                    'date_of_birth' => '1990-01-01',
                    'address' => 'Kampala, Uganda',
                    'city' => 'Kampala',
                    'country' => 'Uganda',
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Clean up any extra members beyond 30
        Member::where('id', '>', 30)->delete();
        
        // Update related records to use correct member IDs
        $this->updateRelatedRecords();
        
        $this->command->info('Fake members cleaned successfully!');
        $this->command->info('Now have exactly 30 real members from Excel file.');
    }
    
    private function updateRelatedRecords()
    {
        // Update monthly savings to use correct member IDs (1-30)
        MonthlySaving::where('member_id', '>', 30)->delete();
        
        // Update member financials to use correct member IDs (1-30)
        MemberFinancial::where('member_id', '>', 30)->delete();
        
        // Update member loan summaries to use correct member IDs (1-30)
        MemberLoanSummary::where('member_id', '>', 30)->delete();
        
        // Update loans to use correct member IDs (1-30)
        Loan::where('member_id', '>', 30)->delete();
    }
}
