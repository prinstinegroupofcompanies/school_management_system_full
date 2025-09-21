@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Fee Structure</h1>
            <p class="mt-2 text-gray-600">Update fee structure details</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('finance.fees.structures.show', $feeStructure) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                View Details
            </a>
            <a href="{{ route('finance.fees.structures.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('finance.fees.structures.update', $feeStructure) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Fee Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $feeStructure->name) }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="e.g., Tuition Fee, Library Fee" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class *</label>
                    <select id="class_id" name="class_id" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $feeStructure->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fee Type -->
                <div>
                    <label for="fee_type" class="block text-sm font-medium text-gray-700 mb-2">Fee Type *</label>
                    <select id="fee_type" name="fee_type" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Fee Type</option>
                        <option value="tuition" {{ old('fee_type', $feeStructure->fee_type) == 'tuition' ? 'selected' : '' }}>Tuition</option>
                        <option value="library" {{ old('fee_type', $feeStructure->fee_type) == 'library' ? 'selected' : '' }}>Library</option>
                        <option value="laboratory" {{ old('fee_type', $feeStructure->fee_type) == 'laboratory' ? 'selected' : '' }}>Laboratory</option>
                        <option value="sports" {{ old('fee_type', $feeStructure->fee_type) == 'sports' ? 'selected' : '' }}>Sports</option>
                        <option value="transport" {{ old('fee_type', $feeStructure->fee_type) == 'transport' ? 'selected' : '' }}>Transport</option>
                        <option value="hostel" {{ old('fee_type', $feeStructure->fee_type) == 'hostel' ? 'selected' : '' }}>Hostel</option>
                        <option value="examination" {{ old('fee_type', $feeStructure->fee_type) == 'examination' ? 'selected' : '' }}>Examination</option>
                        <option value="miscellaneous" {{ old('fee_type', $feeStructure->fee_type) == 'miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                    </select>
                    @error('fee_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount ($) *</label>
                    <input type="number" id="amount" name="amount" value="{{ old('amount', $feeStructure->total_amount) }}" step="0.01" min="0"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="0.00" required>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date *</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $feeStructure->due_date ? $feeStructure->due_date->format('Y-m-d') : '') }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('due_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year *</label>
                    <input type="text" id="academic_year" name="academic_year" value="{{ old('academic_year', $feeStructure->academic_year) }}" 
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="2025" required>
                    @error('academic_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="active" {{ old('status', $feeStructure->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $feeStructure->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="draft" {{ old('status', $feeStructure->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active Checkbox -->
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           {{ old('is_active', $feeStructure->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active for New Enrollments
                    </label>
                </div>
            </div>

            <!-- Advanced Financial Settings -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Advanced Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Discount Percentage -->
                    <div>
                        <label for="discount_percentage" class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (%)</label>
                        <input type="number" id="discount_percentage" name="discount_percentage" 
                               value="{{ old('discount_percentage', $feeStructure->discount_percentage) }}" 
                               step="0.01" min="0" max="100"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="0.00">
                        @error('discount_percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Discount Amount -->
                    <div>
                        <label for="discount_amount" class="block text-sm font-medium text-gray-700 mb-2">Discount Amount ($)</label>
                        <input type="number" id="discount_amount" name="discount_amount" 
                               value="{{ old('discount_amount', $feeStructure->discount_amount) }}" 
                               step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="0.00">
                        @error('discount_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grace Period Days -->
                    <div>
                        <label for="grace_period_days" class="block text-sm font-medium text-gray-700 mb-2">Grace Period (Days)</label>
                        <input type="number" id="grace_period_days" name="grace_period_days" 
                               value="{{ old('grace_period_days', $feeStructure->grace_period_days) }}" 
                               min="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="0">
                        @error('grace_period_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Late Fee Percentage -->
                    <div>
                        <label for="late_fee_percentage" class="block text-sm font-medium text-gray-700 mb-2">Late Fee Percentage (% per day)</label>
                        <input type="number" id="late_fee_percentage" name="late_fee_percentage" 
                               value="{{ old('late_fee_percentage', $feeStructure->late_fee_percentage) }}" 
                               step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="0.00">
                        @error('late_fee_percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Late Fee Amount -->
                    <div>
                        <label for="late_fee_amount" class="block text-sm font-medium text-gray-700 mb-2">Maximum Late Fee ($)</label>
                        <input type="number" id="late_fee_amount" name="late_fee_amount" 
                               value="{{ old('late_fee_amount', $feeStructure->late_fee_amount) }}" 
                               step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="0.00">
                        @error('late_fee_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Installments -->
                    <div>
                        <label for="max_installments" class="block text-sm font-medium text-gray-700 mb-2">Maximum Installments</label>
                        <input type="number" id="max_installments" name="max_installments" 
                               value="{{ old('max_installments', $feeStructure->max_installments) }}" 
                               min="1"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                               placeholder="1">
                        @error('max_installments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Allow Installments -->
                    <div class="flex items-center">
                        <input type="checkbox" id="allow_installments" name="allow_installments" value="1" 
                               {{ old('allow_installments', $feeStructure->allow_installments) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="allow_installments" class="ml-2 block text-sm text-gray-900">
                            Allow Installment Payments
                        </label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="3" 
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          placeholder="Optional description for this fee structure">{{ old('description', $feeStructure->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('finance.fees.structures.show', $feeStructure) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    Update Fee Structure
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    @if($feeStructure->studentFees->count() == 0)
        <div class="bg-white rounded-lg shadow-sm border border-red-200 mt-6">
            <div class="px-6 py-4 border-b border-red-200">
                <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-sm font-medium text-red-900">Delete Fee Structure</h4>
                        <p class="text-sm text-red-700">This action cannot be undone. This will permanently delete the fee structure.</p>
                    </div>
                    <form method="POST" action="{{ route('finance.fees.structures.destroy', $feeStructure) }}" 
                          class="inline" onsubmit="return confirm('Are you sure you want to delete this fee structure? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                            Delete Fee Structure
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <svg class="w-5 h-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yellow-800">Cannot Delete</h3>
                    <p class="text-sm text-yellow-700 mt-1">This fee structure has {{ $feeStructure->studentFees->count() }} student assignments and cannot be deleted.</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
