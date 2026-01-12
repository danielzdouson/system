@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Edit Transaction</h2>
                    <a href="{{ route('cashflow.index') }}" class="text-gray-600 hover:text-gray-800">
                        &larr; Back to Transactions
                    </a>
                </div>

                <form action="{{ route('cashflow.update', $transaction) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('cashflow._form')
                </form>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-red-700 mb-4">Danger Zone</h3>
                    <form action="{{ route('cashflow.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-md text-sm font-medium">
                            Delete Transaction
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
