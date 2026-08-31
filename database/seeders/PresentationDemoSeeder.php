<?php

namespace Database\Seeders;

use App\Models\FiscalYear;
use App\Models\Member;
use App\Models\MemberAccount;
use App\Models\MemberFinancial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PresentationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $fiscalYear = FiscalYear::firstOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'status' => 'active',
            ]
        );

        $members = [
            ['first_name' => 'Amina', 'last_name' => 'Nabirye', 'phone' => '0700000001', 'savings' => 1850000],
            ['first_name' => 'Brian', 'last_name' => 'Kato', 'phone' => '0700000002', 'savings' => 3200000],
            ['first_name' => 'Clara', 'last_name' => 'Nankya', 'phone' => '0700000003', 'savings' => 950000],
            ['first_name' => 'Daniel', 'last_name' => 'Ouma', 'phone' => '0700000004', 'savings' => 2400000],
            ['first_name' => 'Esther', 'last_name' => 'Achieng', 'phone' => '0700000005', 'savings' => 1250000],
            ['first_name' => 'Frank', 'last_name' => 'Mugisha', 'phone' => '0700000006', 'savings' => 2750000],
            ['first_name' => 'Grace', 'last_name' => 'Atim', 'phone' => '0700000007', 'savings' => 1800000],
            ['first_name' => 'Henry', 'last_name' => 'Ssekandi', 'phone' => '0700000008', 'savings' => 4100000],
        ];

        foreach ($members as $index => $data) {
            $member = Member::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'national_id' => 'DEMO-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'phone' => $data['phone'],
                'email' => strtolower($data['first_name'] . '.' . $data['last_name']) . '@demo.sacco',
                'address' => 'SACCO Demo Town',
                'city' => 'Kampala',
                'country' => 'Uganda',
            ]);

            MemberFinancial::create([
                'member_id' => $member->id,
                'name' => $member->first_name . ' ' . $member->last_name,
                'number' => str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'savings' => $data['savings'],
                'welfare' => 25000,
                'loan_repayments' => $index % 2 === 0 ? 180000 : 0,
                'loan_charges' => $index % 2 === 0 ? 15000 : 0,
                'notes' => 'Synthetic presentation data',
            ]);

            MemberAccount::create([
                'member_id' => $member->id,
                'fiscal_year_id' => $fiscalYear->id,
                'total_deposited' => $data['savings'] + 25000,
                'current_balance' => $data['savings'] + 25000,
                'savings_balance' => $data['savings'],
                'welfare_balance' => 25000,
                'total_shares' => 100,
            ]);

            if ($index === 0) {
                User::where('email', 'member@saco.com')->update(['member_id' => $member->id]);
            }
        }

        User::where('email', 'admin@sacco.com')->update([
            'password' => Hash::make('password'),
        ]);
    }
}
