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
}
