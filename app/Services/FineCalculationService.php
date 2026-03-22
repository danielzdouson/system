<?php

namespace App\Services;

use App\Models\Fine;
use App\Models\Member;
use App\Models\FiscalYear;
use App\Models\GroupSaving;
use Carbon\Carbon;

class FineCalculationService
{
    public function calculateMissedSavingFines($fiscalYearId, $month, $options = [])
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear) {
            throw new \Exception('Fiscal year not found');
        }

        // Get members who missed savings for the specified month
        $membersWithoutSavings = Member::whereDoesntHave('groupSavings', function($query) use ($fiscalYearId, $month) {
            $query->where('fiscal_year_id', $fiscalYearId)
                  ->where('month', $month)
                  ->where('status', 'paid');
        })->get();

        $results = [
            'applied' => 0,
            'skipped' => 0,
            'total_amount' => 0,
            'members' => []
        ];

        foreach ($membersWithoutSavings as $member) {
            // Check if fine already exists
            $existingFine = Fine::where('member_id', $member->id)
                ->where('fiscal_year_id', $fiscalYearId)
                ->where('month', $month)
                ->where('reason', 'missed_saving')
                ->first();

            if ($existingFine) {
                $results['skipped']++;
                $results['members'][] = [
                    'member' => $member,
                    'status' => 'skipped',
                    'reason' => 'Fine already exists',
                    'amount' => 0
                ];
                continue;
            }

            $fineAmount = $this->calculateFineAmount($member, $month, $fiscalYear, $options);
            
            if (!($options['dry_run'] ?? false)) {
                Fine::create([
                    'member_id' => $member->id,
                    'fiscal_year_id' => $fiscalYearId,
                    'month' => $month,
                    'amount' => $fineAmount,
                    'reason' => 'missed_saving',
                    'description' => "Auto-generated fine for missed saving in " . Carbon::create()->month($month)->format('F'),
                    'status' => 'pending',
                    'created_by' => $options['created_by'] ?? 1
                ]);
            }

            $results['applied']++;
            $results['total_amount'] += $fineAmount;
            $results['members'][] = [
                'member' => $member,
                'status' => 'applied',
                'reason' => 'Missed saving',
                'amount' => $fineAmount
            ];
        }

        return $results;
    }

    public function calculateLatePaymentFines($fiscalYearId, $month, $options = [])
    {
        // Get members who paid late (after due date)
        $latePayments = GroupSaving::where('fiscal_year_id', $fiscalYearId)
            ->where('month', $month)
            ->where('status', 'paid')
            ->whereHas('deposit', function($query) use ($month) {
                // Assuming deposits have a deposit_date and due_date is 5th of the month
                $query->where('deposit_date', '>', Carbon::create()->month($month)->day(5));
            })
            ->with(['member', 'deposit'])
            ->get();

        $results = [
            'applied' => 0,
            'skipped' => 0,
            'total_amount' => 0,
            'members' => []
        ];

        foreach ($latePayments as $payment) {
            // Check if fine already exists
            $existingFine = Fine::where('member_id', $payment->member_id)
                ->where('fiscal_year_id', $fiscalYearId)
                ->where('month', $month)
                ->where('reason', 'late_payment')
                ->first();

            if ($existingFine) {
                $results['skipped']++;
                continue;
            }

            $fineAmount = $this->calculateLatePaymentFine($payment, $options);
            
            if (!($options['dry_run'] ?? false)) {
                Fine::create([
                    'member_id' => $payment->member_id,
                    'fiscal_year_id' => $fiscalYearId,
                    'month' => $month,
                    'amount' => $fineAmount,
                    'reason' => 'late_payment',
                    'description' => "Late payment fine for " . Carbon::create()->month($month)->format('F'),
                    'status' => 'pending',
                    'created_by' => $options['created_by'] ?? 1
                ]);
            }

            $results['applied']++;
            $results['total_amount'] += $fineAmount;
        }

        return $results;
    }

    private function calculateFineAmount($member, $month, $fiscalYear, $options = [])
    {
        $baseAmount = $options['base_amount'] ?? 10000;
        
        // Check member's fine history for progressive penalties
        $previousMissedCount = Fine::where('member_id', $member->id)
            ->where('reason', 'missed_saving')
            ->where('status', '!=', 'paid')
            ->count();
        
        // Progressive fine amounts
        if ($previousMissedCount >= 6) {
            $baseAmount = $options['high_penalty'] ?? 20000;
        } elseif ($previousMissedCount >= 3) {
            $baseAmount = $options['medium_penalty'] ?? 15000;
        }

        // Apply member-specific adjustments
        if ($member->is_vip ?? false) {
            $baseAmount *= 0.5; // 50% discount for VIP members
        }

        return $baseAmount;
    }

    private function calculateLatePaymentFine($payment, $options = [])
    {
        $baseAmount = $options['late_payment_base'] ?? 5000;
        
        // Calculate days late
        $dueDate = Carbon::create()->month($payment->month)->day(5);
        $paymentDate = $payment->deposit->deposit_date;
        $daysLate = $paymentDate->diffInDays($dueDate);
        
        // Add penalty per day late
        $dailyPenalty = $options['daily_penalty'] ?? 500;
        $totalPenalty = $baseAmount + ($daysLate * $dailyPenalty);
        
        // Cap the maximum fine
        $maxFine = $options['max_late_fine'] ?? 25000;
        return min($totalPenalty, $maxFine);
    }

    public function getFineStatistics($fiscalYearId, $memberId = null)
    {
        $query = Fine::where('fiscal_year_id', $fiscalYearId);
        
        if ($memberId) {
            $query->where('member_id', $memberId);
        }

        $fines = $query->get();

        return [
            'total_fines' => $fines->count(),
            'total_amount' => $fines->sum('amount'),
            'pending_fines' => $fines->where('status', 'pending')->count(),
            'pending_amount' => $fines->where('status', 'pending')->sum('amount'),
            'paid_fines' => $fines->where('status', 'paid')->count(),
            'paid_amount' => $fines->where('status', 'paid')->sum('amount'),
            'waived_fines' => $fines->where('status', 'waived')->count(),
            'waived_amount' => $fines->where('status', 'waived')->sum('amount'),
            'collection_rate' => $fines->count() > 0 ? 
                round(($fines->where('status', 'paid')->count() / $fines->count()) * 100, 2) : 0,
            'average_fine_amount' => $fines->count() > 0 ? 
                round($fines->sum('amount') / $fines->count(), 0) : 0,
        ];
    }

    public function generateFineSchedule($fiscalYearId)
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        $schedule = [];

        for ($month = 1; $month <= 12; $month++) {
            // Check if month is within fiscal year period
            $monthDate = Carbon::create()->month($month);
            if ($monthDate < $fiscalYear->start_date || $monthDate > $fiscalYear->end_date) {
                continue;
            }

            $schedule[$month] = [
                'month_name' => $monthDate->format('F'),
                'expected_fines' => $this->estimateExpectedFines($fiscalYearId, $month),
                'current_fines' => Fine::where('fiscal_year_id', $fiscalYearId)
                    ->where('month', $month)
                    ->count(),
                'pending_amount' => Fine::where('fiscal_year_id', $fiscalYearId)
                    ->where('month', $month)
                    ->where('status', 'pending')
                    ->sum('amount'),
            ];
        }

        return $schedule;
    }

    private function estimateExpectedFines($fiscalYearId, $month)
    {
        // Estimate based on historical data or member count
        $totalMembers = Member::count();
        $historicalFineRate = 0.1; // Assume 10% of members typically miss payments
        
        return round($totalMembers * $historicalFineRate);
    }
}
