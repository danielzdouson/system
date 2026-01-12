@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Member Record</h2>

    <form method="POST" action="{{ route('admin.financial.update', $record) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>ID Number (Member No.)</label>
            <input type="text" name="id_number" class="form-control" value="{{ $record->id_number }}" required>
        </div>

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $record->name }}" required>
        </div>

        <div class="mb-3">
            <label>Contact</label>
            <input type="text" name="contact" class="form-control" value="{{ $record->contact }}">
        </div>

        <div class="mb-3">
            <label>Savings</label>
            <input type="number" name="savings" class="form-control" value="{{ $record->savings }}" step="0.01" required>
        </div>

        <div class="mb-3">
            <label>Welfare Funds</label>
            <input type="number" name="welfare_funds" class="form-control" value="{{ $record->welfare_funds }}" step="0.01">
        </div>

        <div class="mb-3">
            <label>Education Funds</label>
            <input type="number" name="education_funds" class="form-control" value="{{ $record->education_funds }}" step="0.01">
        </div>

        <div class="mb-3">
            <label>Fines Outstanding</label>
            <input type="number" name="fines_outstanding" class="form-control" value="{{ $record->fines_outstanding }}" step="0.01">
        </div>

        <div class="mb-3">
            <label>Fines Collected</label>
            <input type="number" name="fines_collected" class="form-control" value="{{ $record->fines_collected }}" step="0.01">
        </div>

        <div class="mb-3">
            <label>Loan Balance</label>
            <input type="number" name="loan_balance" class="form-control" value="{{ $record->loan_balance }}" step="0.01">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('admin.financial.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
