<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemberLoanSummary;
use Illuminate\Support\Facades\DB;

class CorrectedLoanSeeder extends Seeder
{
    public function run()
    {
        // Clear existing loan data
        DB::table('member_loan_summaries')->delete();

        // Loan records with correct member IDs
        $loanData = [
            [
                'member_id' => 291, // KIRUMIRA KIMBERLY
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
                'member_id' => 274, // CHRISTOPHER MWESIGE
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
                'member_id' => 275, // KIMULI BASIL
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
                'member_id' => 276, // SERUNJOJI JOHN
                'name' => 'SERUNJOJI JOHN',
                'loan_brought_forward' => 17100000,
                'loan_issued_current_year' => 360000,
                'current_year_loan_plus_interest' => 360000,
                'loan_balance_without_fines' => 17100000,
                'loan_out' => 19200,
                'total' => 17100000,
                'notes' => 'Large loan with regular repayments'
            ],
            [
                'member_id' => 277, // NAKIDDE SUZAN MAVIS
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
                'member_id' => 278, // NASAAZI SHARON
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
                'member_id' => 279, // BASIRIKA AIDAH
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
                'member_id' => 280, // KUKKIRIZA EMMANUEL
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
                'member_id' => 281, // NASASIRA DAVID
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
                'member_id' => 282, // JONAH MUHUMUZA
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
                'member_id' => 283, // SSEMWOGERERE DOUGLAS
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
                'member_id' => 284, // MAGALA MARVIN
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
                'member_id' => 292, // BABIRYE CLAIRE
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
                'member_id' => 285, // NANCY KAZIBWE
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
                'member_id' => 288, // SSENYUNGULE SHARIF
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
                'member_id' => 286, // MUHIIRWE MADIINAH
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
                'member_id' => 287, // ROSE NAKALEMA
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
                'member_id' => 289, // NAMAKULA BRIDGET
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

        $this->command->info('Imported ' . count($loanData) . ' loan records with correct member IDs');
    }
}
