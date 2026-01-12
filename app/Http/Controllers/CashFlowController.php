<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\Deposit;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Fine;
use App\Models\Distribution;
use App\Models\FiscalYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class CashFlowController extends Controller
{
    protected $categories = [
        'income' => [
            'loan_interest' => 'Loan Interest Income',
            'member_deposits' => 'Member Deposits',
            'registration_fees' => 'Registration Fees',
            'fine_payments' => 'Fine Payments',
            'service_fees' => 'Service Fees',
            'other_income' => 'Other Income'
        ],
        'expense' => [
            'loan_disbursements' => 'Loan Disbursements',
            'welfare_payments' => 'Welfare Payments',
            'operational_costs' => 'Operational Costs',
            'bank_charges' => 'Bank Charges',
            'administrative' => 'Administrative Expenses',
            'other_expenses' => 'Other Expenses'
        ]
    ];

    protected $paymentMethods = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'mpesa' => 'M-Pesa',
        'cheque' => 'Cheque',
        'other' => 'Other'
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = CashFlow::with('user')->latest('transaction_date');
        
        // Apply filters if any
        if ($request->has('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        
        $transactions = $query->paginate(20);
        
        return view('cashflow.index', [
            'transactions' => $transactions,
            'categories' => $this->categories,
            'paymentMethods' => $this->paymentMethods,
        ]);
    }

    public function monthlyDashboard(Request $request)
    {
        $activeFiscalYear = FiscalYear::getActive();
        $currentMonth = $request->get('month', Carbon::now()->month);
        $currentYear = $request->get('year', Carbon::now()->year);
        
        // Get monthly cash flow data
        $monthlyData = $this->getMonthlyCashFlowData($currentMonth, $currentYear, $activeFiscalYear);
        
        // Get year-to-date summary
        $ytdSummary = $this->getYearToDateSummary($currentYear, $activeFiscalYear);
        
        // Get monthly trends for the current year
        $monthlyTrends = $this->getMonthlyTrends($currentYear, $activeFiscalYear);
        
        return view('cashflow.monthly-dashboard', [
            'monthlyData' => $monthlyData,
            'ytdSummary' => $ytdSummary,
            'monthlyTrends' => $monthlyTrends,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'activeFiscalYear' => $activeFiscalYear,
            'categories' => $this->categories,
        ]);
    }

    private function getMonthlyCashFlowData($month, $year, $activeFiscalYear)
    {
        // Get cash flow transactions for the month
        $cashFlowTransactions = CashFlow::whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->get();

        // Get SACCO-specific data for the month
        $monthlyDeposits = Deposit::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('amount');

        $monthlyLoanDisbursements = Loan::whereMonth('disbursement_date', $month)
            ->whereYear('disbursement_date', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('loan_amount');

        $monthlyLoanRepayments = Loan::whereMonth('disbursement_date', $month)
            ->whereYear('disbursement_date', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('paid_amount');

        $monthlyFines = Fine::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('amount');

        $monthlyWelfarePayments = Distribution::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('type', 'welfare')
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->whereHas('deposit', function($subQuery) use ($activeFiscalYear) {
                    $subQuery->where('fiscal_year_id', $activeFiscalYear->id);
                });
            })
            ->sum('amount');

        // Calculate totals
        $totalIncome = $cashFlowTransactions->where('type', 'income')->sum('amount') + 
                      $monthlyLoanRepayments + $monthlyFines + $monthlyDeposits;
        
        $totalExpenses = $cashFlowTransactions->where('type', 'expense')->sum('amount') + 
                        $monthlyLoanDisbursements + $monthlyWelfarePayments;
        
        $netCashFlow = $totalIncome - $totalExpenses;

        return [
            'month' => $month,
            'year' => $year,
            'monthName' => Carbon::create($year, $month, 1)->format('F'),
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netCashFlow' => $netCashFlow,
            'monthlyDeposits' => $monthlyDeposits,
            'monthlyLoanDisbursements' => $monthlyLoanDisbursements,
            'monthlyLoanRepayments' => $monthlyLoanRepayments,
            'monthlyFines' => $monthlyFines,
            'monthlyWelfarePayments' => $monthlyWelfarePayments,
            'cashFlowTransactions' => $cashFlowTransactions,
        ];
    }

    private function getYearToDateSummary($year, $activeFiscalYear)
    {
        // Get YTD data
        $ytdDeposits = Deposit::whereYear('created_at', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('amount');

        $ytdLoanDisbursements = Loan::whereYear('disbursement_date', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('loan_amount');

        $ytdLoanRepayments = Loan::whereYear('disbursement_date', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('paid_amount');

        $ytdFines = Fine::whereYear('created_at', $year)
            ->when($activeFiscalYear, function($query) use ($activeFiscalYear) {
                $query->where('fiscal_year_id', $activeFiscalYear->id);
            })
            ->sum('amount');

        $ytdCashFlow = CashFlow::whereYear('transaction_date', $year)
            ->get();

        $ytdIncome = $ytdCashFlow->where('type', 'income')->sum('amount') + $ytdLoanRepayments + $ytdFines + $ytdDeposits;
        $ytdExpenses = $ytdCashFlow->where('type', 'expense')->sum('amount') + $ytdLoanDisbursements;

        return [
            'totalIncome' => $ytdIncome,
            'totalExpenses' => $ytdExpenses,
            'netCashFlow' => $ytdIncome - $ytdExpenses,
            'totalDeposits' => $ytdDeposits,
            'totalLoanDisbursements' => $ytdLoanDisbursements,
            'totalLoanRepayments' => $ytdLoanRepayments,
            'totalFines' => $ytdFines,
        ];
    }

    private function getMonthlyTrends($year, $activeFiscalYear)
    {
        $trends = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData = $this->getMonthlyCashFlowData($month, $year, $activeFiscalYear);
            $trends[] = [
                'month' => $month,
                'monthName' => Carbon::create($year, $month, 1)->format('M'),
                'income' => $monthlyData['totalIncome'],
                'expenses' => $monthlyData['totalExpenses'],
                'netCashFlow' => $monthlyData['netCashFlow'],
            ];
        }
        
        return $trends;
    }

    public function create()
    {
        return view('cashflow.create', [
            'categories' => $this->categories,
            'paymentMethods' => $this->paymentMethods
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'cleared'; // Default status
        
        CashFlow::create($validated);
        
        return redirect()->route('cashflow.index')
            ->with('success', 'Transaction added successfully.');
    }

    public function show(CashFlow $cashflow)
    {
        return view('cashflow.show', compact('cashflow'));
    }

    public function edit(CashFlow $cashflow)
    {
        return view('cashflow.edit', [
            'transaction' => $cashflow,
            'categories' => $this->categories,
            'paymentMethods' => $this->paymentMethods
        ]);
    }

    public function update(Request $request, CashFlow $cashflow)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,cleared,reconciled',
            'notes' => 'nullable|string|max:2000',
        ]);
        
        $cashflow->update($validated);
        
        return redirect()->route('cashflow.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(CashFlow $cashflow)
    {
        $cashflow->delete();
        
        return redirect()->route('cashflow.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function download(Request $request)
    {
        $transactions = CashFlow::query()
            ->when($request->type, function($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->category, function($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->date_from, function($query, $dateFrom) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            })
            ->when($request->date_to, function($query, $dateTo) {
                $query->whereDate('transaction_date', '<=', $dateTo);
            })
            ->orderBy('transaction_date', 'desc')
            ->get();

        $filename = 'cashflow_transactions_' . now()->format('Y_m_d') . '.xlsx';
        
        return Excel::download(new CashFlowExport($transactions), $filename);
    }
}

class CashFlowExport implements FromCollection, WithHeadings, WithMapping
{
    protected $categories;
    protected $paymentMethods;
    protected $transactions;

    public function __construct($transactions)
    {
        $this->categories = [
            'income' => [
                'membership_fees' => 'Membership Fees',
                'loan_repayments' => 'Loan Repayments',
                'interest_income' => 'Interest Income',
                'other_income' => 'Other Income'
            ],
            'expense' => [
                'salaries' => 'Salaries',
                'office_supplies' => 'Office Supplies',
                'utilities' => 'Utilities',
                'maintenance' => 'Maintenance',
                'other_expenses' => 'Other Expenses'
            ]
        ];
        $this->paymentMethods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'mpesa' => 'M-Pesa',
            'cheque' => 'Cheque',
            'other' => 'Other'
        ];
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Description',
            'Reference Number',
            'Category',
            'Type',
            'Amount',
            'Payment Method',
            'Status',
            'Notes',
            'Created At'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_date->format('Y-m-d'),
            $transaction->description,
            $transaction->reference_number,
            $this->getCategoryName($transaction->category),
            ucfirst($transaction->type),
            number_format($transaction->amount, 2),
            $this->getPaymentMethodName($transaction->payment_method),
            ucfirst($transaction->status),
            $transaction->notes,
            $transaction->created_at->format('Y-m-d H:i:s')
        ];
    }

    private function getCategoryName($category)
    {
        $allCategories = array_merge($this->categories['income'], $this->categories['expense']);
        return $allCategories[$category] ?? $category;
    }

    private function getPaymentMethodName($method)
    {
        return $this->paymentMethods[$method] ?? $method;
    }
}
