<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'role' => 'required|in:admin,super_admin,loans_officer,treasurer,member',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->role = $validated['role'];
        $user->save();

        return $user;
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->all();

        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // `role` is not mass-assignable; set it explicitly when provided.
        if ($request->filled('role')) {
            $request->validate([
                'role' => 'in:admin,super_admin,loans_officer,treasurer,member',
            ]);
            $user->role = $request->input('role');
            $user->save();
        }

        return $user;
    }

    public function destroy($id)
    {
        return User::destroy($id);
    }
}
