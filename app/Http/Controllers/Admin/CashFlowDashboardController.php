<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CashFlowDashboardService;
use App\Models\FiscalYear;
use App\Models\Deposit;
use App\Models\LoanRepayment;
use App\Models\CashflowTransaction;
use App\Models\CashFlow;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashFlowDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(CashFlowDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the cash flow dashboard
     */
    public function index(Request $request): View
    {
        $fiscalYearId = $request->get('fiscal_year_id');
        $month = $request->get('month');

        $dashboardData = $this->dashboardService->getDashboardData($fiscalYearId, $month);

        // Add recent business transactions
        $fiscalYear = FiscalYear::find($fiscalYearId);

        $recentDeposits = Deposit::where('fiscal_year_id', $fiscalYear->id)
            ->with('member')
            ->orderBy('deposit_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($deposit) {
                return [
                    'id' => 'DEP-' . $deposit->id,
                    'date' => $deposit->deposit_date->format('Y-m-d'),
                    'description' => 'Member Deposit - ' . $deposit->member->first_name . ' ' . $deposit->member->last_name,
                    'transaction_type' => 'INFLOW',
                    'category' => 'OPERATING',
                    'subcategory' => 'Member Deposits',
                    'amount' => (float) $deposit->amount,
                    'reference_type' => 'DEPOSIT',
                    'reference_number' => $deposit->id,
                    'member_info' => [
                        'name' => $deposit->member->first_name . ' ' . $deposit->member->last_name,
                        'id' => $deposit->member->id,
                    ],
                    'status' => 'CLEARED',
                    'source_module' => 'Savings',
                ];
            })
            ->toArray();

        $recentRepayments = LoanRepayment::whereHas('loan', function($query) use ($fiscalYear) {
                $query->where('fiscal_year_id', $fiscalYear->id);
            })
            ->with('loan.member')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($repayment) {
                return [
                    'id' => 'REP-' . $repayment->id,
                    'date' => $repayment->payment_date->format('Y-m-d'),
                    'description' => 'Loan Repayment - ' . $repayment->loan->member->first_name . ' ' . $repayment->loan->member->last_name,
                    'transaction_type' => 'INFLOW',
                    'category' => 'FINANCING',
                    'subcategory' => 'Loan Repayments',
                    'amount' => (float) $repayment->payment_amount,
                    'reference_type' => 'LOAN_REPAYMENT',
                    'reference_number' => $repayment->loan_id,
                    'member_info' => [
                        'name' => $repayment->loan->member->first_name . ' ' . $repayment->loan->member->last_name,
                        'id' => $repayment->loan->member->id,
                    ],
                    'status' => 'CLEARED',
                    'source_module' => 'Loans',
                ];
            })
            ->toArray();

        $dashboardData['recentDeposits'] = $recentDeposits;
        $dashboardData['recentRepayments'] = $recentRepayments;

        return view('admin.cashflow.dashboard', compact('dashboardData'));
    }

    /**
     * Get dashboard data via AJAX for real-time updates
     */
    public function getDashboardData(Request $request)
    {
        $fiscalYearId = $request->get('fiscal_year_id');
        $month = $request->get('month');

        $dashboardData = $this->dashboardService->getDashboardData($fiscalYearId, $month);

        // Add pending count from comprehensive sources
        $pendingCount = \App\Models\CashflowTransaction::where('status', 'PENDING')->count() +
                       \App\Models\CashFlow::where('status', 'pending')->count() +
                       \App\Models\Fine::where('status', 'pending')->count();

        return response()->json([
            'success' => true,
            'data' => $dashboardData,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Get available fiscal years for dropdown
     */
    public function getFiscalYears()
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $fiscalYears,
        ]);
    }

    /**
     * Get available months for selected fiscal year
     */
    public function getMonths(Request $request)
    {
        $fiscalYearId = $request->get('fiscal_year_id');
        $fiscalYear = FiscalYear::find($fiscalYearId);
        
        if (!$fiscalYear) {
            return response()->json([
                'success' => false,
                'message' => 'Fiscal year not found',
            ]);
        }

        $dashboardService = new CashFlowDashboardService();
        $months = $dashboardService->getAvailableMonths($fiscalYear);

        return response()->json([
            'success' => true,
            'data' => $months,
        ]);
    }
}
