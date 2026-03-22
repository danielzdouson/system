<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberFinancial;
use App\Services\MemberFinancialSummaryService;

class MemberFinancialController extends Controller
{
    protected $memberFinancialService;

    public function __construct(MemberFinancialSummaryService $memberFinancialService)
    {
        $this->memberFinancialService = $memberFinancialService;
    }

    public function create()
    {
        $members = Member::all(); // list of members for selection
        return view('admin.financials.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'       => 'required|exists:members,id',
            'name'            => 'required|string|max:255',
            'number'          => 'required|string|max:50',
            'savings'         => 'nullable|numeric',
            'welfare'         => 'nullable|numeric',
            'education_in'    => 'nullable|numeric',
            'fined'           => 'nullable|numeric',
            'fines_paid'      => 'nullable|numeric',
            'education_out'   => 'nullable|numeric',
            'loan_repayments' => 'nullable|numeric',
            'loan_charges'    => 'nullable|numeric',
            'notes'           => 'nullable|string',
        ]);

        MemberFinancial::create($data);

        return redirect()->back()->with('success', 'Member financial record saved!');
    }

    /**
     * Get members sector data for financials page
     */
    public function membersSector(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $search = $request->get('search');

        // Check if export is requested
        if ($request->get('export') == 1) {
            return $this->exportMembersData($search);
        }

        $members = $this->memberFinancialService->getAllMembersFinancialSummary($perPage, $search);
        $stats = $this->memberFinancialService->getMembersSummaryStats();

        return response()->json([
            'success' => true,
            'data' => [
                'members' => $members,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * Export members financial data to CSV
     */
    private function exportMembersData($search = null)
    {
        // Get all members without pagination
        $query = Member::with([
            'monthlySaving',
            'memberFinancial',
            'memberLoanSummary',
            'memberAccounts' => function($query) {
                $query->orderBy('fiscal_year_id', 'desc');
            },
            'deposits',
            'loans' => function($query) {
                $query->whereNotIn('status', ['completed', 'paid']);
            },
            'fines' => function($query) {
                $query->where('status', 'pending');
            }
        ]);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $members = $query->get();

        // Prepare CSV data
        $csvData = [];
        $csvData[] = [
            'Member Name',
            'Member Number',
            'Total Deposits',
            'Total Savings',
            'Welfare',
            'Outstanding Fines',
            'Loan Balance',
            'Available Balance',
            'Distributed Funds',
            'Shares on Hold',
            'Total Shares',
            'Net Worth',
            'Status'
        ];

        foreach ($members as $member) {
            $summary = $this->memberFinancialService->getMemberFinancialSummary($member);
            
            $csvData[] = [
                $summary['name'],
                $summary['member_number'],
                number_format($summary['total_deposits'], 2),
                number_format($summary['total_savings'], 2),
                number_format($summary['welfare'], 2),
                number_format($summary['outstanding_fines'], 2),
                number_format($summary['loan_balance'], 2),
                number_format($summary['available_balance'], 2),
                number_format($summary['distributed_funds'], 2),
                number_format($summary['shares_on_hold'], 2),
                number_format($summary['total_shares'], 2),
                number_format($summary['net_worth'], 2),
                $summary['status']
            ];
        }

        // Generate CSV file
        $filename = 'members_financial_data_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
