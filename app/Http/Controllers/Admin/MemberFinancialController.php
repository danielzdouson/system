<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberFinancial;

class MemberFinancialController extends Controller
{
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
}
