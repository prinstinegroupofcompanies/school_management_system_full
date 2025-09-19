@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Process Payment</h1>
                    <p class="mt-2 text-gray-600">Record a new fee payment for a student</p>
                </div>
                <a href="{{ route('finance.payments.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Payments
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="POST" action="{{ route('finance.payments.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Student Selection -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Student Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="student_search" class="block text-sm font-medium text-gray-700 mb-2">
                            Search Student <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="student_search" placeholder="Search by name, admission number, or student ID..."
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   autocomplete="off">
                            <div id="student_results" class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <input type="hidden" name="student_id" id="selected_student_id" required>
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="student_info" class="hidden">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-medium text-blue-900 mb-2">Selected Student</h3>
                            <div id="student_details" class="text-sm text-blue-800"></div>
                        </div>
                    </div>
                </div>

                <!-- Fee Balance Information -->
                <div id="fee_balance_info" class="hidden mt-6 p-4 bg-yellow-50 rounded-lg">
                    <h3 class="font-medium text-yellow-900 mb-2">Current Fee Status</h3>
                    <div id="balance_details" class="text-sm text-yellow-800"></div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">$</span>
                            <input type="number" name="amount" id="amount" step="0.01" min="0" required
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="0.00">
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Cheque</option>
                            <option value="card">Credit/Debit Card</option>
                        </select>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Reference
                        </label>
                        <input type="text" name="payment_reference" id="payment_reference"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Transaction ID, Check number, etc.">
                        @error('payment_reference')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="payment_date" id="payment_date" required
                               value="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('payment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fee_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Fee Type
                        </label>
                        <select name="fee_type" id="fee_type"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">General Payment</option>
                            <option value="tuition">Tuition Fee</option>
                            <option value="registration">Registration Fee</option>
                            <option value="library">Library Fee</option>
                            <option value="laboratory">Laboratory Fee</option>
                            <option value="sports">Sports Fee</option>
                            <option value="technology">Technology Fee</option>
                            <option value="examination">Examination Fee</option>
                            <option value="activity">Activity Fee</option>
                            <option value="transport">Transport Fee</option>
                            <option value="hostel">Hostel Fee</option>
                            <option value="meal">Meal Fee</option>
                            <option value="uniform">Uniform Fee</option>
                            <option value="book">Book Fee</option>
                            <option value="miscellaneous">Miscellaneous Fee</option>
                        </select>
                        @error('fee_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Academic Year
                        </label>
                        <input type="text" name="academic_year" id="academic_year"
                               value="{{ date('Y') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('academic_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Payment Proof Upload -->
                <div class="mt-6">
                    <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">
                        Payment Proof (Receipt, Bank Slip, etc.)
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept="image/*,.pdf">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                        </div>
                    </div>
                    @error('payment_proof')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Additional notes about this payment..."></textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('finance.payments.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft"
                        class="px-6 py-3 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="process"
                        class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Process Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Student search functionality
document.getElementById('student_search').addEventListener('input', function(e) {
    const query = e.target.value;
    const resultsDiv = document.getElementById('student_results');
    
    if (query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
    }

    // Simulate API call - replace with actual endpoint
    fetch(`/api/students/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = '';
            
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-gray-500">No students found</div>';
            } else {
                data.forEach(student => {
                    const div = document.createElement('div');
                    div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b';
                    div.innerHTML = `
                        <div class="font-medium">${student.name}</div>
                        <div class="text-sm text-gray-500">${student.admission_number} • ${student.class_name}</div>
                        <div class="text-sm text-gray-500">Balance: $${student.balance_fees}</div>
                    `;
                    div.addEventListener('click', () => selectStudent(student));
                    resultsDiv.appendChild(div);
                });
            }
            
            resultsDiv.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error searching students:', error);
            resultsDiv.innerHTML = '<div class="p-3 text-red-500">Error searching students</div>';
            resultsDiv.classList.remove('hidden');
        });
});

function selectStudent(student) {
    document.getElementById('student_search').value = student.name;
    document.getElementById('selected_student_id').value = student.id;
    document.getElementById('student_results').classList.add('hidden');
    
    // Show student info
    document.getElementById('student_details').innerHTML = `
        <div><strong>Name:</strong> ${student.name}</div>
        <div><strong>Admission Number:</strong> ${student.admission_number}</div>
        <div><strong>Class:</strong> ${student.class_name}</div>
        <div><strong>Student ID:</strong> ${student.student_number}</div>
    `;
    document.getElementById('student_info').classList.remove('hidden');
    
    // Show fee balance info
    document.getElementById('balance_details').innerHTML = `
        <div><strong>Total Fees:</strong> $${student.total_fees}</div>
        <div><strong>Paid:</strong> $${student.paid_fees}</div>
        <div><strong>Balance:</strong> $${student.balance_fees}</div>
    `;
    document.getElementById('fee_balance_info').classList.remove('hidden');
    
    // Set suggested amount to balance
    if (student.balance_fees > 0) {
        document.getElementById('amount').value = student.balance_fees;
    }
}

// Hide results when clicking outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('student_search').contains(e.target) && 
        !document.getElementById('student_results').contains(e.target)) {
        document.getElementById('student_results').classList.add('hidden');
    }
});
</script>
@endsection
