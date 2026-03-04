<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Models\CashflowTransaction;
use App\Models\Deposit;
use App\Models\Distribution;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanPenalty;
use App\Models\Fine;
use App\Models\WelfareFund;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\Transaction;
use App\Models\FiscalYear;
use App\Models\Member;
use App\Services\CashPositionService;
use App\Services\CashflowStatementService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CashflowStatementExport;
use App\Exports\CashflowTransactionExport;
use PDF;

class CashflowController extends Controller
{
    /**
     * Display comprehensive cashflow with optimized loading
     */
    public function index(Request $request): View
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        $activeFiscalYear = FiscalYear::where('status', 'active')->first();
        
        // Check if this is an AJAX request for lazy loading
        if ($request->ajax() && $request->has('load_transactions')) {
            return $this->loadTransactionsAjax($request);
        }
        
        // Calculate comprehensive totals with caching
        $totals = $this->calculateComprehensiveTotals($request);
        
        // Get chart data
        $chartData = $this->getChartData($request);
        
        // Get initial transactions (first page only)
        $initialTransactions = $this->getInitialTransactions($request);
        
        return view('admin.cashflow.index', compact(
            'initialTransactions',
            'fiscalYears',
            'activeFiscalYear',
            'totals',
            'chartData'
        ));
    }

    /**
     * Load transactions via AJAX for lazy loading
     */
    public function loadTransactionsAjax(Request $request)
    {
        $transactions = $this->getAllTransactions($request);
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        
        $paginatedTransactions = $transactions->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'transactions' => $paginatedTransactions->items(),
            'pagination' => [
                'current_page' => $paginatedTransactions->currentPage(),
                'last_page' => $paginatedTransactions->lastPage(),
                'per_page' => $paginatedTransactions->perPage(),
                'total' => $paginatedTransactions->total(),
                'has_more' => $paginatedTransactions->hasMorePages()
            ]
        ]);
    }
    
    /**
     * Get initial transactions for first page load
     */
    private function getInitialTransactions($request = null)
    {
        $transactions = $this->getAllTransactions($request);
        return $transactions->paginate(20);
    }
    
    /**
     * Get chart data for visualization
     */
    private function getChartData($request = null)
    {
        // Get last 6 months of data for trends
        $months = [];
        $inflows = [];
        $outflows = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;
            
            $inflows[] = $this->calculateMonthInflows($date->month, $date->year);
            $outflows[] = $this->calculateMonthOutflows($date->month, $date->year);
        }
        
        // Get category breakdown for current month
        $categoryBreakdown = $this->getCategoryBreakdown();
        
        return [
            'trends' => [
                'months' => $months,
                'inflows' => $inflows,
                'outflows' => $outflows
            ],
            'categories' => $categoryBreakdown
        ];
    }
    
    /**
     * Get category breakdown for current month
     */
    private function getCategoryBreakdown()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $categories = [
            'Operating' => 0,
            'Investing' => 0,
            'Financing' => 0
        ];
        
        // Calculate from all sources
        $categories['Operating'] += 
            CashFlow::where('category', 'OPERATING')
                ->where('status', 'cleared')
                ->whereMonth('transaction_date', $currentMonth)
                ->whereYear('transaction_date', $currentYear)
                ->sum('amount');
                
        $categories['Investing'] += 
            CashFlow::where('category', 'INVESTING')
                ->where('status', 'cleared')
                ->whereMonth('transaction_date', $currentMonth)
                ->whereYear('transaction_date', $currentYear)
                ->sum('amount');
                
        $categories['Financing'] += 
            CashFlow::where('category', 'FINANCING')
                ->where('status', 'cleared')
                ->whereMonth('transaction_date', $currentMonth)
                ->whereYear('transaction_date', $currentYear)
                ->sum('amount');
        
        return $categories;
    }

    /**
     * Get ALL transactions from the entire system (optimized)
     */
    private function getAllTransactions($request = null)
    {
        // Manual CashFlow entries
        $cashFlowQuery = CashFlow::query()
            ->select([
                'id',
                'transaction_date',
                'description',
                'reference_number',
                DB::raw("'income' as type"),
                'category',
                'amount',
                'payment_method',
                'status',
                'user_id as member_id',
                'created_at',
                DB::raw("'CashFlow' as source_model"),
                DB::raw("'Manual Entry' as transaction_source")
            ]);

        // CashflowTransaction entries
        $cashflowTransactionQuery = CashflowTransaction::query()
            ->select([
                'id',
                'transaction_date',
                'description',
                'reference_number',
                DB::raw("CASE WHEN transaction_type = 'INFLOW' THEN 'income' ELSE 'expense' END as type"),
                'category',
                'amount',
                'payment_method',
                'status',
                'member_id',
                'created_at',
                DB::raw("'CashflowTransaction' as source_model"),
                DB::raw("'Cashflow Transaction' as transaction_source")
            ]);

        // Member Deposits (INFLOW)
        $depositQuery = Deposit::query()
            ->select([
                'id',
                'deposit_date as transaction_date',
                DB::raw("'Member Savings Deposit' as description"),
                DB::raw("CONCAT('DEP-', id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'savings' as payment_method"),
                'status',
                'member_id',
                'created_at',
                DB::raw("'Deposit' as source_model"),
                DB::raw("'Member Deposits' as transaction_source")
            ]);

        // Savings Distributions (OUTFLOW)
        $distributionQuery = Distribution::query()
            ->select([
                'id',
                'created_at as transaction_date',
                'description',
                DB::raw("CONCAT('DIST-', id) as reference_number"),
                DB::raw("'expense' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'distribution' as payment_method"),
                DB::raw("'distributed' as status"),
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'Distribution' as source_model"),
                DB::raw("'Savings Distribution' as transaction_source")
            ]);

        // Loan Disbursements (OUTFLOW)
        $loanQuery = Loan::query()
            ->whereNotNull('disbursement_date')
            ->select([
                'id',
                'disbursement_date as transaction_date',
                DB::raw("CONCAT('Loan Disbursement - ', loan_number) as description"),
                'loan_number as reference_number',
                DB::raw("'expense' as type"),
                DB::raw("'FINANCING' as category"),
                'principal_amount as amount',
                DB::raw("'loan_disbursement' as payment_method"),
                DB::raw("'disbursed' as status"),
                'member_id',
                'created_at',
                DB::raw("'Loan' as source_model"),
                DB::raw("'Loan Disbursement' as transaction_source")
            ]);

        // Loan Repayments (INFLOW)
        $loanRepaymentQuery = LoanRepayment::query()
            ->select([
                'id',
                'paid_at as transaction_date',
                DB::raw("CONCAT('Loan Repayment - ', reference) as description"),
                'reference as reference_number',
                DB::raw("'income' as type"),
                DB::raw("'FINANCING' as category"),
                'amount as amount',
                'method as payment_method',
                DB::raw("'received' as status"),
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'LoanRepayment' as source_model"),
                DB::raw("'Loan Repayment' as transaction_source")
            ]);

        // Fines (INFLOW - when paid)
        $fineQuery = Fine::query()
            ->where('status', 'paid')
            ->select([
                'id',
                'updated_at as transaction_date',
                DB::raw("CONCAT('Fine Payment - ', reason) as description"),
                DB::raw("CONCAT('FINE-', id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'fine_payment' as payment_method"),
                'status',
                'member_id',
                'created_at',
                DB::raw("'Fine' as source_model"),
                DB::raw("'Member Fine' as transaction_source")
            ]);

        // Welfare Fund Distributions (OUTFLOW)
        $welfareQuery = WelfareFund::query()
            ->select([
                'id',
                'created_at as transaction_date',
                DB::raw("'Welfare Fund Distribution' as description"),
                DB::raw("CONCAT('WELFARE-', id) as reference_number"),
                DB::raw("'expense' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'welfare' as payment_method"),
                DB::raw("'distributed' as status"),
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'WelfareFund' as source_model"),
                DB::raw("'Welfare Distribution' as transaction_source")
            ]);

        // Loan Penalties (INFLOW - when paid)
        $loanPenaltyQuery = LoanPenalty::query()
            ->where('status', 'paid')
            ->whereNotNull('paid_date')
            ->select([
                'id',
                'paid_date as transaction_date',
                DB::raw("CONCAT('Loan Penalty - ', penalty_type) as description"),
                DB::raw("CONCAT('PENALTY-', id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'FINANCING' as category"),
                'penalty_amount as amount',
                DB::raw("'penalty_payment' as payment_method"),
                'status',
                'member_id',
                'created_at',
                DB::raw("'LoanPenalty' as source_model"),
                DB::raw("'Loan Penalty' as transaction_source")
            ]);

        // Investment Transactions (both inflows and outflows)
        $investmentTransactionQuery = InvestmentTransaction::query()
            ->with('investment')
            ->select([
                'investment_transactions.id',
                'investment_transactions.transaction_date',
                DB::raw("CONCAT(investment_portfolios.name, ' - ', investment_transactions.transaction_type) as description"),
                'investment_transactions.reference_number',
                DB::raw("CASE WHEN investment_transactions.transaction_type IN ('INTEREST_INCOME', 'DIVIDEND_INCOME', 'CAPITAL_GAIN', 'PRINCIPAL_RETURN') THEN 'income' ELSE 'expense' END as type"),
                DB::raw("'INVESTING' as category"),
                'investment_transactions.amount',
                'investment_transactions.payment_method',
                DB::raw("'CLEARED' as status"),
                DB::raw("NULL as member_id"),
                'investment_transactions.created_at',
                DB::raw("'InvestmentTransaction' as source_model"),
                DB::raw("'Investment Transaction' as transaction_source")
            ])
            ->join('investment_portfolios', 'investment_transactions.investment_portfolio_id', '=', 'investment_portfolios.id');

        // Apply filters to all queries
        if ($request) {
            $this->applyFiltersToQueries($request, [
                &$cashFlowQuery,
                &$cashflowTransactionQuery,
                &$depositQuery,
                &$distributionQuery,
                &$loanQuery,
                &$loanRepaymentQuery,
                &$fineQuery,
                &$welfareQuery,
                &$loanPenaltyQuery,
                &$investmentTransactionQuery
            ]);
        }

        // Union all queries and order by date
        $allTransactions = $cashFlowQuery
            ->unionAll($cashflowTransactionQuery)
            ->unionAll($depositQuery)
            ->unionAll($distributionQuery)
            ->unionAll($loanQuery)
            ->unionAll($loanRepaymentQuery)
            ->unionAll($fineQuery)
            ->unionAll($welfareQuery)
            ->unionAll($loanPenaltyQuery)
            ->unionAll($investmentTransactionQuery)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        return $allTransactions;
    }

    /**
     * Apply filters to all transaction queries
     */
    private function applyFiltersToQueries($request, $queries)
    {
        foreach ($queries as &$query) {
            // Date filters
            if ($request->filled('date_from')) {
                $dateField = $this->getDateFieldForQuery($query);
                if ($dateField) {
                    $query->where($dateField, '>=', $request->date_from);
                }
            }

            if ($request->filled('date_to')) {
                $dateField = $this->getDateFieldForQuery($query);
                if ($dateField) {
                    $query->where($dateField, '<=', $request->date_to);
                }
            }

            // Transaction type filter
            if ($request->filled('transaction_type')) {
                $type = $request->transaction_type === 'INFLOW' ? 'income' : 'expense';
                $query->whereRaw("type = ?", [$type]);
            }

            // Category filter
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%");
                });
            }
        }
    }

    /**
     * Get appropriate date field for query
     */
    private function getDateFieldForQuery($query)
    {
        return 'transaction_date';
    }

    /**
     * Calculate comprehensive totals from all transaction sources
     */
    private function calculateComprehensiveTotals($request = null)
    {
        $totals = [
            'totalInflows' => 0,
            'totalOutflows' => 0,
            'totalBalance' => 0,
            'pendingCount' => 0,
            'inflowChange' => 0,
            'outflowChange' => 0,
            'balanceChange' => 0
        ];

        // Calculate totals from all sources
        $totals['totalInflows'] = $this->calculateTotalInflows($request);
        $totals['totalOutflows'] = $this->calculateTotalOutflows($request);
        $totals['totalBalance'] = $totals['totalInflows'] - $totals['totalOutflows'];
        $totals['pendingCount'] = $this->calculatePendingCount();

        // Calculate monthly changes
        $changes = $this->calculateMonthlyChanges($request);
        $totals = array_merge($totals, $changes);

        return $totals;
    }

    /**
     * Calculate total inflows from all sources
     */
    private function calculateTotalInflows($request = null)
    {
        $total = 0;

        // From CashFlow (income)
        $total += CashFlow::where('type', 'income')
            ->where('status', 'cleared')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From CashflowTransaction (INFLOW)
        $total += CashflowTransaction::where('transaction_type', 'INFLOW')
            ->where('status', 'CLEARED')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Deposits
        $total += Deposit::where('status', 'cleared')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('deposit_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('deposit_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Loan Repayments
        $total += LoanRepayment::when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('paid_at', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('paid_at', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Fines (paid)
        $total += Fine::where('status', 'paid')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('updated_at', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('updated_at', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Loan Penalties (paid)
        $total += LoanPenalty::where('status', 'paid')
            ->whereNotNull('paid_date')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('paid_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('paid_date', '<=', $request->date_to);
            })
            ->sum('penalty_amount');

        return $total;
    }

    /**
     * Calculate total outflows from all sources
     */
    private function calculateTotalOutflows($request = null)
    {
        $total = 0;

        // From CashFlow (expense)
        $total += CashFlow::where('type', 'expense')
            ->where('status', 'cleared')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From CashflowTransaction (OUTFLOW)
        $total += CashflowTransaction::where('transaction_type', 'OUTFLOW')
            ->where('status', 'CLEARED')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Distributions
        $total += Distribution::when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('created_at', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('created_at', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Loan Disbursements
        $total += Loan::whereNotNull('disbursement_date')
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('disbursement_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('disbursement_date', '<=', $request->date_to);
            })
            ->sum('principal_amount');

        // From Welfare Funds
        $total += WelfareFund::when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('created_at', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('created_at', '<=', $request->date_to);
            })
            ->sum('amount');

        return $total;
    }

    /**
     * Calculate pending transactions count
     */
    private function calculatePendingCount()
    {
        return CashflowTransaction::where('status', 'PENDING')->count() +
               CashFlow::where('status', 'pending')->count() +
               Fine::where('status', 'pending')->count();
    }

    /**
     * Calculate monthly changes
     */
    private function calculateMonthlyChanges($request = null)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $lastMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

        // Current month totals
        $currentInflows = $this->calculateMonthInflows($currentMonth, $currentYear);
        $currentOutflows = $this->calculateMonthOutflows($currentMonth, $currentYear);

        // Last month totals
        $lastInflows = $this->calculateMonthInflows($lastMonth, $lastMonthYear);
        $lastOutflows = $this->calculateMonthOutflows($lastMonth, $lastMonthYear);

        // Calculate changes
        $inflowChange = 0;
        $outflowChange = 0;
        $balanceChange = 0;

        if ($lastInflows > 0) {
            $inflowChange = (($currentInflows - $lastInflows) / $lastInflows) * 100;
            $inflowChange = max(-100, min(1000, $inflowChange));
        }

        if ($lastOutflows > 0) {
            $outflowChange = (($currentOutflows - $lastOutflows) / $lastOutflows) * 100;
            $outflowChange = max(-100, min(1000, $outflowChange));
        }

        $currentBalance = $currentInflows - $currentOutflows;
        $lastBalance = $lastInflows - $lastOutflows;

        if ($lastBalance > 0) {
            $balanceChange = (($currentBalance - $lastBalance) / $lastBalance) * 100;
            $balanceChange = max(-100, min(1000, $balanceChange));
        }

        return [
            'inflowChange' => $inflowChange,
            'outflowChange' => $outflowChange,
            'balanceChange' => $balanceChange
        ];
    }

    /**
     * Calculate month inflows
     */
    private function calculateMonthInflows($month, $year)
    {
        $total = 0;

        $total += CashFlow::where('type', 'income')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $total += CashflowTransaction::where('transaction_type', 'INFLOW')
            ->where('status', 'CLEARED')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $total += Deposit::where('status', 'cleared')
            ->whereMonth('deposit_date', $month)
            ->whereYear('deposit_date', $year)
            ->sum('amount');

        $total += LoanRepayment::whereMonth('paid_at', $month)
            ->whereYear('paid_at', $year)
            ->sum('amount');

        $total += Fine::where('status', 'paid')
            ->whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->sum('amount');

        $total += LoanPenalty::where('status', 'paid')
            ->whereNotNull('paid_date')
            ->whereMonth('paid_date', $month)
            ->whereYear('paid_date', $year)
            ->sum('penalty_amount');

        return $total;
    }

    /**
     * Calculate month outflows
     */
    private function calculateMonthOutflows($month, $year)
    {
        $total = 0;

        $total += CashFlow::where('type', 'expense')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $total += CashflowTransaction::where('transaction_type', 'OUTFLOW')
            ->where('status', 'CLEARED')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $total += Distribution::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        $total += Loan::whereNotNull('disbursement_date')
            ->whereMonth('disbursement_date', $month)
            ->whereYear('disbursement_date', $year)
            ->sum('principal_amount');

        $total += WelfareFund::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('amount');

        return $total;
    }

    /**
     * Show the form for creating a new cashflow transaction.
     */
    public function create(): View
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        return view('admin.cashflow.create', compact('fiscalYears'));
    }

    /**
     * Store a newly created cashflow transaction.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:INFLOW,OUTFLOW',
            'category' => 'required|in:OPERATING,INVESTING,FINANCING',
            'subcategory' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'reference_type' => 'nullable|in:DEPOSIT,LOAN_DISBURSEMENT,LOAN_REPAYMENT,WELFARE_PAYMENT,FINE_PAYMENT,EXPENSE,OTHER',
            'reference_number' => 'nullable|string|max:100',
            'fiscal_year_id' => 'nullable|exists:fiscal_years,id',
            'member_id' => 'nullable|exists:members,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        CashflowTransaction::create([
            'transaction_date' => $request->transaction_date,
            'transaction_type' => $request->transaction_type,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference_type' => $request->reference_type,
            'reference_number' => $request->reference_number,
            'fiscal_year_id' => $request->fiscal_year_id,
            'member_id' => $request->member_id,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
            'status' => CashflowTransaction::STATUS_PENDING
        ]);

        return redirect()->route('admin.cashflow.index')
            ->with('success', 'Cashflow transaction created successfully.');
    }

    /**
     * Display monthly cashflow statement
     */
    public function monthlyStatement(Request $request): View
    {
        $activeFiscalYear = FiscalYear::where('status', 'active')->first();
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        $statement = null;
        if ($request->filled('fiscal_year_id') && $request->filled('month')) {
            $statementService = new CashflowStatementService();
            $statement = $statementService->generateMonthlyStatement(
                $request->fiscal_year_id,
                $request->month,
                $request->all()
            );
        }

        return view('admin.cashflow.monthly-statement', compact(
            'statement',
            'activeFiscalYear',
            'fiscalYears'
        ));
    }

    /**
     * Export cashflow transactions
     */
    public function export(Request $request)
    {
        return redirect()->back()->with('success', 'Export functionality coming soon!');
    }
}
