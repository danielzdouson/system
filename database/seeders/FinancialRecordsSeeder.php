<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberFinancial;
use Illuminate\Support\Facades\DB;

class FinancialRecordsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('member_financials')->delete();

        // Import financial records based on Individual State.csv data
        $financialData = [
            [
                'member_id' => 256, // MWESIGYE JONAH
                'name' => 'MWESIGYE JONAH',
                'number' => '002',
                'savings' => 114294,
                'welfare' => 15000,
                'education_in' => 0,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 11400,
                'loan_charges' => 20000,
                'notes' => 'Regular contributor'
            ],
            [
                'member_id' => 257, // CHRISTOPHER MWESIGE
                'name' => 'CHRISTOPHER MWESIGE',
                'number' => '003',
                'savings' => 2753050,
                'welfare' => 8700,
                'education_in' => 12000,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 1000,
                'loan_charges' => 20000,
                'notes' => 'High savings contributor'
            ],
            [
                'member_id' => 258, // KIMULI BASIL
                'name' => 'KIMULI BASIL',
                'number' => '004',
                'savings' => 4840476,
                'welfare' => 15600,
                'education_in' => 9000,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 200,
                'loan_charges' => 20000,
                'notes' => 'Top savings member'
            ],
            [
                'member_id' => 259, // SERUNJOJI JOHN K
                'name' => 'SERUNJOJI JOHN K',
                'number' => '005',
                'savings' => 7898049,
                'welfare' => 1595100,
                'education_in' => 275000,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 800,
                'loan_charges' => 20000,
                'notes' => 'Active member with multiple contributions'
            ],
            [
                'member_id' => 260, // NAKIDDE SUZAN MAVIS
                'name' => 'NAKIDDE SUZAN MAVIS',
                'number' => '006',
                'savings' => 1100000,
                'welfare' => 11000,
                'education_in' => 0,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 0,
                'loan_charges' => 0,
                'notes' => 'New member with basic savings'
            ],
            [
                'member_id' => 261, // NASAAZI SHARON
                'name' => 'NASAAZI SHARON',
                'number' => '007',
                'savings' => 7872800,
                'welfare' => 15000,
                'education_in' => 152800,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 800,
                'loan_charges' => 125000,
                'notes' => 'Regular contributor with education fund'
            ],
            [
                'member_id' => 262, // BASIRIKA AIDAH
                'name' => 'BASIRIKA AIDAH',
                'number' => '008',
                'savings' => 8359330,
                'welfare' => 2000,
                'education_in' => 7500,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 1400,
                'loan_charges' => 0,
                'notes' => 'Steady contributor'
            ],
            [
                'member_id' => 263, // KUKKIRIZA EMMANUEL
                'name' => 'KUKKIRIZA EMMANUEL',
                'number' => '009',
                'savings' => 3175076,
                'welfare' => 13000,
                'education_in' => 15460,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 20000,
                'loan_charges' => 0,
                'notes' => 'Consistent saver'
            ],
            [
                'member_id' => 264, // NASASIRA DAVID
                'name' => 'NASASIRA DAVID',
                'number' => '010',
                'savings' => 1094000,
                'welfare' => 2000,
                'education_in' => 7500,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 50000,
                'loan_charges' => 0,
                'notes' => 'Active member'
            ],
            [
                'member_id' => 265, // JONAH MUHUMUZA
                'name' => 'JONAH MUHUMUZA',
                'number' => '011',
                'savings' => 2891000,
                'welfare' => 13000,
                'education_in' => 55100,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 660000,
                'loan_charges' => 0,
                'notes' => 'High contributor'
            ],
            [
                'member_id' => 266, // SSEMWOGERERE DOUGLAS
                'name' => 'SSEMWOGERERE DOUGLAS',
                'number' => '012',
                'savings' => 6993000,
                'welfare' => 10000,
                'education_in' => 53000,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 1500,
                'loan_charges' => 0,
                'notes' => 'High contributor'
            ],
            [
                'member_id' => 267, // MAGALA MARVIN
                'name' => 'MAGALA MARVIN',
                'number' => '013',
                'savings' => 13372000,
                'welfare' => 10000,
                'education_in' => 54000,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 7500,
                'loan_charges' => 0,
                'notes' => 'High contributor'
            ],
            [
                'member_id' => 268, // BABIRYE CLAIRE
                'name' => 'BABIRYE CLAIRE',
                'number' => '014',
                'savings' => 1059296,
                'welfare' => 3000,
                'education_in' => 4500,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 0,
                'loan_charges' => 0,
                'notes' => 'Minor member with small savings'
            ],
            [
                'member_id' => 269, // NANCY KAZIBWE
                'name' => 'NANCY KAZIBWE',
                'number' => '015',
                'savings' => 3048250,
                'welfare' => 2000,
                'education_in' => 7850,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 0,
                'loan_charges' => 0,
                'notes' => 'New member with basic savings'
            ],
            [
                'member_id' => 270, // SSENYUNGULE SHARIF
                'name' => 'SSENYUNGULE SHARIF',
                'number' => '016',
                'savings' => 2592815,
                'welfare' => 4600,
                'education_in' => 500,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 0,
                'loan_charges' => 0,
                'notes' => 'Active member'
            ],
            [
                'member_id' => 271, // MUHIIRWE MADIINAH
                'name' => 'MUHIIRWE MADIINAH',
                'number' => '017',
                'savings' => 5414050,
                'welfare' => 8000,
                'education_in' => 4600,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 1600,
                'loan_charges' => 0,
                'notes' => 'Medium contributor'
            ],
            [
                'member_id' => 272, // ROSE NAKALEMA
                'name' => 'ROSE NAKALEMA',
                'number' => '018',
                'savings' => 4705000,
                'welfare' => 12000,
                'education_in' => 800,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 203800,
                'loan_charges' => 0,
                'notes' => 'Large loan with regular repayments'
            ],
            [
                'member_id' => 273, // NAMAKULA BRIDGET
                'name' => 'NAMAKULA BRIDGET',
                'number' => '019',
                'savings' => 6446000,
                'welfare' => 10000,
                'education_in' => 9300,
                'fined' => 0,
                'fines_paid' => 0,
                'education_out' => 0,
                'loan_repayments' => 27200,
                'loan_charges' => 0,
                'notes' => 'Very large loan with substantial balance'
            ],
        ];

        foreach ($financialData as $record) {
            MemberFinancial::create($record);
        }

        $this->command->info('Imported ' . count($financialData) . ' financial records');
    }
}
