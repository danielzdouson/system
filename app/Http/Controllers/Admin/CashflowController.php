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
use App\Services\FiscalYearContext;
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
        // Handle date range parsing
        $this->parseDateRange($request);
        
        // Use global fiscal year context
        $currentFiscalYear = FiscalYearContext::getCurrent();
        $allFiscalYears = FiscalYearContext::getAllForSelector();
        
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
            'allFiscalYears',
            'currentFiscalYear',
            'totals',
            'chartData'
        ));
    }

    /**
     * Parse date range from request
     */
    private function parseDateRange($request)
    {
        if ($request->filled('date_range') && !$request->filled('date_from') && !$request->filled('date_to')) {
            $dateRange = $request->date_range;
            if (strpos($dateRange, ' to ') !== false) {
                [$dateFrom, $dateTo] = explode(' to ', $dateRange);
                $request->merge([
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]);
            } elseif (strpos($dateRange, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $dateRange);
                $request->merge([
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]);
            }
        }
    }

    /**
     * Load transactions via AJAX for lazy loading
     */
    public function loadTransactionsAjax(Request $request)
    {
        // Handle date range parsing for AJAX requests
        $this->parseDateRange($request);
        
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
        // Handle date range parsing for initial load
        $this->parseDateRange($request);
        
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
        // Get current fiscal year for filtering
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // Manual CashFlow entries
        $cashFlowQuery = CashFlow::query();
        
        // Filter by fiscal year if selected
        if ($currentFiscalYear) {
            $cashFlowQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            // Show no data if no fiscal year selected
            $cashFlowQuery->whereRaw('1 = 0');
        }
        
        $cashFlowQuery->select([
                'id',
                'transaction_date',
                'description',
                'reference_number',
                'type',
                'category',
                'amount',
                'payment_method',
                'status',
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'CashFlow' as source_model"),
                DB::raw("'Manual Entry' as transaction_source")
            ]);

        // CashflowTransaction entries (manual entries only - exclude observer-generated)
        // Observer-generated records for DEPOSIT, LOAN_DISBURSEMENT, LOAN_REPAYMENT,
        // DISTRIBUTION, WELFARE_FUND, FINE_PAYMENT, LOAN_PENALTY, INVESTMENT are
        // already covered by their respective source table queries below
        $cashflowTransactionQuery = CashflowTransaction::query();
        
        // Exclude observer-generated records to prevent duplicates
        // These transactions are already included from their source tables
        $cashflowTransactionQuery->whereNotIn('reference_type', [
                'DEPOSIT',
                'LOAN_DISBURSEMENT',
                'LOAN_REPAYMENT',
                'DISTRIBUTION',
                'WELFARE_FUND',
                'FINE_PAYMENT',
                'LOAN_PENALTY',
                'INVESTMENT',
                'OTHER'  // DistributionObserver uses 'OTHER' for distributions
            ]);
        
        // Filter by fiscal year if selected
        if ($currentFiscalYear) {
            $cashflowTransactionQuery->where('cashflow_transactions.fiscal_year_id', $currentFiscalYear->id);
        } else {
            $cashflowTransactionQuery->whereRaw('1 = 0');
        }
        
        // Left join with members to add names when member_id exists
        $cashflowTransactionQuery->leftJoin('members', 'cashflow_transactions.member_id', '=', 'members.id')
            ->select([
                'cashflow_transactions.id',
                'cashflow_transactions.transaction_date',
                DB::raw("CASE 
                    WHEN cashflow_transactions.member_id IS NOT NULL THEN CONCAT(cashflow_transactions.description, ' - ', members.first_name, ' ', members.last_name)
                    ELSE cashflow_transactions.description
                END as description"),
                'cashflow_transactions.reference_number',
                DB::raw("CASE WHEN cashflow_transactions.transaction_type = 'INFLOW' THEN 'income' ELSE 'expense' END as type"),
                'cashflow_transactions.category',
                'cashflow_transactions.amount',
                'cashflow_transactions.payment_method',
                'cashflow_transactions.status',
                'cashflow_transactions.member_id',
                'cashflow_transactions.created_at',
                DB::raw("'CashflowTransaction' as source_model"),
                DB::raw("'Cashflow Transaction' as transaction_source")
            ]);

        // Member Deposits (INFLOW)
        $depositQuery = Deposit::query();
        if ($currentFiscalYear) {
            // Deposits have fiscal_year_id, use it for filtering
            $depositQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            $depositQuery->whereRaw('1 = 0');
        }
        $depositQuery->join('members', 'deposits.member_id', '=', 'members.id')
            ->select([
                'deposits.id',
                'deposit_date as transaction_date',
                DB::raw("CONCAT('Savings Deposit - ', members.first_name, ' ', members.last_name) as description"),
                DB::raw("CONCAT('DEP-', deposits.id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'OPERATING' as category"),
                'deposits.amount',
                DB::raw("'Cash' as payment_method"),
                'deposits.status',
                'deposits.member_id',
                'deposits.created_at',
                DB::raw("'Deposit' as source_model"),
                DB::raw("'Deposit' as transaction_source")
            ]);

        // Savings Distributions (OUTFLOW)
        $distributionQuery = Distribution::query();
        if ($currentFiscalYear) {
            // Distributions have fiscal_year_id, use it for filtering
            $distributionQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            $distributionQuery->whereRaw('1 = 0');
        }
        $distributionQuery->select([
                'id',
                'created_at as transaction_date',
                DB::raw("CONCAT('Distribution - ', type) as description"),
                DB::raw("CONCAT('DIST-', id) as reference_number"),
                DB::raw("'expense' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'Transfer' as payment_method"),
                DB::raw("'distributed' as status"),
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'Distribution' as source_model"),
                DB::raw("'Distribution' as transaction_source")
            ]);

        // Loan Disbursements (OUTFLOW)
        $loanQuery = Loan::query();
        if ($currentFiscalYear) {
            $loanQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            $loanQuery->whereRaw('1 = 0');
        }
        $loanQuery->whereNotNull('disbursement_date')
            ->join('members', 'loans.member_id', '=', 'members.id')
            ->select([
                'loans.id',
                'disbursement_date as transaction_date',
                DB::raw("CONCAT('Loan Disbursement - ', members.first_name, ' ', members.last_name) as description"),
                'loan_number as reference_number',
                DB::raw("'expense' as type"),
                DB::raw("'FINANCING' as category"),
                'principal_amount as amount',
                DB::raw("'Bank Transfer' as payment_method"),
                DB::raw("'disbursed' as status"),
                'loans.member_id',
                'loans.created_at',
                DB::raw("'Loan' as source_model"),
                DB::raw("'Loan' as transaction_source")
            ]);

        // Loan Repayments (INFLOW)
        $loanRepaymentQuery = LoanRepayment::query();
        if ($currentFiscalYear) {
            // LoanRepayments don't have fiscal_year_id, filter by date range
            $loanRepaymentQuery->whereBetween('paid_at', [$currentFiscalYear->start_date, $currentFiscalYear->end_date]);
        } else {
            $loanRepaymentQuery->whereRaw('1 = 0');
        }
        $loanRepaymentQuery->select([
                'id',
                'paid_at as transaction_date',
                DB::raw("'Loan Repayment' as description"),
                'reference as reference_number',
                DB::raw("'income' as type"),
                DB::raw("'FINANCING' as category"),
                'amount as amount',
                'method as payment_method',
                DB::raw("'received' as status"),
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'LoanRepayment' as source_model"),
                DB::raw("'Repayment' as transaction_source")
            ]);

        // Fines (INFLOW - when paid)
        $fineQuery = Fine::query();
        if ($currentFiscalYear) {
            $fineQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            $fineQuery->whereRaw('1 = 0');
        }
        $fineQuery->where('fines.status', 'paid')
            ->join('members', 'fines.member_id', '=', 'members.id')
            ->select([
                'fines.id',
                'fines.updated_at as transaction_date',
                DB::raw("CONCAT('Fine Payment - ', members.first_name, ' ', members.last_name) as description"),
                DB::raw("CONCAT('FINE-', fines.id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'OPERATING' as category"),
                'fines.amount',
                DB::raw("'Cash' as payment_method"),
                'fines.status',
                'fines.member_id',
                'fines.created_at',
                DB::raw("'Fine' as source_model"),
                DB::raw("'Fine' as transaction_source")
            ]);

        // Welfare Fund Distributions (OUTFLOW)
        $welfareQuery = WelfareFund::query();
        if ($currentFiscalYear) {
            // WelfareFunds don't have fiscal_year_id, filter by date range
            $welfareQuery->whereBetween('created_at', [$currentFiscalYear->start_date, $currentFiscalYear->end_date]);
        } else {
            $welfareQuery->whereRaw('1 = 0');
        }
        $welfareQuery->select([
                'id',
                'created_at as transaction_date',
                DB::raw("'Welfare Payment' as description"),
                DB::raw("CONCAT('WELFARE-', id) as reference_number"),
                DB::raw("'expense' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'Cash' as payment_method"),
                DB::raw("'distributed' as status"),
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'WelfareFund' as source_model"),
                DB::raw("'Welfare' as transaction_source")
            ]);

        // Loan Penalties (INFLOW - when paid)
        $loanPenaltyQuery = LoanPenalty::query();
        if ($currentFiscalYear) {
            // LoanPenalties don't have fiscal_year_id, filter by date range
            $loanPenaltyQuery->whereBetween('paid_date', [$currentFiscalYear->start_date, $currentFiscalYear->end_date]);
        } else {
            $loanPenaltyQuery->whereRaw('1 = 0');
        }
        $loanPenaltyQuery->where('loan_penalties.status', 'paid')
            ->whereNotNull('paid_date')
            ->join('members', 'loan_penalties.member_id', '=', 'members.id')
            ->select([
                'loan_penalties.id',
                'paid_date as transaction_date',
                DB::raw("CONCAT('Penalty Payment - ', members.first_name, ' ', members.last_name) as description"),
                DB::raw("CONCAT('PENALTY-', loan_penalties.id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'FINANCING' as category"),
                'penalty_amount as amount',
                DB::raw("'Cash' as payment_method"),
                'loan_penalties.status',
                'loan_penalties.member_id',
                'loan_penalties.created_at',
                DB::raw("'LoanPenalty' as source_model"),
                DB::raw("'Penalty' as transaction_source")
            ]);

        // Investment Transactions (both inflows and outflows)
        $investmentTransactionQuery = InvestmentTransaction::query();
        if ($currentFiscalYear) {
            // InvestmentTransactions don't have fiscal_year_id, filter by date range
            $investmentTransactionQuery->whereBetween('investment_transactions.transaction_date', [$currentFiscalYear->start_date, $currentFiscalYear->end_date]);
        } else {
            $investmentTransactionQuery->whereRaw('1 = 0');
        }
        $investmentTransactionQuery->select([
                'investment_transactions.id',
                'investment_transactions.transaction_date',
                DB::raw("CONCAT(
                    CASE 
                        WHEN investment_transactions.transaction_type = 'INITIAL_INVESTMENT' THEN 'Investment'
                        WHEN investment_transactions.transaction_type = 'ADDITIONAL_INVESTMENT' THEN 'Investment'
                        WHEN investment_transactions.transaction_type = 'INTEREST_INCOME' THEN 'Investment Interest'
                        WHEN investment_transactions.transaction_type = 'DIVIDEND_INCOME' THEN 'Investment Dividend'
                        WHEN investment_transactions.transaction_type = 'CAPITAL_GAIN' THEN 'Investment Gain'
                        WHEN investment_transactions.transaction_type = 'PRINCIPAL_RETURN' THEN 'Investment Return'
                        WHEN investment_transactions.transaction_type = 'WITHDRAWAL' THEN 'Investment Withdrawal'
                        ELSE 'Investment'
                    END,
                    ' - ',
                    investment_portfolios.name
                ) as description"),
                'investment_transactions.reference_number',
                DB::raw("CASE WHEN investment_transactions.transaction_type IN ('INTEREST_INCOME', 'DIVIDEND_INCOME', 'CAPITAL_GAIN', 'PRINCIPAL_RETURN') THEN 'income' ELSE 'expense' END as type"),
                DB::raw("'INVESTING' as category"),
                'investment_transactions.amount',
                DB::raw("'Bank Transfer' as payment_method"),
                DB::raw("'CLEARED' as status"),
                DB::raw("NULL as member_id"),
                'investment_transactions.created_at',
                DB::raw("'InvestmentTransaction' as source_model"),
                DB::raw("'Investment' as transaction_source")
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

        // Union all queries - EXCLUDING CashFlow to prevent duplicates
        // CashFlow table may contain manual duplicates of transactions that are already
        // in their source tables (Deposits, Loans, Fines, etc.)
        $unionQuery = $cashflowTransactionQuery
            ->unionAll($depositQuery)
            ->unionAll($distributionQuery)
            ->unionAll($loanQuery)
            ->unionAll($loanRepaymentQuery)
            ->unionAll($fineQuery)
            ->unionAll($welfareQuery)
            ->unionAll($loanPenaltyQuery)
            ->unionAll($investmentTransactionQuery);

        // Wrap in subquery and apply remaining filters
        $allTransactions = DB::table(DB::raw("({$unionQuery->toSql()}) as combined_transactions"))
            ->setBindings($unionQuery->getBindings())
            ->select([
                'id',
                'transaction_date',
                'description',
                'reference_number',
                'type',
                'category',
                'amount',
                'payment_method',
                'status',
                'member_id',
                'created_at',
                'source_model',
                'transaction_source'
            ]);

        // Apply post-UNION filters for type and category
        $allTransactions = $this->applyPostUnionFilters($request, $allTransactions);

        // Order final result
        $allTransactions = $allTransactions->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        return $allTransactions;
    }

    /**
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Get appropriate date field for query
     */
    private function getDateFieldForQuery($query)
    {
        // Since all queries alias their date fields as 'transaction_date' in the SELECT,
        // and we're filtering before UNION, we need to use the actual field names
        // Let's determine the table from the query class
        $queryClass = get_class($query);
        
        // Extract table name from class name
        if (strpos($queryClass, 'CashFlow') !== false) {
            return 'transaction_date';
        } elseif (strpos($queryClass, 'CashflowTransaction') !== false) {
            return 'transaction_date';
        } elseif (strpos($queryClass, 'Deposit') !== false) {
            return 'deposit_date';
        } elseif (strpos($queryClass, 'Distribution') !== false) {
            return 'created_at';
        } elseif (strpos($queryClass, 'Loan') !== false) {
            return 'disbursement_date';
        } elseif (strpos($queryClass, 'LoanRepayment') !== false) {
            return 'paid_at';
        } elseif (strpos($queryClass, 'Fine') !== false) {
            return 'updated_at';
        } elseif (strpos($queryClass, 'LoanPenalty') !== false) {
            return 'paid_date';
        } elseif (strpos($queryClass, 'InvestmentTransaction') !== false) {
            return 'transaction_date';
        } elseif (strpos($queryClass, 'WelfareFund') !== false) {
            return 'created_at';
        }
        
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
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // If no fiscal year selected, return 0
        if (!$currentFiscalYear) {
            return 0;
        }

        // From CashFlow (income)
        $total += CashFlow::where('type', 'income')
            ->where('status', 'cleared')
            ->where('fiscal_year_id', $currentFiscalYear->id)
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From CashflowTransaction (INFLOW) - exclude observer-generated records
        // to avoid double counting (we already count from source tables)
        $total += CashflowTransaction::where('transaction_type', 'INFLOW')
            ->where('status', 'CLEARED')
            ->where('fiscal_year_id', $currentFiscalYear->id)
            ->whereNotIn('reference_type', [
                'DEPOSIT',
                'LOAN_REPAYMENT',
                'DISTRIBUTION',
                'WELFARE_FUND',
                'FINE_PAYMENT',
                'LOAN_PENALTY'
            ])
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Deposits
        $total += Deposit::where('status', 'cleared')
            ->whereBetween('deposit_date', [$currentFiscalYear->start_date, $currentFiscalYear->end_date])
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('deposit_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('deposit_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Loan Repayments
        $total += LoanRepayment::whereBetween('paid_at', [$currentFiscalYear->start_date, $currentFiscalYear->end_date])
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('paid_at', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('paid_at', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Fines (paid)
        $total += Fine::where('status', 'paid')
            ->where('fiscal_year_id', $currentFiscalYear->id)
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
            ->whereBetween('paid_date', [$currentFiscalYear->start_date, $currentFiscalYear->end_date])
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
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        // If no fiscal year selected, return 0
        if (!$currentFiscalYear) {
            return 0;
        }

        // From CashFlow (expense)
        $total += CashFlow::where('type', 'expense')
            ->where('status', 'cleared')
            ->where('fiscal_year_id', $currentFiscalYear->id)
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From CashflowTransaction (OUTFLOW) - exclude observer-generated records
        // to avoid double counting (we already count from source tables)
        $total += CashflowTransaction::where('transaction_type', 'OUTFLOW')
            ->where('status', 'CLEARED')
            ->where('fiscal_year_id', $currentFiscalYear->id)
            ->whereNotIn('reference_type', [
                'LOAN_DISBURSEMENT',
                'DISTRIBUTION',
                'WELFARE_FUND',
                'INVESTMENT'
            ])
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('transaction_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('transaction_date', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Distributions
        $total += Distribution::whereBetween('created_at', [$currentFiscalYear->start_date, $currentFiscalYear->end_date])
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('created_at', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('created_at', '<=', $request->date_to);
            })
            ->sum('amount');

        // From Loan Disbursements
        $total += Loan::whereNotNull('disbursement_date')
            ->where('fiscal_year_id', $currentFiscalYear->id)
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
                $q->where('disbursement_date', '>=', $request->date_from);
            })
            ->when($request && $request->filled('date_to'), function($q) use ($request) {
                $q->where('disbursement_date', '<=', $request->date_to);
            })
            ->sum('principal_amount');

        // From Welfare Funds
        $total += WelfareFund::whereBetween('created_at', [$currentFiscalYear->start_date, $currentFiscalYear->end_date])
            ->when($request && $request->filled('date_from'), function($q) use ($request) {
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
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        if (!$currentFiscalYear) {
            return 0;
        }
        
        return CashflowTransaction::where('status', 'PENDING')
                   ->where('fiscal_year_id', $currentFiscalYear->id)
                   ->count() +
               CashFlow::where('status', 'pending')
                   ->where('fiscal_year_id', $currentFiscalYear->id)
                   ->count() +
               Fine::where('status', 'pending')
                   ->where('fiscal_year_id', $currentFiscalYear->id)
                   ->count();
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
            ->whereNotIn('reference_type', [
                'DEPOSIT',
                'LOAN_REPAYMENT',
                'DISTRIBUTION',
                'WELFARE_FUND',
                'FINE_PAYMENT',
                'LOAN_PENALTY'
            ])
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
            ->whereNotIn('reference_type', [
                'LOAN_DISBURSEMENT',
                'DISTRIBUTION',
                'WELFARE_FUND',
                'INVESTMENT'
            ])
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
            'reference_type' => $request->reference_type ?? 'OTHER',
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
     * Display the specified cashflow transaction.
     */
    public function show($id): View
    {
        // Find the transaction across all possible sources
        $transaction = $this->findTransactionById($id);
        
        if (!$transaction) {
            abort(404, 'Transaction not found');
        }

        // Ensure transaction has required properties for the view
        $this->normalizeTransactionForView($transaction);

        return view('admin.cashflow.show', compact('transaction'));
    }

    /**
     * Find transaction by ID across all sources
     */
    private function findTransactionById($id)
    {
        // Try CashFlow first
        $transaction = CashFlow::find($id);
        if ($transaction) {
            $transaction->source_model = 'CashFlow';
            $transaction->transaction_source = 'Manual Entry';
            return $transaction;
        }

        // Try CashflowTransaction
        $transaction = CashflowTransaction::find($id);
        if ($transaction) {
            $transaction->source_model = 'CashflowTransaction';
            $transaction->transaction_source = 'Cashflow Transaction';
            return $transaction;
        }

        // Try Deposit
        $transaction = Deposit::find($id);
        if ($transaction) {
            $transaction->source_model = 'Deposit';
            $transaction->transaction_source = 'Member Deposits';
            $transaction->transaction_date = $transaction->deposit_date;
            return $transaction;
        }

        // Try Distribution
        $transaction = Distribution::find($id);
        if ($transaction) {
            $transaction->source_model = 'Distribution';
            $transaction->transaction_source = 'Savings Distribution';
            $transaction->transaction_date = $transaction->created_at;
            return $transaction;
        }

        // Try Loan
        $transaction = Loan::find($id);
        if ($transaction && $transaction->disbursement_date) {
            $transaction->source_model = 'Loan';
            $transaction->transaction_source = 'Loan Disbursement';
            $transaction->transaction_date = $transaction->disbursement_date;
            $transaction->amount = $transaction->principal_amount;
            return $transaction;
        }

        // Try LoanRepayment
        $transaction = LoanRepayment::find($id);
        if ($transaction) {
            $transaction->source_model = 'LoanRepayment';
            $transaction->transaction_source = 'Loan Repayment';
            $transaction->transaction_date = $transaction->paid_at;
            return $transaction;
        }

        // Try Fine
        $transaction = Fine::find($id);
        if ($transaction) {
            $transaction->source_model = 'Fine';
            $transaction->transaction_source = 'Member Fine';
            $transaction->transaction_date = $transaction->updated_at;
            return $transaction;
        }

        // Try WelfareFund
        $transaction = WelfareFund::find($id);
        if ($transaction) {
            $transaction->source_model = 'WelfareFund';
            $transaction->transaction_source = 'Welfare Distribution';
            $transaction->transaction_date = $transaction->created_at;
            return $transaction;
        }

        // Try LoanPenalty
        $transaction = LoanPenalty::find($id);
        if ($transaction && $transaction->paid_date) {
            $transaction->source_model = 'LoanPenalty';
            $transaction->transaction_source = 'Loan Penalty';
            $transaction->transaction_date = $transaction->paid_date;
            $transaction->amount = $transaction->penalty_amount;
            return $transaction;
        }

        // Try InvestmentTransaction
        $transaction = InvestmentTransaction::find($id);
        if ($transaction) {
            $transaction->source_model = 'InvestmentTransaction';
            $transaction->transaction_source = 'Investment Transaction';
            return $transaction;
        }

        return null;
    }

    /**
     * Normalize transaction properties for view compatibility
     */
    private function normalizeTransactionForView($transaction)
    {
        // Set default values for missing properties
        $transaction->type_badge = $transaction->type_badge ?? 
            ($transaction->type === 'income' ? 
                '<span class="badge bg-success">Income</span>' : 
                '<span class="badge bg-danger">Expense</span>');

        $transaction->category_badge = $transaction->category_badge ?? 
            '<span class="badge bg-primary">' . ($transaction->category ?? 'N/A') . '</span>';

        $transaction->status_badge = $transaction->status_badge ?? 
            '<span class="badge bg-info">' . ($transaction->status ?? 'Unknown') . '</span>';

        $transaction->transaction_type = $transaction->transaction_type ?? 
            ($transaction->type === 'income' ? 'INFLOW' : 'OUTFLOW');

        $transaction->reference_type = $transaction->reference_type ?? 'OTHER';
        $transaction->notes = $transaction->notes ?? null;
        $transaction->approved_at = $transaction->approved_at ?? null;

        // Load relationships if they exist
        if (method_exists($transaction, 'member') && !isset($transaction->member)) {
            $transaction->load('member');
        }
        if (method_exists($transaction, 'creator') && !isset($transaction->creator)) {
            $transaction->load('creator');
        }
        if (method_exists($transaction, 'approver') && !isset($transaction->approver)) {
            $transaction->load('approver');
        }
        if (method_exists($transaction, 'fiscal_year') && !isset($transaction->fiscal_year)) {
            $transaction->load('fiscal_year');
        }
    }

    /**
     * Apply filters to all transaction queries
     */
    private function applyFiltersToQueries($request, $queries)
    {
        foreach ($queries as &$query) {
            // Date range filter
            if ($request->filled('date_from')) {
                $dateField = $this->getDateFieldForQuery($query);
                $query->where($dateField, '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $dateField = $this->getDateFieldForQuery($query);
                $query->where($dateField, '<=', $request->date_to);
            }
            
            // Status filter - handle different status values across models
            if ($request->filled('status')) {
                $status = $request->status;
                if ($status === 'cleared') {
                    $query->where(function($q) {
                        $q->where('status', 'cleared')
                          ->orWhere('status', 'CLEARED')
                          ->orWhere('status', 'distributed')
                          ->orWhere('status', 'disbursed')
                          ->orWhere('status', 'received')
                          ->orWhere('status', 'paid');
                    });
                } elseif ($status === 'pending') {
                    $query->where(function($q) {
                        $q->where('status', 'pending')
                          ->orWhere('status', 'PENDING');
                    });
                } elseif ($status === 'reconciled') {
                    $query->where(function($q) {
                        $q->where('status', 'reconciled')
                          ->orWhere('status', 'RECONCILED');
                    });
                } else {
                    $query->where('status', $status);
                }
            }
        }
    }

    /**
     * Apply post-UNION filters that need to be applied to the combined result
     */
    private function applyPostUnionFilters($request, $query)
    {
        // Transaction type filter
        if ($request->filled('transaction_type')) {
            $type = $request->transaction_type === 'INFLOW' ? 'income' : 'expense';
            $query->where('type', $type);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status filter - additional filtering for combined results
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'cleared') {
                $query->where(function($q) {
                    $q->where('status', 'cleared')
                      ->orWhere('status', 'CLEARED')
                      ->orWhere('status', 'distributed')
                      ->orWhere('status', 'disbursed')
                      ->orWhere('status', 'received')
                      ->orWhere('status', 'paid');
                });
            } elseif ($status === 'pending') {
                $query->where(function($q) {
                    $q->where('status', 'pending')
                      ->orWhere('status', 'PENDING');
                });
            } elseif ($status === 'reconciled') {
                $query->where(function($q) {
                    $q->where('status', 'reconciled')
                      ->orWhere('status', 'RECONCILED');
                });
            } else {
                $query->where('status', $status);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        return $query;
    }
    public function export(Request $request)
    {
        // Handle date range parsing
        $this->parseDateRange($request);
        
        // Get all transactions (respecting current filters)
        $transactions = $this->getAllTransactions($request)->get();
        
        // Build filename with date range if provided
        $filename = 'cashflow_transactions_' . now()->format('Y_m_d_His');
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filename .= '_' . $request->date_from . '_to_' . $request->date_to;
        }
        $filename .= '.xlsx';
        
        // Create the export
        $export = new \App\Exports\CashflowExport($transactions);
        
        return Excel::download($export, $filename);
    }
}
