<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MonthlySaving;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class MonthlySavingsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('monthly_savings')->delete();

        // Parse CSV data and map to members
        $csvData = [
            ['name' => 'NATUKUNDA ANN', 'start_balance' => 1223000, 'jul_25' => 200000, 'aug_25' => 125000, 'sep_25' => 100000, 'oct_25' => 25000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 1673000, 'current_year' => 450000],
            ['name' => 'MWESIGYE JONAH', 'start_balance' => 114294, 'jul_25' => 0, 'aug_25' => 0, 'sep_25' => 0, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 114294, 'current_year' => 0],
            ['name' => 'CHRISTOPHER MWESIGE', 'start_balance' => 2653050, 'jul_25' => 100000, 'aug_25' => 0, 'sep_25' => 0, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 2753050, 'current_year' => 100000],
            ['name' => 'KIMULI BASIL', 'start_balance' => 4690476, 'jul_25' => 50000, 'aug_25' => 0, 'sep_25' => 100000, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 4840476, 'current_year' => 150000],
            ['name' => 'SERUNJOJI JOHN', 'start_balance' => 6923049, 'jul_25' => 200000, 'aug_25' => 400000, 'sep_25' => 275000, 'oct_25' => 100000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 7898049, 'current_year' => 975000],
            ['name' => 'NAKIDDE SUZAN MAVIS', 'start_balance' => -61000, 'jul_25' => 0, 'aug_25' => 500000, 'sep_25' => 400000, 'oct_25' => 200000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 1039000, 'current_year' => 1100000],
            ['name' => 'NASAAZI SHARON', 'start_balance' => 6622800, 'jul_25' => 500000, 'aug_25' => 300000, 'sep_25' => 300000, 'oct_25' => 150000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 7872800, 'current_year' => 1250000],
            ['name' => 'BASIRIKA AIDAH', 'start_balance' => 6484330, 'jul_25' => 500000, 'aug_25' => 625000, 'sep_25' => 500000, 'oct_25' => 250000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 8359330, 'current_year' => 1875000],
            ['name' => 'KUKKIRIZA EMMANUEL', 'start_balance' => 2525076, 'jul_25' => 100000, 'aug_25' => 250000, 'sep_25' => 200000, 'oct_25' => 100000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 3175076, 'current_year' => 650000],
            ['name' => 'NASASIRA DAVID', 'start_balance' => 569000, 'jul_25' => 175000, 'aug_25' => 275000, 'sep_25' => 50000, 'oct_25' => 25000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 1094000, 'current_year' => 525000],
            ['name' => 'JONAH MUHUMUZA', 'start_balance' => 2316000, 'jul_25' => 200000, 'aug_25' => 250000, 'sep_25' => 100000, 'oct_25' => 25000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 2891000, 'current_year' => 575000],
            ['name' => 'SSEMWOGERERE DOUGLAS', 'start_balance' => 6593000, 'jul_25' => 150000, 'aug_25' => 0, 'sep_25' => 175000, 'oct_25' => 75000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 6993000, 'current_year' => 400000],
            ['name' => 'MAGALA MARVIN', 'start_balance' => 13022000, 'jul_25' => 150000, 'aug_25' => 0, 'sep_25' => 125000, 'oct_25' => 75000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 13372000, 'current_year' => 350000],
            ['name' => 'BABIRYE CLAIRE', 'start_balance' => 984296, 'jul_25' => 25000, 'aug_25' => 0, 'sep_25' => 50000, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 1059296, 'current_year' => 75000],
            ['name' => 'NANCY KAZIBWE', 'start_balance' => 2998250, 'jul_25' => 0, 'aug_25' => 0, 'sep_25' => 50000, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 3048250, 'current_year' => 50000],
            ['name' => 'SSENYUNGULE SHARIF', 'start_balance' => 2467815, 'jul_25' => 0, 'aug_25' => 125000, 'sep_25' => 0, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 2592815, 'current_year' => 125000],
            ['name' => 'MUHIIRWE MADIINAH', 'start_balance' => 5214050, 'jul_25' => 100000, 'aug_25' => 100000, 'sep_25' => 0, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 5414050, 'current_year' => 200000],
            ['name' => 'ROSE NAKALEMA', 'start_balance' => 8580000, 'jul_25' => 100000, 'aug_25' => 100000, 'sep_25' => 100000, 'oct_25' => 25000, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 8905000, 'current_year' => 325000],
            ['name' => 'NAMAKULA BRIDGET', 'start_balance' => 6321000, 'jul_25' => 0, 'aug_25' => 125000, 'sep_25' => 0, 'oct_25' => 0, 'nov_25' => 0, 'dec_25' => 0, 'jan_26' => 0, 'feb_26' => 0, 'mar_26' => 0, 'apr_26' => 0, 'may_26' => 0, 'jun_26' => 0, 'year_total' => 6446000, 'current_year' => 125000],
        ];

        foreach ($csvData as $data) {
            // Find matching member
            $member = Member::where('first_name', explode(' ', $data['name'])[0])
                           ->where('last_name', explode(' ', $data['name'])[1] ?? '')
                           ->first();

            if ($member) {
                MonthlySaving::create([
                    'member_id' => $member->id,
                    'member_name' => $data['name'],
                    'membership_number' => str_pad($member->id, 3, '0', STR_PAD_LEFT),
                    'start_balance' => $data['start_balance'],
                    'jul_25' => $data['jul_25'],
                    'aug_25' => $data['aug_25'],
                    'sep_25' => $data['sep_25'],
                    'oct_25' => $data['oct_25'],
                    'nov_25' => $data['nov_25'],
                    'dec_25' => $data['dec_25'],
                    'jan_26' => $data['jan_26'],
                    'feb_26' => $data['feb_26'],
                    'mar_26' => $data['mar_26'],
                    'apr_26' => $data['apr_26'],
                    'may_26' => $data['may_26'],
                    'jun_26' => $data['jun_26'],
                    'year_2024_2025_totals' => $data['year_total'],
                    'current_year_savings' => $data['current_year'],
                ]);

                $this->command->info("Created monthly savings for: " . $data['name']);
            } else {
                $this->command->warn("Member not found for: " . $data['name']);
            }
        }

        $this->command->info('Monthly savings data imported successfully');
    }
}
