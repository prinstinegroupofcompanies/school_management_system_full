@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-plus text-green-500 mr-3"></i>
                    Create Fee Structure
                </h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('finance.fees.structures.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Fee Structures
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <form action="{{ route('finance.fees.structures.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Fee Structure Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('name') border-red-300 @enderror" 
                                   placeholder="Enter fee structure name" required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Class <span class="text-red-500">*</span>
                            </label>
                            <select id="class_id" name="class_id" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('class_id') border-red-300 @enderror" required>
                                <option value="">Select Class</option>
                                @foreach($classes ?? [] as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Academic Year and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">
                                Academic Year <span class="text-red-500">*</span>
                            </label>
                            <select id="academic_year" name="academic_year" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('academic_year') border-red-300 @enderror" required>
                                <option value="">Select Academic Year</option>
                                <option value="2024-2025" {{ old('academic_year') === '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                                <option value="2023-2024" {{ old('academic_year') === '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                                <option value="2022-2023" {{ old('academic_year') === '2022-2023' ? 'selected' : '' }}>2022-2023</option>
                            </select>
                            @error('academic_year')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" 
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('status') border-red-300 @enderror" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="3" 
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 @error('description') border-red-300 @enderror" 
                                  placeholder="Enter fee structure description">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fee Items -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Fee Items <span class="text-red-500">*</span>
                        </label>
                        <div id="fee-items-container">
                            <div class="fee-item border border-gray-200 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                        <input type="text" name="fee_items[0][type]" 
                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                                               placeholder="e.g., Tuition Fee" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                        <input type="number" name="fee_items[0][amount]" step="0.01" 
                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                                               placeholder="0.00" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                        <input type="date" name="fee_items[0][due_date]" 
                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="fee_items[0][description]" rows="2" 
                                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                                              placeholder="Optional description"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="addFeeItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Add Fee Item
                        </button>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('finance.fees.structures.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Create Fee Structure
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let feeItemIndex = 1;

function addFeeItem() {
    const container = document.getElementById('fee-items-container');
    const newItem = document.createElement('div');
    newItem.className = 'fee-item border border-gray-200 rounded-lg p-4 mb-4';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                <input type="text" name="fee_items[${feeItemIndex}][type]" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                       placeholder="e.g., Tuition Fee" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="fee_items[${feeItemIndex}][amount]" step="0.01" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                       placeholder="0.00" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                <input type="date" name="fee_items[${feeItemIndex}][due_date]" 
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" required>
            </div>
        </div>
        <div class="mt-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="fee_items[${feeItemIndex}][description]" rows="2" 
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                      placeholder="Optional description"></textarea>
        </div>
        <div class="mt-2 flex justify-end">
            <button type="button" onclick="removeFeeItem(this)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm font-medium transition-colors duration-200">
                <i class="fas fa-trash mr-1"></i>
                Remove
            </button>
        </div>
    `;
    container.appendChild(newItem);
    feeItemIndex++;
}

function removeFeeItem(button) {
    button.closest('.fee-item').remove();
}
</script>
@endpush
