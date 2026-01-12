<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FiscalYear;
use App\Models\Member;
use Carbon\Carbon;

class GroupSavingsSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Setting up Group Savings System...');
        
        // Create active fiscal year 2024/2025
        $fiscalYear = FiscalYear::firstOrCreate([
            'name' => '2024/2025',
        ], [
            'start_date' => Carbon::create(2024, 7, 1),
            'end_date' => Carbon::create(2025, 6, 30),
            'status' => 'active',
        ]);
        
        $this->command->info('Created fiscal year: ' . $fiscalYear->name);
        
        // Create initial group savings records for all members
        $members = Member::all();
        $createdCount = 0;
        $userId = \App\Models\User::first()?->id ?? null;
        
        foreach ($members as $member) {
            for ($month = 7; $month <= 12; $month++) { // July to December 2024
                \App\Models\GroupSaving::firstOrCreate([
                    'member_id' => $member->id,
                    'fiscal_year_id' => $fiscalYear->id,
                    'month' => $month,
                ], [
                    'amount' => 0,
                    'status' => 'pending',
                    'created_by' => $userId,
                ]);
                $createdCount++;
            }
            
            for ($month = 1; $month <= 6; $month++) { // January to June 2025
                \App\Models\GroupSaving::firstOrCreate([
                    'member_id' => $member->id,
                    'fiscal_year_id' => $fiscalYear->id,
                    'month' => $month,
                ], [
                    'amount' => 0,
                    'status' => 'pending',
                    'created_by' => $userId,
                ]);
                $createdCount++;
            }
        }
        
        $this->command->info("Created {$createdCount} group savings records for {$members->count()} members");
        
        // Create welfare fund records for each month
        $welfareCount = 0;
        $userId = \App\Models\User::first()?->id ?? null;
        
        for ($month = 7; $month <= 12; $month++) { // July to December 2024
            \App\Models\WelfareFund::firstOrCreate([
                'fiscal_year_id' => $fiscalYear->id,
                'month' => $month,
            ], [
                'amount' => 0,
                'description' => 'Welfare fund for ' . Carbon::create()->month($month)->format('F') . ' ' . 2024,
                'created_by' => $userId,
            ]);
            $welfareCount++;
        }
        
        for ($month = 1; $month <= 6; $month++) { // January to June 2025
            \App\Models\WelfareFund::firstOrCreate([
                'fiscal_year_id' => $fiscalYear->id,
                'month' => $month,
            ], [
                'amount' => 0,
                'description' => 'Welfare fund for ' . Carbon::create()->month($month)->format('F') . ' ' . 2025,
                'created_by' => $userId,
            ]);
            $welfareCount++;
        }
        
        $this->command->info("Created {$welfareCount} welfare fund records");
        
        $this->command->info('Group Savings System setup completed!');
    }
}
