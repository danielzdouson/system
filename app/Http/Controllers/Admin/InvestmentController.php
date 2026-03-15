<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\InvestmentTransaction;
use App\Models\CashflowTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\FiscalYearContext;

class InvestmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of investments.
     */
    public function index(Request $request): View
    {
        $currentFiscalYear = FiscalYearContext::getCurrent();
        
        $query = Investment::with(['creator', 'approver', 'transactions']);
        
        // Filter by fiscal year if selected
        if ($currentFiscalYear) {
            $query->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            // Show no data if no fiscal year selected
            $query->whereRaw('1 = 0');
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('investment_type')) {
            $query->where('investment_type', $request->investment_type);
        }

        if ($request->filled('institution')) {
            $query->where('institution', 'like', '%' . $request->institution . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $investments = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics - filtered by fiscal year
        $statsQuery = Investment::query();
        if ($currentFiscalYear) {
            $statsQuery->where('fiscal_year_id', $currentFiscalYear->id);
        } else {
            $statsQuery->whereRaw('1 = 0');
        }
        
        $stats = [
            'total_invested' => $statsQuery->sum('principal_amount'),
            'total_current_value' => $statsQuery->sum(DB::raw('COALESCE(current_value, principal_amount + total_returns)')),
            'total_returns' => $statsQuery->sum('total_returns'),
            'active_count' => $statsQuery->where('status', 'ACTIVE')->count(),
            'matured_count' => $statsQuery->where('status', 'MATURED')->count(),
        ];

        $stats['total_roi'] = $stats['total_invested'] > 0 
            ? (($stats['total_current_value'] - $stats['total_invested']) / $stats['total_invested']) * 100 
            : 0;

        return view('admin.investments.index', compact('investments', 'stats', 'currentFiscalYear'));
    }

    /**
     * Show the form for creating a new investment.
     */
    public function create(): View
    {
        $investmentTypes = Investment::getInvestmentTypes();
        $users = User::orderBy('name')->get();
        
        return view('admin.investments.create', compact('investmentTypes', 'users'));
    }

    /**
     * Store a newly created investment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'investment_type' => ['required', Rule::in(array_keys(Investment::getInvestmentTypes()))],
            'institution' => 'required|string|max:200',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'investment_date' => 'required|date',
            'maturity_date' => 'nullable|date|after:investment_date',
            'reference_number' => 'required|string|max:100|unique:investment_portfolios,reference_number',
            'account_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['status'] = Investment::STATUS_ACTIVE;
        $validated['created_by'] = auth()->id();
        $validated['total_returns'] = 0;
        $validated['current_value'] = $validated['principal_amount'];
        $validated['fiscal_year_id'] = $this->getCurrentFiscalYear();

        DB::beginTransaction();
        try {
            $investment = Investment::create($validated);

            // Create initial investment transaction
            $transaction = InvestmentTransaction::create([
                'investment_portfolio_id' => $investment->id,
                'transaction_type' => InvestmentTransaction::TYPE_INITIAL_INVESTMENT,
                'amount' => $validated['principal_amount'],
                'transaction_date' => $validated['investment_date'],
                'description' => 'Initial investment in ' . $investment->name,
                'reference_number' => $investment->reference_number,
                'running_balance' => $validated['principal_amount'],
                'accumulated_returns' => 0,
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Create corresponding cash flow transaction
            CashflowTransaction::create([
                'transaction_date' => $validated['investment_date'],
                'transaction_type' => 'OUTFLOW',
                'category' => 'INVESTING',
                'subcategory' => 'Investment - ' . $investment->investment_type,
                'description' => 'Investment in ' . $investment->name . ' (' . $investment->institution . ')',
                'amount' => $validated['principal_amount'],
                'reference_type' => 'INVESTMENT',
                'reference_id' => $investment->id,
                'reference_number' => $investment->reference_number,
                'status' => 'CLEARED',
                'fiscal_year_id' => $this->getCurrentFiscalYear(),
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('admin.investments.show', $investment)
                ->with('success', 'Investment created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'Error creating investment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified investment.
     */
    public function show(Investment $investment): View
    {
        $investment->load(['creator', 'approver', 'transactions' => function($query) {
            $query->orderBy('transaction_date', 'desc');
        }]);

        $transactionTypes = InvestmentTransaction::getTransactionTypes();

        return view('admin.investments.show', compact('investment', 'transactionTypes'));
    }

    /**
     * Show the form for editing the specified investment.
     */
    public function edit(Investment $investment): View
    {
        $investmentTypes = Investment::getInvestmentTypes();
        $users = User::orderBy('name')->get();
        
        return view('admin.investments.edit', compact('investment', 'investmentTypes', 'users'));
    }

    /**
     * Update the specified investment.
     */
    public function update(Request $request, Investment $investment): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'investment_type' => ['required', Rule::in(array_keys(Investment::getInvestmentTypes()))],
            'institution' => 'required|string|max:200',
            'principal_amount' => 'required|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0|max:100',
            'investment_date' => 'required|date',
            'maturity_date' => 'nullable|date|after:investment_date',
            'reference_number' => ['required', 'string', 'max:100', Rule::unique('investment_portfolios')->ignore($investment->id)],
            'account_number' => 'nullable|string|max:100',
            'current_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $investment->update($validated);

        return redirect()
            ->route('admin.investments.show', $investment)
            ->with('success', 'Investment updated successfully.');
    }

    /**
     * Remove the specified investment.
     */
    public function destroy(Investment $investment): RedirectResponse
    {
        if ($investment->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete investment with existing transactions.');
        }

        $investment->delete();

        return redirect()
            ->route('admin.investments.index')
            ->with('success', 'Investment deleted successfully.');
    }

    /**
     * Add transaction to investment
     */
    public function addTransaction(Request $request, Investment $investment): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_type' => ['required', Rule::in(array_keys(InvestmentTransaction::getTransactionTypes()))],
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
            'receipt_number' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $validated['investment_portfolio_id'] = $investment->id;
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            // Calculate running balance and accumulated returns
            $lastTransaction = $investment->transactions()
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $runningBalance = $lastTransaction ? $lastTransaction->running_balance : $investment->principal_amount;
            $accumulatedReturns = $lastTransaction ? $lastTransaction->accumulated_returns : $investment->total_returns;

            if (in_array($validated['transaction_type'], InvestmentTransaction::getInflowTypes())) {
                $runningBalance += $validated['amount'];
                if (in_array($validated['transaction_type'], ['INTEREST_INCOME', 'DIVIDEND_INCOME', 'CAPITAL_GAIN'])) {
                    $accumulatedReturns += $validated['amount'];
                }
            } else {
                $runningBalance -= $validated['amount'];
            }

            $validated['running_balance'] = (float) $runningBalance;
            $validated['accumulated_returns'] = (float) $accumulatedReturns;

            $transaction = InvestmentTransaction::create($validated);

            // Update investment totals
            $investment->total_returns = (float) $accumulatedReturns;
            $investment->current_value = (float) $runningBalance;
            $investment->save();

            // Create corresponding cash flow transaction
            $cashflowType = in_array($validated['transaction_type'], InvestmentTransaction::getInflowTypes()) ? 'INFLOW' : 'OUTFLOW';
            
            CashflowTransaction::create([
                'transaction_date' => $validated['transaction_date'],
                'transaction_type' => $cashflowType,
                'category' => 'INVESTING',
                'subcategory' => 'Investment ' . strtolower(str_replace('_', ' ', $validated['transaction_type'])),
                'description' => $validated['description'] ?: $investment->name . ' - ' . $validated['transaction_type'],
                'amount' => $validated['amount'],
                'reference_type' => 'INVESTMENT',
                'reference_id' => $investment->id,
                'reference_number' => $validated['reference_number'] ?: $investment->reference_number,
                'status' => 'CLEARED',
                'fiscal_year_id' => $this->getCurrentFiscalYear(),
                'created_by' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Transaction added successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Error adding transaction: ' . $e->getMessage());
        }
    }

    /**
     * Mark investment as matured
     */
    public function markAsMatured(Investment $investment): RedirectResponse
    {
        if ($investment->status !== Investment::STATUS_ACTIVE) {
            return back()->with('error', 'Only active investments can be marked as matured.');
        }

        $investment->markAsMatured();

        return back()->with('success', 'Investment marked as matured.');
    }

    /**
     * Close investment
     */
    public function close(Investment $investment): RedirectResponse
    {
        $investment->close();

        return back()->with('success', 'Investment closed successfully.');
    }

    /**
     * Get current fiscal year
     */
    private function getCurrentFiscalYear()
    {
        return \App\Services\FiscalYearContext::getCurrent()?->id;
    }
}
