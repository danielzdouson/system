<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    public function index()
    {
        return Saving::with('user')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount'  => 'required|numeric|min:1',
        ]);

        return Saving::create($validated);
    }

    public function show($id)
    {
        return Saving::with('user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $saving = Saving::findOrFail($id);

        $saving->update($request->all());

        return $saving;
    }

    public function destroy($id)
    {
        return Saving::destroy($id);
    }
}
