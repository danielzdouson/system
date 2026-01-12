@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Transaction Type -->
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700">Transaction Type <span class="text-red-500">*</span></label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <label class="inline-flex items-center px-4 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-700 text-sm">
                <input type="radio" name="type" value="income" {{ (old('type', $transaction->type ?? '') === 'income') ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                <span class="ml-2">Income</span>
            </label>
            <label class="inline-flex items-center px-4 py-2 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-700 text-sm">
                <input type="radio" name="type" value="expense" {{ (old('type', $transaction->type ?? '') === 'expense') ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                <span class="ml-2">Expense</span>
            </label>
        </div>
        @error('type')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Transaction Date -->
    <div>
        <label for="transaction_date" class="block text-sm font-medium text-gray-700">Date <span class="text-red-500">*</span></label>
        <input type="date" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', isset($transaction) ? $transaction->transaction_date->format('Y-m-d') : now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        @error('transaction_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Amount -->
    <div>
        <label for="amount" class="block text-sm font-medium text-gray-700">Amount <span class="text-red-500">*</span></label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">$</span>
            </div>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" value="{{ old('amount', $transaction->amount ?? '') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
        </div>
        @error('amount')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Category -->
    <div class="income-category">
        <label for="category_income" class="block text-sm font-medium text-gray-700">Income Category <span class="text-red-500">*</span></label>
        <select name="category" id="category_income" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">Select a category</option>
            @foreach($categories['income'] as $key => $category)
                <option value="{{ $key }}" {{ (old('category', $transaction->category ?? '') == $key) ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
        @error('category')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="expense-category hidden">
        <label for="category_expense" class="block text-sm font-medium text-gray-700">Expense Category <span class="text-red-500">*</span></label>
        <select name="category" id="category_expense" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">Select a category</option>
            @foreach($categories['expense'] as $key => $category)
                <option value="{{ $key }}" {{ (old('category', $transaction->category ?? '') == $key) ? 'selected' : '' }}>{{ $category }}</option>
            @endforeach
        </select>
    </div>

    <!-- Payment Method -->
    <div>
        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method <span class="text-red-500">*</span></label>
        <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">Select payment method</option>
            @foreach($paymentMethods as $key => $method)
                <option value="{{ $key }}" {{ (old('payment_method', $transaction->payment_method ?? '') == $key) ? 'selected' : '' }}>{{ $method }}</option>
            @endforeach
        </select>
        @error('payment_method')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Reference Number -->
    <div>
        <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $transaction->reference_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        @error('reference_number')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Description -->
    <div class="col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
        <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $transaction->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Notes -->
    <div class="col-span-2">
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $transaction->notes ?? '') }}</textarea>
        @error('notes')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status (only for edit) -->
    @if(isset($transaction))
    <div class="col-span-2">
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="pending" {{ (old('status', $transaction->status) == 'pending') ? 'selected' : '' }}>Pending</option>
            <option value="cleared" {{ (old('status', $transaction->status) == 'cleared') ? 'selected' : '' }}>Cleared</option>
            <option value="reconciled" {{ (old('status', $transaction->status) == 'reconciled') ? 'selected' : '' }}>Reconciled</option>
        </select>
    </div>
    @endif

    <!-- Form Actions -->
    <div class="col-span-2 flex justify-end space-x-3">
        <a href="{{ route('cashflow.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm font-medium">
            Cancel
        </a>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium">
            {{ isset($transaction) ? 'Update' : 'Create' }} Transaction
        </button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide category fields based on transaction type
        function toggleCategoryFields() {
            const type = document.querySelector('input[name="type"]:checked').value;
            document.querySelectorAll('.income-category, .expense-category').forEach(el => {
                el.classList.add('hidden');
            });
            
            if (type === 'income') {
                document.querySelector('.income-category').classList.remove('hidden');
                document.getElementById('category_income').setAttribute('name', 'category');
                document.getElementById('category_expense').removeAttribute('name');
            } else {
                document.querySelector('.expense-category').classList.remove('hidden');
                document.getElementById('category_expense').setAttribute('name', 'category');
                document.getElementById('category_income').removeAttribute('name');
            }
        }

        // Initial toggle
        toggleCategoryFields();

        // Add event listeners for radio buttons
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', toggleCategoryFields);
        });
    });
</script>
@endpush
