@extends('layouts.admin')

@section('title', 'Add Financial Record')

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
    
    <!-- Instructions Card -->
    <div class="dashboard-card" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:25px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <div style="display:flex; align-items:center; margin-bottom:10px;">
            <span class="card-icon">💰</span>
            <div>
                <h4 style="margin:0; font-size:14px; opacity:0.9;">Financial Entry Guide</h4>
                <p style="margin:5px 0 0 0; font-size:12px; opacity:0.8;">Record member contributions, savings, and welfare payments</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="dashboard-card" style="background:white; padding:25px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0; color:#1f2937; display:flex; align-items:center;">
                <span style="margin-right:10px;">💰</span>
                New Financial Record
            </h3>
            
            @if(session('success'))
                <div class="success-message" style="background:#d1fae5; padding:15px; border-radius:8px; margin-bottom:20px; color:#065f46;">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.financials.store') }}" class="financial-form">
            @csrf
            
            <!-- Member Selection -->
            <div class="form-section">
                <h4 style="margin:0 0 15px 0; color:#374151; display:flex; align-items:center;">
                    <span style="margin-right:10px;">👥</span>
                    Member Information
                </h4>
                
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Select Member</label>
                        <select name="member_id" required class="form-input">
                            <option value="">Choose member...</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} ({{ $member->national_id ?? 'No ID' }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Membership Name</label>
                        <input type="text" name="name" required class="form-input" placeholder="Enter membership name">
                    </div>
                    
                    <div class="form-group">
                        <label>Membership Number</label>
                        <input type="text" name="number" required class="form-input" placeholder="Enter membership number">
                    </div>
                </div>
            </div>

            <!-- Contributions Section -->
            <div class="form-section">
                <h4 style="margin:0 0 15px 0; color:#374151; display:flex; align-items:center;">
                    <span style="margin-right:10px;">💰</span>
                    Contributions
                </h4>
                
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Savings</label>
                        <input type="number" step="0.01" name="savings" required class="form-input" placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Welfare</label>
                        <input type="number" step="0.01" name="welfare" required class="form-input" placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Education In</label>
                        <input type="number" step="0.01" name="education_in" required class="form-input" placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Deductions Section -->
            <div class="form-section">
                <h4 style="margin:0 0 15px 0; color:#374151; display:flex; align-items:center;">
                    <span style="margin-right:10px;">📉</span>
                    Deductions / Penalties
                </h4>
                
                <div class="form-grid" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                    <div class="form-group">
                        <label>Fined</label>
                        <input type="number" step="0.01" name="fined" required class="form-input" placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Fines Paid</label>
                        <input type="number" step="0.01" name="fines_paid" required class="form-input" placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label>Education Out</label>
                        <input type="number" step="0.01" name="education_out" required class="form-input" placeholder="0.00">
                    </div>
                    <input type="text" name="number" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <h4>Contributions</h4>
                <div class="form-group">
                    <label>Savings</label>
                    <input type="number" step="0.01" name="savings" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>
                <div class="form-group">
                    <label>Welfare</label>
                    <input type="number" step="0.01" name="welfare" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>
                <div class="form-group">
                    <label>Education In</label>
                    <input type="number" step="0.01" name="education_in" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <h4>Deductions / Penalties</h4>
                <div class="form-group">
                    <label>Fined</label>
                    <input type="number" step="0.01" name="fined" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>
                <div class="form-group">
                    <label>Fines Paid</label>
                    <input type="number" step="0.01" name="fines_paid" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>
                <div class="form-group">
                    <label>Education Out</label>
                    <input type="number" step="0.01" name="education_out" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <h4>Loans</h4>
                <div class="form-group">
                    <label>Loan Repayments</label>
                    <input type="number" step="0.01" name="loan_repayments" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>
                <div class="form-group">
                    <label>Loan Charges</label>
                    <input type="number" step="0.01" name="loan_charges" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;">
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:4px;"></textarea>
                </div>

                <button type="submit" style="padding:10px 18px; background:#1f2937; color:white; border:none; border-radius:4px; cursor:pointer;">Save Financial Record</button>
            </form>
        </div>
    </div>
</div>
@endsection
