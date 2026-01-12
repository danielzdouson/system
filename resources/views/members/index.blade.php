{{-- resources/views/admin/members/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="header"><h2>Members Management</h2></div>

<div class="container" style="display:flex; min-height:100vh;">
    <div class="sidebar" style="width:220px; background:#111827; color:white; padding:20px;">
        <h3>Navigation</h3>
        <a href="{{ url('/') }}" style="display:block; color:#d1d5db; text-decoration:none; margin:6px 0;">Dashboard</a>
        <a href="{{ route('admin.members.index') }}" style="display:block; color:#d1d5db; text-decoration:none; margin:6px 0;">Members</a>
        <a href="#" style="display:block; color:#d1d5db; text-decoration:none; margin:6px 0;">Loans</a>
        <a href="#" style="display:block; color:#d1d5db; text-decoration:none; margin:6px 0;">Savings</a>
        <a href="#" style="display:block; color:#d1d5db; text-decoration:none; margin:6px 0;">Cash Flow</a>
    </div>

    <div class="content" style="flex:1; padding:20px;">

        {{-- Add New Member --}}
        <div class="card" style="background:#fff; padding:20px; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.1); max-width:800px; margin-bottom:20px;">
            <h3>Add New Member</h3>

            @if(session('success'))
                <div class="success" style="background: #d1fae5; padding:10px; border-radius:4px; margin-bottom:15px; color:#065f46;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.members.store') }}">
                @csrf

                <div class="form-group" style="margin-bottom:15px;">
                    <label>First Name</label>
                    <input type="text" name="first_name" required style="width:100%; padding:10px; font-size:14px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required style="width:100%; padding:10px; font-size:14px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>National ID</label>
                    <input type="text" name="national_id" style="width:100%; padding:10px; font-size:14px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Email</label>
                    <input type="email" name="email" style="width:100%; padding:10px; font-size:14px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <div class="form-group" style="margin-bottom:15px;">
                    <label>Phone</label>
                    <input type="text" name="phone" style="width:100%; padding:10px; font-size:14px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <button type="submit" style="padding:10px 18px; background:#1f2937; color:white; border:none; border-radius:4px; cursor:pointer;">Save Member</button>
            </form>
        </div>

        {{-- List of Members --}}
        @if(isset($members) && $members->count() > 0)
            <table style="width:100%; border-collapse:collapse; margin-top:30px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>National ID</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $member->first_name }}</td>
                        <td>{{ $member->last_name }}</td>
                        <td>{{ $member->national_id }}</td>
                        <td>{{ $member->email }}</td>
                        <td>{{ $member->phone }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    </div>
</div>
@endsection
