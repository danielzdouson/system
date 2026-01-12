<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\MemberFinancial;
use App\Models\MemberLoanSummary;
use Illuminate\Support\Facades\DB;

class SACCODataSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('members')->delete();
        DB::table('member_financials')->delete();
        DB::table('member_loan_summaries')->delete();

        // Import Members (skip NATUKUNDA ANN)
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
        ];

        foreach ($members as $member) {
            Member::create($member);
        }

        $this->command->info('Imported ' . count($members) . ' members');
    }

    private function importFinancialRecords()
    {
        // Import financial records based on Individual State.csv data
        $financialData = [
            [
                'member_id' => 2, // MWESIGYE JONAH
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
                'member_id' => 3, // CHRISTOPHER MWESIGE
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
                'member_id' => 4, // KIMULI BASIL
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
                'member_id' => 5, // SERUNJOJI JOHN K
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
                'member_id' => 6, // NAKIDDE SUZAN MAVIS
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
                'member_id' => 7, // NASAAZI SHARON
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
                'member_id' => 8, // BASIRIKA AIDAH
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
                'member_id' => 9, // KUKKIRIZA EMMANUEL
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
                'member_id' => 10, // NASASIRA DAVID
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
                'member_id' => 11, // JONAH MUHUMUZA
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
                'member_id' => 12, // SSEMWOGERERE DOUGLAS
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
                'member_id' => 13, // MAGALA MARVIN
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
                'member_id' => 14, // BABIRYE CLAIRE
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
                'member_id' => 15, // NANCY KAZIBWE
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
                'member_id' => 16, // SSENYUNGULE SHARIF
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
                'member_id' => 17, // MUHIIRWE MADIINAH
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
                'member_id' => 18, // ROSE NAKALEMA
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
                'member_id' => 19, // NAMAKULA BRIDGET
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

    private function importLoanRecords()
    {
        // Import loan records from Loan Ledger.csv
        $loanData = [
            [
                'member_id' => 1, // KIRUMIRA KIMBERLY
                'name' => 'KIRUMIRA KIMBERLY',
                'loan_brought_forward' => 360000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 3659700,
                'loan_out' => 300300,
                'total' => 3960000,
                'notes' => 'Original loan with repayments'
            ],
            [
                'member_id' => 3, // CHRISTOPHER MWESIGE
                'name' => 'CHRISTOPHER MWESIGE',
                'loan_brought_forward' => 5390000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 5390000,
                'loan_out' => 120100,
                'total' => 5900000,
                'notes' => 'Multiple loans with repayments'
            ],
            [
                'member_id' => 4, // KIMULI BASIL
                'name' => 'KIMULI BASIL',
                'loan_brought_forward' => 5300000,
                'loan_issued_current_year' => 396000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 5300000,
                'loan_out' => 70200,
                'total' => 5900000,
                'notes' => 'Active loan with regular repayments'
            ],
            [
                'member_id' => 5, // SERUNJOJI JOHN K
                'name' => 'SERUNJOJI JOHN K',
                'loan_brought_forward' => 17100000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 17100000,
                'loan_out' => 19200,
                'total' => 17100000,
                'notes' => 'Large loan with regular repayments'
            ],
            [
                'member_id' => 6, // NAKIDDE SUZAN MAVIS
                'name' => 'NAKIDDE SUZAN MAVIS',
                'loan_brought_forward' => 8304000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 8304000,
                'loan_out' => 501000,
                'total' => 8904000,
                'notes' => 'High value loan with repayments'
            ],
            [
                'member_id' => 7, // NASAAZI SHARON
                'name' => 'NASAAZI SHARON',
                'loan_brought_forward' => 8304000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 8304000,
                'loan_out' => 301000,
                'total' => 8904000,
                'notes' => 'Medium loan with repayments'
            ],
            [
                'member_id' => 8, // BASIRIKA AIDAH
                'name' => 'BASIRIKA AIDAH',
                'loan_brought_forward' => 2640000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 2640000,
                'loan_out' => 301000,
                'total' => 3000000,
                'notes' => 'Medium loan with repayments'
            ],
            [
                'member_id' => 9, // KUKKIRIZA EMMANUEL
                'name' => 'KUKKIRIZA EMMANUEL',
                'loan_brought_forward' => 2284560,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 2284560,
                'loan_out' => 2545200,
                'total' => 2284560,
                'notes' => 'Large loan with substantial repayments'
            ],
            [
                'member_id' => 10, // NASASIRA DAVID
                'name' => 'NASASIRA DAVID',
                'loan_brought_forward' => 600000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 600000,
                'loan_out' => 545200,
                'total' => 1145200,
                'notes' => 'Multiple loans with good repayment history'
            ],
            [
                'member_id' => 11, // JONAH MUHUMUZA
                'name' => 'JONAH MUHUMUZA',
                'loan_brought_forward' => 2310000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 2310000,
                'loan_out' => 660000,
                'total' => 2970000,
                'notes' => 'Very active borrower with multiple loans'
            ],
            [
                'member_id' => 12, // SSEMWOGERERE DOUGLAS
                'name' => 'SSEMWOGERERE DOUGLAS',
                'loan_brought_forward' => 6993000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 6993000,
                'loan_out' => 153300,
                'total' => 7353000,
                'notes' => 'High value loan with regular repayments'
            ],
            [
                'member_id' => 13, // MAGALA MARVIN
                'name' => 'MAGALA MARVIN',
                'loan_brought_forward' => 28158000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 28158000,
                'loan_out' => 29500,
                'total' => 28515800,
                'notes' => 'Very large loan with substantial balance'
            ],
            [
                'member_id' => 14, // BABIRYE CLAIRE
                'name' => 'BABIRYE CLAIRE',
                'loan_brought_forward' => 2200000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 2200000,
                'loan_out' => 29500,
                'total' => 2229500,
                'notes' => 'Large loan with minimal repayments'
            ],
            [
                'member_id' => 15, // NANCY KAZIBWE
                'name' => 'NANCY KAZIBWE',
                'loan_brought_forward' => 3048250,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 3048250,
                'loan_out' => 86250,
                'total' => 3134500,
                'notes' => 'Large loan with substantial balance'
            ],
            [
                'member_id' => 16, // SSENYUNGULE SHARIF
                'name' => 'SSENYUNGULE SHARIF',
                'loan_brought_forward' => 4717100,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 4717100,
                'loan_out' => 2700,
                'total' => 4719800,
                'notes' => 'Large loan with regular repayments'
            ],
            [
                'member_id' => 17, // MUHIIRWE MADIINAH
                'name' => 'MUHIIRWE MADIINAH',
                'loan_brought_forward' => 2578750,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 2578750,
                'loan_out' => 1600,
                'total' => 2590350,
                'notes' => 'Medium loan with some repayments'
            ],
            [
                'member_id' => 18, // ROSE NAKALEMA
                'name' => 'ROSE NAKALEMA',
                'loan_brought_forward' => 7700000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 7700000,
                'loan_out' => 203800,
                'total' => 7903800,
                'notes' => 'Large loan with regular repayments'
            ],
            [
                'member_id' => 19, // NAMAKULA BRIDGET
                'name' => 'NAMAKULA BRIDGET',
                'loan_brought_forward' => 6446000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 6446000,
                'loan_out' => 27200,
                'total' => 6473200,
                'notes' => 'Very large loan with some repayments'
            ],
        ];

        foreach ($loanData as $record) {
            MemberLoanSummary::create($record);
        }

        $this->command->info('Imported ' . count($loanData) . ' loan records');
    }
}
