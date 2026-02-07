<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Models\CashflowTransaction;
use App\Models\Deposit;
use App\Models\Distribution;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Transaction;
use App\Models\FiscalYear;
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
     * Show the form for editing the specified cashflow transaction.
     */
    public function edit(CashflowTransaction $transaction): View
    {
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        return view('admin.cashflow.edit', compact('transaction', 'fiscalYears'));
    }

    /**
     * Update the specified cashflow transaction.
     */
    public function update(Request $request, CashflowTransaction $transaction): RedirectResponse
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

        $transaction->update($request->all());

        return redirect()->route('admin.cashflow.index')
            ->with('success', 'Cashflow transaction updated successfully.');
    }

    /**
     * Remove the specified cashflow transaction.
     */
    public function destroy(CashflowTransaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return redirect()->route('admin.cashflow.index')
            ->with('success', 'Cashflow transaction deleted successfully.');
    }

    /**
     * Display cashflow dashboard
     */
    public function dashboard(): View
    {
        $activeFiscalYear = FiscalYear::where('status', 'active')->first();
        $currentBalance = CashPositionService::getCurrentBalance();
        $cashPositionTrend = CashPositionService::getCashPositionTrend(30);
        
        return view('admin.cashflow.dashboard', compact(
            'activeFiscalYear',
            'currentBalance',
            'cashPositionTrend'
        ));
    }

    /**
     * Get comprehensive transactions from all system models
     */
    private function getAllTransactions($request = null)
    {
        // Get CashFlow transactions
        $cashFlowQuery = CashFlow::with(['user'])
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
                'notes',
                'user_id',
                'created_at',
                DB::raw("'CashFlow' as source_model"),
                DB::raw("'Manual Entry' as transaction_source")
            ]);

        // Get CashflowTransaction records
        $cashflowTransactionQuery = CashflowTransaction::with(['member', 'creator'])
            ->select([
                'id',
                'transaction_date',
                'description',
                'reference_number',
                DB::raw("'INFLOW' as type"),
                'category',
                'amount',
                'payment_method',
                'status',
                'notes',
                'member_id',
                'created_at',
                DB::raw("'CashflowTransaction' as source_model"),
                DB::raw("'Cashflow Transaction' as transaction_source")
            ]);

        // Get Deposit records (treated as inflows)
        $depositQuery = Deposit::with(['member', 'creator'])
            ->select([
                'id',
                'deposit_date as transaction_date',
                DB::raw("'Group Savings Deposit' as description"),
                DB::raw("CONCAT('DEP-', id) as reference_number"),
                DB::raw("'income' as type"),
                DB::raw("'OPERATING' as category"),
                'amount',
                DB::raw("'savings' as payment_method"),
                'status',
                'notes',
                'member_id',
                'created_at',
                DB::raw("'Deposit' as source_model"),
                DB::raw("'Group Savings' as transaction_source")
            ]);

        // Get Distribution records (treated as outflows)
        $distributionQuery = Distribution::with(['deposit.member', 'creator'])
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
                'notes',
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'Distribution' as source_model"),
                DB::raw("'Savings Distribution' as transaction_source")
            ]);

        // Get Loan disbursements (treated as outflows)
        $loanQuery = Loan::with(['member', 'disbursedBy'])
            ->where('disbursement_date', '!=', null)
            ->select([
                'id',
                'disbursement_date as transaction_date',
                DB::raw("CONCAT('Loan Disbursement - ', loan_purpose) as description"),
                'loan_number as reference_number',
                DB::raw("'expense' as type"),
                DB::raw("'FINANCING' as category"),
                'loan_amount as amount',
                DB::raw("'loan_disbursement' as payment_method"),
                DB::raw("'disbursed' as status"),
                'notes',
                'member_id',
                'created_at',
                DB::raw("'Loan' as source_model"),
                DB::raw("'Loan Disbursement' as transaction_source")
            ]);

        // Get Loan repayments (treated as inflows)
        $loanRepaymentQuery = LoanRepayment::with(['loan.member', 'receivedBy'])
            ->select([
                'id',
                'payment_date as transaction_date',
                DB::raw("CONCAT('Loan Repayment - ', receipt_number) as description"),
                'receipt_number as reference_number',
                DB::raw("'income' as type"),
                DB::raw("'FINANCING' as category"),
                'payment_amount as amount',
                'payment_method',
                DB::raw("'received' as status"),
                'notes',
                DB::raw("NULL as member_id"),
                'created_at',
                DB::raw("'LoanRepayment' as source_model"),
                DB::raw("'Loan Repayment' as transaction_source")
            ]);

        // Apply filters to all queries if request is provided
        if ($request) {
            // Date filters
            if ($request->filled('date_from')) {
                $dateFrom = $request->date_from;
                $cashFlowQuery->where('transaction_date', '>=', $dateFrom);
                $cashflowTransactionQuery->where('transaction_date', '>=', $dateFrom);
                $depositQuery->where('deposit_date', '>=', $dateFrom);
                $distributionQuery->where('created_at', '>=', $dateFrom);
                $loanQuery->where('disbursement_date', '>=', $dateFrom);
                $loanRepaymentQuery->where('payment_date', '>=', $dateFrom);
            }

            if ($request->filled('date_to')) {
                $dateTo = $request->date_to;
                $cashFlowQuery->where('transaction_date', '<=', $dateTo);
                $cashflowTransactionQuery->where('transaction_date', '<=', $dateTo);
                $depositQuery->where('deposit_date', '<=', $dateTo);
                $distributionQuery->where('created_at', '<=', $dateTo);
                $loanQuery->where('disbursement_date', '<=', $dateTo);
                $loanRepaymentQuery->where('payment_date', '<=', $dateTo);
            }

            // Transaction type filter
            if ($request->filled('transaction_type')) {
                $type = $request->transaction_type === 'INFLOW' ? 'income' : 'expense';
                $cashFlowQuery->where('type', $type);
                $cashflowTransactionQuery->whereRaw("'INFLOW' = ?", [$request->transaction_type]);
                $depositQuery->whereRaw("'income' = ?", [$type]);
                $distributionQuery->whereRaw("'expense' = ?", [$type]);
                $loanQuery->whereRaw("'expense' = ?", [$type]);
                $loanRepaymentQuery->whereRaw("'income' = ?", [$type]);
            }

            // Category filter
            if ($request->filled('category')) {
                $cashFlowQuery->where('category', $request->category);
                $cashflowTransactionQuery->where('category', $request->category);
                $depositQuery->whereRaw("'OPERATING' = ?", [$request->category]);
                $distributionQuery->whereRaw("'OPERATING' = ?", [$request->category]);
                $loanQuery->whereRaw("'FINANCING' = ?", [$request->category]);
                $loanRepaymentQuery->whereRaw("'FINANCING' = ?", [$request->category]);
            }

            // Status filter
            if ($request->filled('status')) {
                $cashFlowQuery->where('status', $request->status);
                $cashflowTransactionQuery->where('status', $request->status);
                $depositQuery->where('status', $request->status);
                $distributionQuery->whereRaw("'distributed' = ?", [$request->status]);
                $loanQuery->whereRaw("'disbursed' = ?", [$request->status]);
                $loanRepaymentQuery->whereRaw("'received' = ?", [$request->status]);
            }

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $cashFlowQuery->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });

                $cashflowTransactionQuery->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('reference_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });

                $depositQuery->where(function($q) use ($search) {
                    $q->where('notes', 'like', "%{$search}%");
                });

                $distributionQuery->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });

                $loanQuery->where(function($q) use ($search) {
                    $q->where('loan_purpose', 'like', "%{$search}%")
                      ->orWhere('loan_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });

                $loanRepaymentQuery->where(function($q) use ($search) {
                    $q->where('receipt_number', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%");
                });
            }
        }

        // Union all queries and order by date
        $allTransactions = $cashFlowQuery
            ->unionAll($cashflowTransactionQuery)
            ->unionAll($depositQuery)
            ->unionAll($distributionQuery)
            ->unionAll($loanQuery)
            ->unionAll($loanRepaymentQuery)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        return $allTransactions;
    }

    /**
     * Display cashflow transactions list
     */
    public function index(Request $request): View
    {
        $activeFiscalYear = FiscalYear::where('status', 'active')->first();
        
        // Use comprehensive transaction query
        $query = $this->getAllTransactions($request);
        
        // Since we're using union queries, we need to paginate differently
        // We'll get all results and paginate manually, or use a simpler approach
        $allTransactions = $query->get();
        
        // Manual pagination
        $page = $request->get('page', 1);
        $perPage = 50;
        $total = $allTransactions->count();
        $transactions = $allTransactions->forPage($page, $perPage);
        
        // Create pagination object
        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $transactions,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
            ]
        );

        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();

        // Calculate real-time totals from all transaction sources
        $totalInflows = 0;
        $totalOutflows = 0;
        
        // From CashFlow
        $totalInflows += CashFlow::where('type', 'income')
            ->where('status', 'cleared')
            ->sum('amount');
        $totalOutflows += CashFlow::where('type', 'expense')
            ->where('status', 'cleared')
            ->sum('amount');
            
        // From CashflowTransaction
        $totalInflows += CashflowTransaction::where('transaction_type', 'INFLOW')
            ->where('status', 'CLEARED')
            ->sum('amount');
        $totalOutflows += CashflowTransaction::where('transaction_type', 'OUTFLOW')
            ->where('status', 'CLEARED')
            ->sum('amount');
            
        // From Deposits (inflows)
        $totalInflows += Deposit::where('status', 'cleared')
            ->sum('amount');
            
        // From Distributions (outflows)
        $totalOutflows += Distribution::sum('amount');
            
        // From Loan disbursements (outflows)
        $totalOutflows += Loan::whereNotNull('disbursement_date')
            ->sum('loan_amount');
            
        // From Loan repayments (inflows)
        $totalInflows += LoanRepayment::sum('payment_amount');
            
        $totalBalance = $totalInflows - $totalOutflows;

        // Calculate month-over-month changes
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $lastMonthYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

        // Current month totals from all sources
        $currentMonthInflows = 0;
        $currentMonthOutflows = 0;
        
        $currentMonthInflows += CashFlow::where('type', 'income')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        $currentMonthOutflows += CashFlow::where('type', 'expense')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $currentMonthInflows += CashflowTransaction::where('transaction_type', 'INFLOW')
            ->where('status', 'CLEARED')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');
            
        $currentMonthOutflows += CashflowTransaction::where('transaction_type', 'OUTFLOW')
            ->where('status', 'CLEARED')
            ->whereMonth('transaction_date', $currentMonth)
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $currentMonthInflows += Deposit::where('status', 'cleared')
            ->whereMonth('deposit_date', $currentMonth)
            ->whereYear('deposit_date', $currentYear)
            ->sum('amount');
            
        $currentMonthOutflows += Distribution::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');
            
        $currentMonthOutflows += Loan::whereNotNull('disbursement_date')
            ->whereMonth('disbursement_date', $currentMonth)
            ->whereYear('disbursement_date', $currentYear)
            ->sum('loan_amount');
            
        $currentMonthInflows += LoanRepayment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('payment_amount');

        // Last month totals (similar logic)
        $lastMonthInflows = 0;
        $lastMonthOutflows = 0;
        
        $lastMonthInflows += CashFlow::where('type', 'income')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastMonthYear)
            ->sum('amount');
            
        $lastMonthOutflows += CashFlow::where('type', 'expense')
            ->where('status', 'cleared')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastMonthYear)
            ->sum('amount');

        $lastMonthInflows += CashflowTransaction::where('transaction_type', 'INFLOW')
            ->where('status', 'CLEARED')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastMonthYear)
            ->sum('amount');
            
        $lastMonthOutflows += CashflowTransaction::where('transaction_type', 'OUTFLOW')
            ->where('status', 'CLEARED')
            ->whereMonth('transaction_date', $lastMonth)
            ->whereYear('transaction_date', $lastMonthYear)
            ->sum('amount');

        $lastMonthInflows += Deposit::where('status', 'cleared')
            ->whereMonth('deposit_date', $lastMonth)
            ->whereYear('deposit_date', $lastMonthYear)
            ->sum('amount');
            
        $lastMonthOutflows += Loan::whereNotNull('disbursement_date')
            ->whereMonth('disbursement_date', $lastMonth)
            ->whereYear('disbursement_date', $lastMonthYear)
            ->sum('loan_amount');
            
        $lastMonthInflows += LoanRepayment::whereMonth('payment_date', $lastMonth)
            ->whereYear('payment_date', $lastMonthYear)
            ->sum('payment_amount');

        // Calculate monthly balances
        $currentMonthBalance = $currentMonthInflows - $currentMonthOutflows;
        $lastMonthBalance = $lastMonthInflows - $lastMonthOutflows;

        // Calculate percentage changes with better error handling
        $inflowChange = 0;
        $outflowChange = 0;
        $balanceChange = 0;
        
        // Only calculate if there was data last month
        if ($lastMonthInflows > 0) {
            $inflowChange = (($currentMonthInflows - $lastMonthInflows) / $lastMonthInflows) * 100;
            // Cap at reasonable range to prevent unrealistic values
            $inflowChange = max(-100, min(1000, $inflowChange));
        }
        
        if ($lastMonthOutflows > 0) {
            $outflowChange = (($currentMonthOutflows - $lastMonthOutflows) / $lastMonthOutflows) * 100;
            // Cap at reasonable range to prevent unrealistic values
            $outflowChange = max(-100, min(1000, $outflowChange));
        }
        
        if ($lastMonthBalance > 0) {
            $balanceChange = (($currentMonthBalance - $lastMonthBalance) / $lastMonthBalance) * 100;
            // Cap at reasonable range to prevent unrealistic values
            $balanceChange = max(-100, min(1000, $balanceChange));
        }

        // Count pending transactions
        $pendingCount = CashflowTransaction::where('status', 'PENDING')->count() +
                       CashFlow::where('status', 'pending')->count();

        return view('admin.cashflow.index', compact(
            'transactions',
            'activeFiscalYear',
            'fiscalYears',
            'totalBalance',
            'totalInflows',
            'totalOutflows',
            'balanceChange',
            'inflowChange',
            'outflowChange',
            'pendingCount'
        ));
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
                $request->all() // Pass all URL parameters for filtering
            );
        }

        return view('admin.cashflow.monthly-statement', compact(
            'statement',
            'activeFiscalYear',
            'fiscalYears'
        ));
    }

    /**
     * Display fiscal year cashflow statement
     */
    public function fiscalYearStatement(Request $request): View
    {
        $activeFiscalYear = FiscalYear::where('status', 'active')->first();
        $fiscalYears = FiscalYear::orderBy('start_date', 'desc')->get();
        
        $statement = null;
        if ($request->filled('fiscal_year_id')) {
            try {
                $statementService = new CashflowStatementService();
                $statement = $statementService->generateFiscalYearStatement(
                    $request->fiscal_year_id
                );
            } catch (\Exception $e) {
                \Log::error('Fiscal year statement generation error: ' . $e->getMessage());
                $statement = [
                    'fiscal_year' => 'Error',
                    'error' => $e->getMessage()
                ];
            }
        }

        return view('admin.cashflow.fiscal-year-statement', compact(
            'statement',
            'activeFiscalYear',
            'fiscalYears'
        ));
    }

    /**
     * Bulk approve pending transactions
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:cashflow_transactions,id'
        ]);

        $transactionIds = $request->transaction_ids;
        
        CashflowTransaction::whereIn('id', $transactionIds)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'CLEARED',
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);

        $count = count($transactionIds);
        
        return redirect()->back()
            ->with('success', "{$count} pending transaction(s) approved successfully.");
    }

    /**
     * Show cashflow transaction details
     */
    public function show(CashflowTransaction $transaction): View
    {
        $transaction->load(['member', 'creator', 'approver', 'fiscalYear']);
        
        return view('admin.cashflow.show', compact('transaction'));
    }

    /**
     * Approve pending cashflow transaction
     */
    public function approve(CashflowTransaction $transaction): RedirectResponse
    {
        if ($transaction->status !== CashflowTransaction::STATUS_PENDING) {
            return redirect()->back()
                ->with('error', 'Transaction cannot be approved. Current status: ' . $transaction->status);
        }

        $transaction->approve(auth()->user());

        return redirect()->back()
            ->with('success', 'Cashflow transaction approved successfully.');
    }

    /**
     * Reconcile cashflow transaction
     */
    public function reconcile(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:cashflow_transactions,id'
        ]);

        $transactions = CashflowTransaction::whereIn('id', $request->transaction_ids)->get();
        
        foreach ($transactions as $transaction) {
            $transaction->reconcile();
        }

        return redirect()->back()
            ->with('success', count($transactions) . ' transactions reconciled successfully.');
    }

    /**
     * Export cashflow transactions to Excel
     */
    public function export(Request $request)
    {
        $query = CashflowTransaction::with(['member', 'creator', 'fiscalYear'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('fiscal_year_id')) {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $filename = 'cashflow_transactions_' . now()->format('Y_m_d') . '.xlsx';
        
        return Excel::download(new CashflowTransactionExport($transactions), $filename);
    }

    /**
     * Export monthly cashflow statement to PDF
     */
    public function exportMonthlyStatement(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $statementService = new CashflowStatementService();
        $statement = $statementService->generateMonthlyStatement(
            $request->fiscal_year_id,
            $request->month
        );

        $filename = 'cashflow_statement_' . $statement['period'] . '.xlsx';
        
        return Excel::download(new CashflowStatementExport($statement), $filename);
    }

    /**
     * Export monthly cashflow statement to PDF
     */
    public function exportMonthlyStatementPDF(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'month' => 'required|integer|min:1|max:12'
        ]);

        $statementService = new CashflowStatementService();
        $statement = $statementService->generateMonthlyStatement(
            $request->fiscal_year_id,
            $request->month
        );

        $pdf = \Barryvdh\DomPDF\Facade::loadView('admin.cashflow.pdf.monthly-statement', compact('statement'));
        
        $filename = 'cashflow_statement_' . $statement['period'] . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export fiscal year cashflow statement to PDF
     */
    public function exportFiscalYearStatement(Request $request)
    {
        $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id'
        ]);

        $statementService = new CashflowStatementService();
        $statement = $statementService->generateFiscalYearStatement(
            $request->fiscal_year_id
        );

        // For now, return as array - PDF export can be added later
        return redirect()->back()
            ->with('success', 'Fiscal year statement generated successfully.')
            ->with('statement_data', $statement);
    }

    /**
     * Get real-time cash position
     */
    public function getCashPosition(): \Illuminate\Http\JsonResponse
    {
        $balance = CashPositionService::getCurrentBalance();
        $trend = CashPositionService::getCashPositionTrend(7); // Last 7 days
        
        return response()->json([
            'current_balance' => $balance,
            'trend' => $trend,
            'formatted_balance' => number_format($balance, 2)
        ]);
    }
}
