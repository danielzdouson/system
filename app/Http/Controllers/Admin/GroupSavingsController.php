<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FiscalYear;
use App\Models\Deposit;
use App\Models\Distribution;
use App\Models\GroupSaving;
use App\Models\WelfareFund;
use App\Models\Fine;
use App\Models\MemberAccount;
use App\Models\Member;
use Carbon\Carbon;

class GroupSavingsController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get selected fiscal year from URL or auto-detect current year
        $selectedYear = $request->get('fiscal_year');
        $currentDate = now();
        
        // Store selected fiscal year in session for monthly view
        if ($selectedYear) {
            session(['selected_fiscal_year' => $selectedYear]);
        }
        
        // Auto-detect current fiscal year if none selected
        if (!$selectedYear) {
            $activeFiscalYear = FiscalYear::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->orderBy('start_date', 'desc')
                ->first();
            // Store auto-detected fiscal year in session
            if ($activeFiscalYear) {
                session(['selected_fiscal_year' => $activeFiscalYear->id]);
            }
        } else {
            $activeFiscalYear = FiscalYear::find($selectedYear);
        }
        
        // Get all fiscal years for selector, ordered by start date
        $allFiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        if (!$activeFiscalYear) {
            return view('admin.group-savings.dashboard', [
                'activeFiscalYear' => null,
                'allFiscalYears' => $allFiscalYears,
                'selectedYear' => $selectedYear,
                'currentDate' => $currentDate,
                'totalDeposits' => 0,
                'totalSavings' => 0,
                'totalWelfare' => 0,
                'totalFines' => 0,
                'pendingMonthsCount' => 0,
                'unpaidFinesCount' => 0,
            ]);
        }

        // Apply automatic fines for missed savings
        $this->applyAutomaticFines($activeFiscalYear);

        // Check if fiscal year has ended and auto-generate CSV if needed
        $this->generateYearEndCSV($activeFiscalYear);

        return view('admin.group-savings.dashboard', [
            'activeFiscalYear' => $activeFiscalYear,
            'allFiscalYears' => $allFiscalYears,
            'selectedYear' => $selectedYear,
            'currentDate' => $currentDate,
            'totalDeposits' => $activeFiscalYear->total_deposits,
            'totalSavings' => $activeFiscalYear->total_savings,
            'totalWelfare' => $activeFiscalYear->total_welfare,
            'totalFines' => $activeFiscalYear->total_fines,
            'pendingMonthsCount' => $activeFiscalYear->pending_months_count,
            'unpaidFinesCount' => $activeFiscalYear->fines()->where('status', 'pending')->count(),
        ]);
    }

    public function exportMonthCSV($fiscalYearId, $month)
    {
        $fiscalYear = FiscalYear::find($fiscalYearId);
        if (!$fiscalYear) {
            return back()->with('error', 'Fiscal year not found');
        }

        // Get monthly data
        $monthlyData = $this->getMonthlyDataForCSV($fiscalYear, $month);
        
        // Generate CSV
        $filename = "savings_{$fiscalYear->name}_month_{$month}.csv";
        
        $callback = function() use ($monthlyData, $filename) {
            $file = fopen('php://output', 'w');
            
            // Headers
            $headers = [
                'Member Name', 'Member Number', 'Deposit Amount', 'Savings Amount', 
                'Welfare Amount', 'Fines Amount', 'Other Amount', 'Current Balance', 'Status'
            ];
            fputcsv($file, $headers);
            
            // Data rows
            foreach ($monthlyData as $data) {
                fputcsv($file, [
                    $data['member_name'],
                    $data['member_number'],
                    $data['deposit_amount'],
                    $data['savings_amount'],
                    $data['welfare_amount'],
                    $data['fines_amount'],
                    $data['other_amount'],
                    $data['current_balance'],
                    $data['status']
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    private function generateYearEndCSV($fiscalYear)
    {
        // Check if fiscal year has ended and CSV hasn't been generated
        if ($fiscalYear->end_date < now() && !$fiscalYear->csv_generated) {
            // Generate CSV for all months
            for ($month = 1; $month <= 12; $month++) {
                $this->exportMonthCSV($fiscalYear->id, $month);
            }
            
            // Mark as generated
            $fiscalYear->update(['csv_generated' => true]);
        }
    }

    private function getMonthlyDataForCSV($fiscalYear, $month)
    {
        // Get all deposits for this month
        $deposits = Deposit::where('fiscal_year_id', $fiscalYear->id)
            ->where('month', $month)
            ->with(['member', 'distributions'])
            ->get();

        $monthlyData = [];
        foreach ($deposits as $deposit) {
            // Check if member exists before accessing properties
            $memberName = $deposit->member ? $deposit->member->first_name . ' ' . $deposit->member->last_name : 'Unknown Member';
            $memberNumber = $deposit->member ? $deposit->member->membership_number : 'N/A';
            
            $monthlyData[] = [
                'member_name' => $memberName,
                'member_number' => $memberNumber,
                'deposit_amount' => $deposit->amount,
                'savings_amount' => $deposit->distributions ? $deposit->distributions()->where('type', 'savings')->sum('amount') : 0,
                'welfare_amount' => $deposit->distributions ? $deposit->distributions()->where('type', 'welfare')->sum('amount') : 0,
                'fines_amount' => $deposit->distributions ? $deposit->distributions()->where('type', 'fines')->sum('amount') : 0,
                'other_amount' => $deposit->distributions ? $deposit->distributions()->where('type', 'other')->sum('amount') : 0,
                'current_balance' => $deposit->balance,
                'status' => $deposit->status ?? 'active'
            ];
        }

        return $monthlyData;
    }

    public function finesIndex()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No active fiscal year found');
        }

        // Apply automatic fines first
        $this->applyAutomaticFines($activeFiscalYear);

        $fines = Fine::where('fiscal_year_id', $activeFiscalYear->id)
            ->with(['member'])
            ->orderBy('status', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Filter out fines with missing members to prevent null errors
        $validFines = $fines->filter(function($fine) {
            return $fine->member || $fine->member_id;
        });

        return view('admin.group-savings.fines', [
            'activeFiscalYear' => $activeFiscalYear,
            'fines' => $validFines,
        ]);
    }

    public function payFine($fineId)
    {
        $fine = Fine::with(['member', 'fiscalYear'])->findOrFail($fineId);
        
        // Mark fine as paid
        $fine->markAsPaid();

        return redirect()->back()->with('success', 'Fine marked as paid successfully');
    }

    public function waiveFine($fineId)
    {
        $fine = Fine::with(['member', 'fiscalYear'])->findOrFail($fineId);
        
        // Waive the fine
        $fine->waive('Waived by administrator');

        return redirect()->back()->with('success', 'Fine waived successfully');
    }

    private function applyAutomaticFines($fiscalYear)
    {
        $currentDate = Carbon::now();
        
        // Get all members
        $members = Member::all();
        
        foreach ($members as $member) {
            // Check each month up to current month
            for ($month = 1; $month <= 12; $month++) {
                // Calculate the deadline for this month (5th of next month)
                $deadlineDate = $this->getMonthDeadline($month, $currentDate->year);
                
                // Only apply fines if deadline has passed
                if ($currentDate->greaterThan($deadlineDate)) {
                    // Check if member has savings for this month
                    $saving = GroupSaving::where('member_id', $member->id)
                        ->where('fiscal_year_id', $fiscalYear->id)
                        ->where('month', $month)
                        ->first();

                    // Check if member has any deposit for this month
                    $hasDeposit = Deposit::where('member_id', $member->id)
                        ->where('fiscal_year_id', $fiscalYear->id)
                        ->where('month', $month)
                        ->exists();

                    // If no savings and no deposit, apply missed saving fine
                    if ((!$saving || $saving->amount == 0) && !$hasDeposit) {
                        // Check if fine already exists for this month
                        $existingFine = Fine::where('member_id', $member->id)
                            ->where('fiscal_year_id', $fiscalYear->id)
                            ->where('month', $month)
                            ->where('reason', 'missed_saving')
                            ->first();

                        if (!$existingFine) {
                            Fine::create([
                                'member_id' => $member->id,
                                'fiscal_year_id' => $fiscalYear->id,
                                'month' => $month,
                                'amount' => 2000,
                                'reason' => 'missed_saving',
                                'description' => 'Automatic fine for missed saving in ' . Carbon::create()->month($month)->format('F') . ' ' . ($month <= 6 ? 2025 : 2024) . ' (Deadline: ' . $deadlineDate->format('M d, Y') . ')',
                                'status' => 'pending',
                                'created_by' => auth()->id(),
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function applyLatePaymentFines($memberId, $fiscalYearId, $month, $depositDate)
    {
        // Convert deposit_date to Carbon object if it's a string
        $depositDate = is_string($depositDate) ? Carbon::parse($depositDate) : $depositDate;
        
        // Calculate the deadline for this month (5th of next month)
        $deadlineDate = $this->getMonthDeadline($month, $depositDate->year);
        
        // Check if deposit was made after deadline
        if ($depositDate->greaterThan($deadlineDate)) {
            // Calculate days late
            $daysLate = $depositDate->diffInDays($deadlineDate);
            
            // Apply late payment fine (UGX 100 per day late, max UGX 5000)
            $lateFineAmount = min(100 * $daysLate, 5000);
            
            // Check if late payment fine already exists
            $existingFine = Fine::where('member_id', $memberId)
                ->where('fiscal_year_id', $fiscalYearId)
                ->where('month', $month)
                ->where('reason', 'late_payment')
                ->first();

            if (!$existingFine) {
                Fine::create([
                    'member_id' => $memberId,
                    'fiscal_year_id' => $fiscalYearId,
                    'month' => $month,
                    'amount' => $lateFineAmount,
                    'reason' => 'late_payment',
                    'description' => 'Late payment fine: ' . $daysLate . ' days late (Deadline: ' . $deadlineDate->format('M d, Y') . ', Paid: ' . $depositDate->format('M d, Y') . ')',
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                ]);
            }
        }
    }

    private function getMonthDeadline($month, $year)
    {
        // Deadline is 5th of next month
        if ($month == 12) {
            return Carbon::create($year + 1, 1, 5);
        } else {
            return Carbon::create($year, $month + 1, 5);
        }
    }

    private function getMonthDateInFiscalYear($month, $fiscalYearStartYear)
    {
        // Fiscal year starts in July (month 7)
        if ($month >= 7) {
            // Months July-December belong to the start year
            return Carbon::create($fiscalYearStartYear, $month, 1);
        } else {
            // Months January-June belong to the next year
            return Carbon::create($fiscalYearStartYear + 1, $month, 1);
        }
    }

    private function getPendingMonthsCount($fiscalYear, $currentDate)
    {
        $pendingCount = 0;
        $fiscalYearStart = Carbon::parse($fiscalYear->start_date);
        
        // Check each month in fiscal year up to current date
        for ($month = 1; $month <= 12; $month++) {
            $monthDate = $this->getMonthDateInFiscalYear($month, $fiscalYearStart->year);
            
            // Only count months that are in fiscal year range and have passed
            if ($monthDate->between($fiscalYearStart, $fiscalYear->end_date) && 
                $monthDate->lt($currentDate)) {
                
                // Check if all members have savings for this month
                $totalMembers = Member::count();
                $membersWithSavings = GroupSaving::where('fiscal_year_id', $fiscalYear->id)
                    ->where('month', $month)
                    ->where('amount', '>', 0)
                    ->count();
                
                if ($membersWithSavings < $totalMembers) {
                    $pendingCount++;
                }
            }
        }
        
        return $pendingCount;
    }

    public function monthlyViewWithFiscalYear($month, $fiscalYearId)
    {
        // Store the selected fiscal year in session
        session(['selected_fiscal_year' => $fiscalYearId]);
        
        // Get the specified fiscal year
        $activeFiscalYear = FiscalYear::find($fiscalYearId);
        
        if (!$activeFiscalYear) {
            return redirect()->route('admin.group-savings.dashboard')
                ->with('error', 'Fiscal year not found');
        }

        // Apply automatic fines first
        $this->applyAutomaticFines($activeFiscalYear);

        // Get all deposits for this month
        $deposits = Deposit::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->with(['member', 'distributions'])
            ->get();

        // Get all members who have activity for this month (deposits, savings, or fines)
        $depositMemberIds = Deposit::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->pluck('member_id')
            ->toArray();

        $savingMemberIds = GroupSaving::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->pluck('member_id')
            ->toArray();

        $fineMemberIds = Fine::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->pluck('member_id')
            ->toArray();

        // Get members with available balance but no deposit in current month
        $balanceOnlyMemberIds = MemberAccount::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('current_balance', '>', 0)
            ->whereNotIn('member_id', $depositMemberIds)
            ->pluck('member_id')
            ->toArray();

        $activeMemberIds = array_unique(array_merge($depositMemberIds, $savingMemberIds, $fineMemberIds, $balanceOnlyMemberIds));
        $members = Member::whereIn('id', $activeMemberIds)->orderBy('first_name')->orderBy('last_name')->get();
        $monthlyData = [];

        foreach ($members as $member) {
            $deposit = $deposits->where('member_id', $member->id)->first();
            $saving = GroupSaving::where('member_id', $member->id)
                ->where('fiscal_year_id', $activeFiscalYear->id)
                ->where('month', $month)
                ->first();

            // Get member account for current balance
            $memberAccount = MemberAccount::where('member_id', $member->id)
                ->where('fiscal_year_id', $activeFiscalYear->id)
                ->first();

            // Get unpaid fines for this member and month
            $unpaidFines = Fine::where('member_id', $member->id)
                ->where('fiscal_year_id', $activeFiscalYear->id)
                ->where('month', $month)
                ->where('status', 'pending')
                ->get();

            $monthlyData[] = [
                'member' => $member,
                'deposit' => $deposit,
                'saving' => $saving,
                'savings_amount' => $deposit ? $deposit->distributions()->where('type', 'savings')->sum('amount') : 0,
                'welfare_amount' => $deposit ? $deposit->distributions()->where('type', 'welfare')->sum('amount') : 0,
                'fines_amount' => $deposit ? $deposit->distributions()->where('type', 'fines')->sum('amount') : 0,
                'other_amount' => $deposit ? $deposit->distributions()->where('type', 'other')->sum('amount') : 0,
                'current_balance' => $memberAccount ? $memberAccount->current_balance : 0,
                'unpaid_fines' => $unpaidFines,
                'unpaid_fines_total' => $unpaidFines->sum('amount'),
                'has_balance_but_no_deposit' => !$deposit && $memberAccount && $memberAccount->current_balance > 0,
            ];
        }

        return view('admin.group-savings.monthly', [
            'activeFiscalYear' => $activeFiscalYear,
            'month' => $month,
            'monthName' => $this->getMonthName($month),
            'monthlyData' => $monthlyData,
        ]);
    }

    public function monthlyView($month)
    {
        // Get selected fiscal year from session or auto-detect current
        $selectedYearId = session('selected_fiscal_year');
        if ($selectedYearId) {
            $activeFiscalYear = FiscalYear::find($selectedYearId);
        } else {
            // Auto-detect current fiscal year
            $currentDate = now();
            $activeFiscalYear = FiscalYear::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->orderBy('start_date', 'desc')
                ->first();
        }
        
        if (!$activeFiscalYear) {
            return redirect()->route('admin.group-savings.dashboard')
                ->with('error', 'No fiscal year found');
        }

        // Apply automatic fines first
        $this->applyAutomaticFines($activeFiscalYear);

        // Get all deposits for this month
        $deposits = Deposit::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->with(['member', 'distributions'])
            ->get();

        // Get all members who have activity for this month (deposits, savings, or fines)
        $depositMemberIds = Deposit::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->pluck('member_id')
            ->toArray();

        $savingMemberIds = GroupSaving::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->pluck('member_id')
            ->toArray();

        $fineMemberIds = Fine::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->pluck('member_id')
            ->toArray();

        // Get members with available balance but no deposit in current month
        $balanceOnlyMemberIds = MemberAccount::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('current_balance', '>', 0)
            ->whereNotIn('member_id', $depositMemberIds)
            ->pluck('member_id')
            ->toArray();

        $activeMemberIds = array_unique(array_merge($depositMemberIds, $savingMemberIds, $fineMemberIds, $balanceOnlyMemberIds));
        $members = Member::whereIn('id', $activeMemberIds)->orderBy('first_name')->orderBy('last_name')->get();
        $monthlyData = [];

        foreach ($members as $member) {
            $deposit = $deposits->where('member_id', $member->id)->first();
            $saving = GroupSaving::where('member_id', $member->id)
                ->where('fiscal_year_id', $activeFiscalYear->id)
                ->where('month', $month)
                ->first();

            // Get member account for current balance
            $memberAccount = MemberAccount::where('member_id', $member->id)
                ->where('fiscal_year_id', $activeFiscalYear->id)
                ->first();

            // Get unpaid fines for this member and month
            $unpaidFines = Fine::where('member_id', $member->id)
                ->where('fiscal_year_id', $activeFiscalYear->id)
                ->where('month', $month)
                ->where('status', 'pending')
                ->get();

            $monthlyData[] = [
                'member' => $member,
                'deposit' => $deposit,
                'saving' => $saving,
                'savings_amount' => $deposit ? $deposit->distributions()->where('type', 'savings')->sum('amount') : 0,
                'welfare_amount' => $deposit ? $deposit->distributions()->where('type', 'welfare')->sum('amount') : 0,
                'fines_amount' => $deposit ? $deposit->distributions()->where('type', 'fines')->sum('amount') : 0,
                'other_amount' => $deposit ? $deposit->distributions()->where('type', 'other')->sum('amount') : 0,
                'current_balance' => $memberAccount ? $memberAccount->current_balance : 0,
                'unpaid_fines' => $unpaidFines,
                'unpaid_fines_total' => $unpaidFines->sum('amount'),
                'has_balance_but_no_deposit' => !$deposit && $memberAccount && $memberAccount->current_balance > 0,
            ];
        }

        return view('admin.group-savings.monthly', [
            'activeFiscalYear' => $activeFiscalYear,
            'month' => $month,
            'monthName' => $this->getMonthName($month),
            'monthlyData' => $monthlyData,
        ]);
    }

    public function createDeposit()
    {
        $activeFiscalYear = FiscalYear::getActive();
        $members = Member::all();

        $response = view('admin.group-savings.create-deposit', [
            'activeFiscalYear' => $activeFiscalYear,
            'members' => $members,
        ]);

        return response($response)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function storeDeposit(Request $request)
    {
        // Check for duplicate submission using session token
        $sessionKey = 'deposit_form_submitted_' . $request->member_id . '_' . $request->month;
        if (session($sessionKey)) {
            return redirect()->route('admin.group-savings.dashboard')
                ->with('error', 'Deposit already submitted for this member and month.');
        }

        $request->validate([
            'member_id' => 'required|exists:members,id',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'deposit_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return redirect()->back()->with('error', 'No active fiscal year found');
        }

        // Mark this form as submitted in session
        session([$sessionKey => true]);

        $deposit = Deposit::create([
            'member_id' => $request->member_id,
            'fiscal_year_id' => $activeFiscalYear->id,
            'month' => $request->month,
            'amount' => $request->amount,
            'balance' => $request->amount,
            'status' => 'pending',
            'deposit_date' => $request->deposit_date,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        // Add deposit to member account
        $memberAccount = MemberAccount::getOrCreate($request->member_id, $activeFiscalYear->id);
        $memberAccount->addDeposit($request->amount);

        // Apply late payment fines if deposit is made after the month deadline
        $this->applyLatePaymentFines($request->member_id, $activeFiscalYear->id, $request->month, $request->deposit_date);

        return redirect()->route('admin.group-savings.distribute', $deposit->id)
            ->with('success', 'Deposit created successfully. Please distribute the funds.')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function distributeDeposit($depositId)
    {
        $deposit = Deposit::with(['member', 'distributions'])->findOrFail($depositId);

        // Get member account balance
        $memberAccount = MemberAccount::where('member_id', $deposit->member_id)
            ->where('fiscal_year_id', $deposit->fiscal_year_id)
            ->first();

        // Get unpaid fines for this member and month
        $unpaidFines = Fine::where('member_id', $deposit->member_id)
            ->where('fiscal_year_id', $deposit->fiscal_year_id)
            ->where('month', $deposit->month)
            ->where('status', 'pending')
            ->get();

        $response = view('admin.group-savings.distribute', [
            'deposit' => $deposit,
            'memberAccount' => $memberAccount,
            'unpaidFines' => $unpaidFines,
            'availableBalance' => $memberAccount ? $memberAccount->current_balance : 0,
        ]);

        return response($response)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function storeDistribution(Request $request, $depositId)
    {
        $request->validate([
            'savings_amount' => 'required|numeric|min:0',
            'welfare_amount' => 'required|numeric|min:0',
            'fines_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'distribution_month' => 'required|integer|min:1|max:12',
            'distribution_note' => 'nullable|string|max:255',
        ]);

        $deposit = Deposit::findOrFail($depositId);
        $totalDistribution = $request->savings_amount + $request->welfare_amount + 
                           $request->fines_amount + $request->other_amount;

        // Check member account balance instead of deposit balance
        $memberAccount = MemberAccount::where('member_id', $deposit->member_id)
            ->where('fiscal_year_id', $deposit->fiscal_year_id)
            ->first();

        if (!$memberAccount || $totalDistribution > $memberAccount->current_balance) {
            return redirect()->back()->with('error', 'Total distribution exceeds available account balance');
        }

        // Clear the session token to allow future deposits
        $sessionKey = 'deposit_form_submitted_' . $deposit->member_id . '_' . $deposit->month;
        session()->forget($sessionKey);

        // Get the target month for distribution
        $targetMonth = (int) $request->distribution_month;
        $distributionNote = $request->distribution_note ?? 'Distribution from deposit #' . $deposit->id;

        // Distribute from member account
        $memberAccount->distributeFunds(
            $request->savings_amount,
            $request->welfare_amount,
            $request->fines_amount,
            $request->other_amount
        );

        // Update deposit balance for tracking purposes
        $totalDistribution = $request->savings_amount + $request->welfare_amount + 
                           $request->fines_amount + $request->other_amount;
        
        // Only update deposit balance if it has sufficient funds
        if ($deposit->balance >= $totalDistribution) {
            $deposit->balance -= $totalDistribution;
            $deposit->status = $deposit->balance == 0 ? 'distributed' : 'partial';
            $deposit->save();
        }

        // Create distributions with target month using member account balance
        if ($request->savings_amount > 0) {
            // Create distribution record directly instead of using deposit->distribute()
            Distribution::create([
                'deposit_id' => $deposit->id,
                'type' => 'savings',
                'amount' => $request->savings_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $targetMonth,
                'fiscal_year_id' => $deposit->fiscal_year_id,
            ]);
            $this->updateGroupSaving($deposit->member_id, $deposit->fiscal_year_id, $targetMonth, $request->savings_amount);
        }

        if ($request->welfare_amount > 0) {
            // Create distribution record directly instead of using deposit->distribute()
            Distribution::create([
                'deposit_id' => $deposit->id,
                'type' => 'welfare',
                'amount' => $request->welfare_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $targetMonth,
                'fiscal_year_id' => $deposit->fiscal_year_id,
            ]);
            $this->updateWelfareFund($deposit->fiscal_year_id, $targetMonth, $request->welfare_amount);
        }

        if ($request->fines_amount > 0) {
            // Create distribution record directly instead of using deposit->distribute()
            Distribution::create([
                'deposit_id' => $deposit->id,
                'type' => 'fines',
                'amount' => $request->fines_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $targetMonth,
                'fiscal_year_id' => $deposit->fiscal_year_id,
            ]);
            $this->payFinesFromDistribution($deposit->member_id, $deposit->fiscal_year_id, $targetMonth, $request->fines_amount);
        }

        if ($request->other_amount > 0) {
            // Create distribution record directly instead of using deposit->distribute()
            Distribution::create([
                'deposit_id' => $deposit->id,
                'type' => 'other',
                'amount' => $request->other_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $targetMonth,
                'fiscal_year_id' => $deposit->fiscal_year_id,
            ]);
        }

        return redirect()->route('admin.group-savings.dashboard')
            ->with('success', 'Deposit distributed successfully to ' . \Carbon\Carbon::create()->month($targetMonth)->format('F'));
    }

    private function payFinesFromDistribution($memberId, $fiscalYearId, $month, $amount)
    {
        // Get unpaid fines for this member and month
        $unpaidFines = Fine::where('member_id', $memberId)
            ->where('fiscal_year_id', $fiscalYearId)
            ->where('month', $month)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $remainingAmount = $amount;

        foreach ($unpaidFines as $fine) {
            if ($remainingAmount <= 0) break;

            if ($remainingAmount >= $fine->amount) {
                // Pay the full fine
                $fine->markAsPaid();
                $remainingAmount -= $fine->amount;
            } else {
                // Partial payment - create a note
                $fine->description .= ' (Partial payment: UGX ' . number_format($remainingAmount, 0) . ')';
                $fine->save();
                $remainingAmount = 0;
            }
        }
    }

    public function pendingMonths()
    {
        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return redirect()->route('admin.group-savings.dashboard')
                ->with('error', 'No active fiscal year found');
        }

        // Get all pending savings
        $pendingSavings = GroupSaving::where('fiscal_year_id', $activeFiscalYear->id)
            ->where('status', 'pending')
            ->with('member')
            ->get()
            ->groupBy('member_id');

        return view('admin.group-savings.pending', [
            'activeFiscalYear' => $activeFiscalYear,
            'pendingSavings' => $pendingSavings,
        ]);
    }

    public function applyFines(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|in:missed_saving,late_payment,other',
            'description' => 'nullable|string',
        ]);

        $activeFiscalYear = FiscalYear::getActive();
        
        if (!$activeFiscalYear) {
            return redirect()->back()->with('error', 'No active fiscal year found');
        }

        $fine = Fine::create([
            'member_id' => $request->member_id,
            'fiscal_year_id' => $activeFiscalYear->id,
            'month' => $request->month,
            'amount' => $request->amount,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Fine applied successfully');
    }

    private function updateGroupSaving($memberId, $fiscalYearId, $month, $amount)
    {
        $saving = GroupSaving::firstOrCreate([
            'member_id' => $memberId,
            'fiscal_year_id' => $fiscalYearId,
            'month' => $month,
        ]);

        $saving->markAsPaid($amount);
    }

    private function updateWelfareFund($fiscalYearId, $month, $amount)
    {
        $welfare = WelfareFund::firstOrCreate([
            'fiscal_year_id' => $fiscalYearId,
            'month' => $month,
        ]);

        $welfare->increment('amount', $amount);
    }

    private function getMonthName($month)
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return $months[$month] ?? 'Unknown';
    }

    public function distributeBalance($memberId, $month, $fiscalYearId = null)
    {
        // Get fiscal year from parameter or session
        if ($fiscalYearId) {
            $activeFiscalYear = FiscalYear::find($fiscalYearId);
        } else {
            $selectedYearId = session('selected_fiscal_year');
            $activeFiscalYear = $selectedYearId ? FiscalYear::find($selectedYearId) : FiscalYear::getActive();
        }

        if (!$activeFiscalYear) {
            return redirect()->route('admin.group-savings.dashboard')
                ->with('error', 'No fiscal year found');
        }

        $member = Member::findOrFail($memberId);
        
        // Get member account
        $memberAccount = MemberAccount::where('member_id', $memberId)
            ->where('fiscal_year_id', $activeFiscalYear->id)
            ->first();

        if (!$memberAccount || $memberAccount->current_balance <= 0) {
            return redirect()->back()
                ->with('error', 'No available balance to distribute');
        }

        // Get unpaid fines for this member and month
        $unpaidFines = Fine::where('member_id', $memberId)
            ->where('fiscal_year_id', $activeFiscalYear->id)
            ->where('month', $month)
            ->where('status', 'pending')
            ->get();

        return view('admin.group-savings.distribute-balance', [
            'member' => $member,
            'memberAccount' => $memberAccount,
            'activeFiscalYear' => $activeFiscalYear,
            'month' => $month,
            'monthName' => $this->getMonthName($month),
            'unpaidFines' => $unpaidFines,
            'availableBalance' => $memberAccount->current_balance,
        ]);
    }

    public function storeBalanceDistribution(Request $request, $memberId, $month, $fiscalYearId = null)
    {
        $request->validate([
            'savings_amount' => 'required|numeric|min:0',
            'welfare_amount' => 'required|numeric|min:0',
            'fines_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'distribution_note' => 'nullable|string|max:255',
        ]);

        // Get fiscal year
        if ($fiscalYearId) {
            $activeFiscalYear = FiscalYear::find($fiscalYearId);
        } else {
            $selectedYearId = session('selected_fiscal_year');
            $activeFiscalYear = $selectedYearId ? FiscalYear::find($selectedYearId) : FiscalYear::getActive();
        }

        if (!$activeFiscalYear) {
            return redirect()->back()->with('error', 'No fiscal year found');
        }

        $member = Member::findOrFail($memberId);
        $totalDistribution = $request->savings_amount + $request->welfare_amount + 
                           $request->fines_amount + $request->other_amount;

        // Get member account
        $memberAccount = MemberAccount::where('member_id', $memberId)
            ->where('fiscal_year_id', $activeFiscalYear->id)
            ->first();

        if (!$memberAccount || $totalDistribution > $memberAccount->current_balance) {
            return redirect()->back()->with('error', 'Total distribution exceeds available account balance');
        }

        // Create virtual deposit for tracking
        $virtualDeposit = Deposit::create([
            'member_id' => $memberId,
            'fiscal_year_id' => $activeFiscalYear->id,
            'month' => $month,
            'amount' => 0,
            'balance' => 0,
            'status' => 'virtual',
            'deposit_date' => now(),
            'notes' => 'Virtual deposit for balance distribution',
            'created_by' => auth()->id(),
        ]);

        // Distribute from member account
        $memberAccount->distributeFunds(
            $request->savings_amount,
            $request->welfare_amount,
            $request->fines_amount,
            $request->other_amount
        );

        $distributionNote = $request->distribution_note ?? "Balance distribution for {$this->getMonthName($month)}";

        // Create distributions
        if ($request->savings_amount > 0) {
            Distribution::create([
                'deposit_id' => $virtualDeposit->id,
                'type' => 'savings',
                'amount' => $request->savings_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $month,
                'fiscal_year_id' => $activeFiscalYear->id,
            ]);
            $this->updateGroupSaving($memberId, $activeFiscalYear->id, $month, $request->savings_amount);
        }

        if ($request->welfare_amount > 0) {
            Distribution::create([
                'deposit_id' => $virtualDeposit->id,
                'type' => 'welfare',
                'amount' => $request->welfare_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $month,
                'fiscal_year_id' => $activeFiscalYear->id,
            ]);
            $this->updateWelfareFund($activeFiscalYear->id, $month, $request->welfare_amount);
        }

        if ($request->fines_amount > 0) {
            Distribution::create([
                'deposit_id' => $virtualDeposit->id,
                'type' => 'fines',
                'amount' => $request->fines_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $month,
                'fiscal_year_id' => $activeFiscalYear->id,
            ]);
            $this->payFinesFromDistribution($memberId, $activeFiscalYear->id, $month, $request->fines_amount);
        }

        if ($request->other_amount > 0) {
            Distribution::create([
                'deposit_id' => $virtualDeposit->id,
                'type' => 'other',
                'amount' => $request->other_amount,
                'description' => $distributionNote,
                'created_by' => auth()->id(),
                'month' => $month,
                'fiscal_year_id' => $activeFiscalYear->id,
            ]);
        }

        return redirect()->route('admin.group-savings.monthly', ['month' => $month])
            ->with('success', 'Balance distributed successfully to ' . $this->getMonthName($month));
    }
}
