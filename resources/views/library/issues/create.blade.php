@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-blue-600 opacity-5"></div>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-book-open text-purple-600 mr-3"></i>
                        Issue New Book
                    </h1>
                    <p class="text-lg text-gray-600">Issue a book to a library member</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('library.issues.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Issues
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-8">
                <form action="{{ route('library.issues.store') }}" method="POST" class="space-y-8">
                        @csrf
                    
                    <!-- Book and Member Selection -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-book text-blue-600 mr-3"></i>
                            Book and Member Selection
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="book_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Book <span class="text-red-500">*</span>
                                </label>
                                <select id="book_id" name="book_id" 
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('book_id') border-red-300 @enderror" required>
                                        <option value="">Select Book</option>
                                        @foreach(\App\Models\Book::where('status', 'available')->get() as $book)
                                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                                {{ $book->title }} by {{ $book->author }} ({{ $book->isbn ?? 'No ISBN' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('book_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>

                            <div>
                                <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Member <span class="text-red-500">*</span>
                                </label>
                                <select id="member_id" name="member_id" 
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('member_id') border-red-300 @enderror" required>
                                        <option value="">Select Member</option>
                                        @foreach(\App\Models\LibraryMember::where('is_active', true)->with('user')->get() as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->user->name ?? 'N/A' }} ({{ $member->member_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('member_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>
                            </div>
                        </div>

                    <!-- Issue Details -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-calendar text-green-600 mr-3"></i>
                            Issue Details
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Issue Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('issue_date') border-red-300 @enderror" required>
                                    @error('issue_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Due Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('due_date') border-red-300 @enderror" required>
                                    @error('due_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>
                            </div>
                        </div>

                    <!-- Additional Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-info-circle text-orange-600 mr-3"></i>
                            Additional Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="issue_no" class="block text-sm font-medium text-gray-700 mb-2">
                                    Issue Number
                                </label>
                                <input type="text" id="issue_no" name="issue_no" value="{{ old('issue_no', 'ISS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('issue_no') border-red-300 @enderror" 
                                       placeholder="Auto-generated issue number">
                                    @error('issue_no')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>

                            <div>
                                <label for="fine_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fine Amount (if any)
                                </label>
                                <input type="number" step="0.01" id="fine_amount" name="fine_amount" value="{{ old('fine_amount', 0) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('fine_amount') border-red-300 @enderror" 
                                       placeholder="Enter fine amount" min="0">
                                    @error('fine_amount')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes
                            </label>
                            <textarea id="notes" name="notes" rows="4" 
                                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 @error('notes') border-red-300 @enderror" 
                                      placeholder="Enter any additional notes about the book issue">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('library.issues.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-book-open mr-2"></i>
                            Issue Book
                        </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>
@endsection
