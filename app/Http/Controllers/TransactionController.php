<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with('user')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'   => 'required|exists:users,id',
            'type'      => 'required|string', // deposit, withdrawal, loan_repayment
            'amount'    => 'required|numeric|min:1',
            'ref_id'    => 'nullable|string',
        ]);

        return Transaction::create($validated);
    }

    public function show($id)
    {
        return Transaction::with('user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $tx = Transaction::findOrFail($id);

        $tx->update($request->all());

        return $tx;
    }

    public function destroy($id)
    {
        return Transaction::destroy($id);
    }
}
