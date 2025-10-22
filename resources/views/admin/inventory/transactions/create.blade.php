@extends('layouts.app')

@section('title', 'Create Inventory Transaction')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create Inventory Transaction</h1>
        <a href="{{ route('admin.inventory.transactions.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Transactions
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form method="POST" action="{{ route('admin.inventory.transactions.store') }}" class="space-y-6">
            @csrf

            <!-- Transaction Type -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Item Selection -->
                <div>
                    <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Inventory Item <span class="text-red-500">*</span>
                    </label>
                    <select id="item_id" 
                            name="item_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('item_id') border-red-500 @enderror"
                            required>
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->item_name }} (Current Stock: {{ $item->current_stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Type -->
                <div>
                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Transaction Type <span class="text-red-500">*</span>
                    </label>
                    <select id="transaction_type" 
                            name="transaction_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('transaction_type') border-red-500 @enderror"
                            required>
                        <option value="">Select Type</option>
                        @foreach($transactionTypes as $type)
                            <option value="{{ $type }}" {{ old('transaction_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('transaction_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Quantity and Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="quantity" 
                           name="quantity" 
                           step="0.01"
                           min="0.01"
                           value="{{ old('quantity') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('quantity') border-red-500 @enderror"
                           required>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transaction Date -->
                <div>
                    <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Transaction Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="transaction_date" 
                           name="transaction_date" 
                           value="{{ old('transaction_date', date('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('transaction_date') border-red-500 @enderror"
                           required>
                    @error('transaction_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Reference and Destination -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reference Number -->
                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Reference Number
                    </label>
                    <input type="text" 
                           id="reference_number" 
                           name="reference_number" 
                           value="{{ old('reference_number') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('reference_number') border-red-500 @enderror"
                           placeholder="e.g., PO-2024-001, INVOICE-123">
                    @error('reference_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Destination Location -->
                <div id="destination_field" style="display: none;">
                    <label for="destination_location" class="block text-sm font-medium text-gray-700 mb-2">
                        Destination Location
                    </label>
                    <input type="text" 
                           id="destination_location" 
                           name="destination_location" 
                           value="{{ old('destination_location') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('destination_location') border-red-500 @enderror"
                           placeholder="e.g., Warehouse A, Office Building">
                    @error('destination_location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                          placeholder="Additional notes about this transaction...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transaction Type Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-900 mb-2">Transaction Types:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li><strong>In:</strong> Stock received/added to inventory</li>
                    <li><strong>Out:</strong> Stock removed/distributed from inventory</li>
                    <li><strong>Transfer:</strong> Stock moved between locations</li>
                    <li><strong>Adjustment:</strong> Stock level correction</li>
                </ul>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.inventory.transactions.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Transaction
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transactionTypeSelect = document.getElementById('transaction_type');
    const destinationField = document.getElementById('destination_field');
    
    transactionTypeSelect.addEventListener('change', function() {
        if (this.value === 'transfer') {
            destinationField.style.display = 'block';
            document.getElementById('destination_location').required = true;
        } else {
            destinationField.style.display = 'none';
            document.getElementById('destination_location').required = false;
        }
    });
});
</script>
@endpush
@endsection
