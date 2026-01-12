<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MonthlySaving;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SyncMonthlySavingsSeeder extends Seeder
{
    public function run()
    {
        // Get all members
        $allMembers = Member::all();
        
        // Get members who already have monthly savings
        $membersWithSavings = MonthlySaving::pluck('member_id')->toArray();
        
        // Find members without monthly savings
        $membersWithoutSavings = $allMembers->whereNotIn('id', $membersWithSavings);
        
        echo "Found {$allMembers->count()} total members\n";
        echo "Found " . count($membersWithSavings) . " members with monthly savings\n";
        echo "Need to create monthly savings for " . $membersWithoutSavings->count() . " members\n\n";
        
        // Create monthly savings records for members who don't have them
        foreach ($membersWithoutSavings as $member) {
            echo "Creating monthly savings for: {$member->first_name} {$member->last_name}\n";
            
            MonthlySaving::create([
                'member_id' => $member->id,
                'member_name' => $member->first_name . ' ' . $member->last_name,
                'membership_number' => 'SACCO-' . str_pad($member->id, 4, '0', STR_PAD_LEFT),
                'start_balance' => 0,
                'jul_25' => 0,
                'aug_25' => 0,
                'sep_25' => 0,
                'oct_25' => 0,
                'nov_25' => 0,
                'dec_25' => 0,
                'jan_26' => 0,
                'feb_26' => 0,
                'mar_26' => 0,
                'apr_26' => 0,
                'may_26' => 0,
                'jun_26' => 0,
                'year_2024_2025_totals' => 0,
                'current_year_savings' => 0,
            ]);
        }
        
        echo "\nSync completed! All members now have monthly savings records.\n";
        echo "Total monthly savings records: " . MonthlySaving::count() . "\n";
    }
}
