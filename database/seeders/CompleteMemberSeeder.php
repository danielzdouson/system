<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use Illuminate\Support\Facades\DB;

class CompleteMemberSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('members')->delete();
        DB::table('member_financials')->delete();
        DB::table('member_loan_summaries')->delete();

        // Complete list of 30+ members including missing ones
        $members = [
            ['first_name' => 'MWESIGYE', 'last_name' => 'JONAH', 'national_id' => '0702422010', 'phone' => '0702422010', 'email' => 'mwesigye@sacco.com'],
            ['first_name' => 'CHRISTOPHER', 'last_name' => 'MWESIGE', 'national_id' => '0703783951', 'phone' => '0703783951', 'email' => 'christopher@sacco.com'],
            ['first_name' => 'KIMULI', 'last_name' => 'BASIL', 'national_id' => '0708586112', 'phone' => '0708586112', 'email' => 'kimuli@sacco.com'],
            ['first_name' => 'SERUNJOJI', 'last_name' => 'JOHN', 'national_id' => '0706505274', 'phone' => '0706505274', 'email' => 'serunji@sacco.com'],
            ['first_name' => 'NAKIDDE', 'last_name' => 'SUZAN MAVIS', 'national_id' => '0772479805', 'phone' => '0772479805', 'email' => 'nakidde@sacco.com'],
            ['first_name' => 'NASAAZI', 'last_name' => 'SHARON', 'national_id' => '0702043496', 'phone' => '0702043496', 'email' => 'nasaazi@sacco.com'],
            ['first_name' => 'BASIRIKA', 'last_name' => 'AIDAH', 'national_id' => '07', 'phone' => '07', 'email' => 'basirika@sacco.com'],
            ['first_name' => 'KUKKIRIZA', 'last_name' => 'EMMANUEL', 'national_id' => '0706585569', 'phone' => '0706585569', 'email' => 'kukkiriza@sacco.com'],
            ['first_name' => 'NASASIRA', 'last_name' => 'DAVID', 'national_id' => '0708516759', 'phone' => '0708516759', 'email' => 'nasasira@sacco.com'],
            ['first_name' => 'JONAH', 'last_name' => 'MUHUMUZA', 'national_id' => '+971 54 754 9432', 'phone' => '+971 54 754 9432', 'email' => 'jonah@sacco.com'],
            ['first_name' => 'SSEMWOGERERE', 'last_name' => 'DOUGLAS', 'national_id' => '0752520684', 'phone' => '0752520684', 'email' => 'ssemwogere@sacco.com'],
            ['first_name' => 'MAGALA', 'last_name' => 'MARVIN', 'national_id' => '0754129620', 'phone' => '0754129620', 'email' => 'magala@sacco.com'],
            ['first_name' => 'NANCY', 'last_name' => 'KAZIBWE', 'national_id' => '0701641908', 'phone' => '0701641908', 'email' => 'nancy@sacco.com'],
            ['first_name' => 'MUHIIRWE', 'last_name' => 'MADIINAH', 'national_id' => '0752542626', 'phone' => '0752542626', 'email' => 'muhiirwe@sacco.com'],
            ['first_name' => 'ROSE', 'last_name' => 'NAKALEMA', 'national_id' => '0705279439', 'phone' => '0705279439', 'email' => 'rose@sacco.com'],
            ['first_name' => 'SSENYUNGULE', 'last_name' => 'SHARIF', 'national_id' => '0753633015', 'phone' => '0753633015', 'email' => 'ssenyungule@sacco.com'],
            ['first_name' => 'NAMAKULA', 'last_name' => 'BRIDGET', 'national_id' => '0708129611', 'phone' => '0708129611', 'email' => 'namakula@sacco.com'],
            ['first_name' => 'NATUKUNDA', 'last_name' => 'ANN', 'national_id' => '0701234567', 'phone' => '0701234567', 'email' => 'natukunda@sacco.com'],
            ['first_name' => 'KIRUMIRA', 'last_name' => 'KIMBERLY', 'national_id' => '0702345678', 'phone' => '0702345678', 'email' => 'kirumira@sacco.com'],
            ['first_name' => 'BABIRYE', 'last_name' => 'CLAIRE', 'national_id' => '0703456789', 'phone' => '0703456789', 'email' => 'babirye@sacco.com'],
            ['first_name' => 'MUKASA', 'last_name' => 'JOSEPH', 'national_id' => '0704567890', 'phone' => '0704567890', 'email' => 'mukasa@sacco.com'],
            ['first_name' => 'NANKYA', 'last_name' => 'MARY', 'national_id' => '0705678901', 'phone' => '0705678901', 'email' => 'nankya@sacco.com'],
            ['first_name' => 'SEBUFU', 'last_name' => 'PETER', 'national_id' => '0706789012', 'phone' => '0706789012', 'email' => 'sebufu@sacco.com'],
            ['first_name' => 'NANTEZA', 'last_name' => 'GRACE', 'national_id' => '0707890123', 'phone' => '0707890123', 'email' => 'nantza@sacco.com'],
            ['first_name' => 'LUBEGA', 'last_name' => 'SAMUEL', 'national_id' => '0708901234', 'phone' => '0708901234', 'email' => 'lubega@sacco.com'],
            ['first_name' => 'NAMUSISI', 'last_name' => 'SARAH', 'national_id' => '0709012345', 'phone' => '0709012345', 'email' => 'namusisi@sacco.com'],
            ['first_name' => ' SSENYONJO', 'last_name' => 'DAVID', 'national_id' => '0710123456', 'phone' => '0710123456', 'email' => 'ssenyonjo@sacco.com'],
            ['first_name' => 'NABWAMI', 'last_name' => 'REBECCA', 'national_id' => '0711234567', 'phone' => '0711234567', 'email' => 'nabwami@sacco.com'],
            ['first_name' => 'MUGABI', 'last_name' => 'RICHARD', 'national_id' => '0712345678', 'phone' => '0712345678', 'email' => 'mugabi@sacco.com'],
            ['first_name' => 'NAMULONDO', 'last_name' => 'PATRICIA', 'national_id' => '0713456789', 'phone' => '0713456789', 'email' => 'namulondo@sacco.com'],
        ];

        foreach ($members as $index => $member) {
            $createdMember = Member::create($member);
            $this->command->info("Created member: " . $createdMember->first_name . " " . $createdMember->last_name . " (ID: " . $createdMember->id . ")");
        }

        $this->command->info('Imported ' . count($members) . ' members');
    }
}
