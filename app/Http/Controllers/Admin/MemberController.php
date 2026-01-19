<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     */
    public function index()
    {
        $members = Member::latest()->get();
        return view('admin.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new member.
     * (Using same Blade as index with form)
     */
    public function create()
    {
        return redirect()->route('admin.members.index');
    }

    /**
     * Store a newly created member in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50|unique:members,national_id',
            'email' => 'nullable|email|max:255|unique:members,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            // User account fields
            'create_user_account' => 'nullable|boolean',
            'user_email' => 'required_if:create_user_account,1|email|max:255|unique:users,email',
            'user_password' => 'required_if:create_user_account,1|string|min:8',
        ]);

        // Create member
        $member = Member::create($request->all());

        // Create user account if requested
        if ($request->boolean('create_user_account')) {
            $user = User::create([
                'name' => $member->first_name . ' ' . $member->last_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'role' => 'member',
            ]);

            // Link the user to the member
            $member->user_id = $user->id;
            $member->save();

            $message = 'Member and user account created successfully.';
        } else {
            $message = 'Member created successfully.';
        }

        return redirect()->route('admin.members.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified member.
     */
    public function edit(Member $member)
    {
        $members = Member::latest()->get(); // to show list
        return view('admin.members.index', [
            'members' => $members,
            'memberToEdit' => $member
        ]);
    }

    /**
     * Update the specified member in storage.
     */
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50|unique:members,national_id,'.$member->id,
            'email' => 'nullable|email|max:255|unique:members,email,'.$member->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $member->update($request->all());

        return redirect()->route('admin.members.index')->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified member from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully.');
    }
}
