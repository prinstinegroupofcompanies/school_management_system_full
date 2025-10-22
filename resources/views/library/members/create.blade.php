@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-5"></div>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                        Add New Library Member
                    </h1>
                    <p class="text-lg text-gray-600">Register a new member for the library system</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('library.members.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Members
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-8">
                <form action="{{ route('library.members.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Personal Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-user text-blue-600 mr-3"></i>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-300 @enderror" 
                                       placeholder="Enter first name" required>
                                @error('first_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-300 @enderror" 
                                       placeholder="Enter last name" required>
                                @error('last_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror" 
                                       placeholder="Enter email address" required>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-300 @enderror" 
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Library Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-book text-green-600 mr-3"></i>
                            Library Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Member ID
                                </label>
                                <input type="text" id="member_id" name="member_id" value="{{ old('member_id', 'MEM' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('member_id') border-red-300 @enderror" 
                                       placeholder="Auto-generated member ID">
                                @error('member_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="member_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Member Type <span class="text-red-500">*</span>
                                </label>
                                <select id="member_type" name="member_type" 
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('member_type') border-red-300 @enderror" required>
                                    <option value="">Select Member Type</option>
                                    <option value="student" {{ old('member_type') === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ old('member_type') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="staff" {{ old('member_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="external" {{ old('member_type') === 'external' ? 'selected' : '' }}>External</option>
                                </select>
                                @error('member_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <textarea id="address" name="address" rows="3" 
                                      class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-300 @enderror" 
                                      placeholder="Enter full address">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Account Settings -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cog text-purple-600 mr-3"></i>
                            Account Settings
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="max_books" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maximum Books Allowed
                                </label>
                                <input type="number" id="max_books" name="max_books" value="{{ old('max_books', 5) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('max_books') border-red-300 @enderror" 
                                       placeholder="Maximum books" min="1" max="20">
                                @error('max_books')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="loan_period" class="block text-sm font-medium text-gray-700 mb-2">
                                    Loan Period (Days)
                                </label>
                                <input type="number" id="loan_period" name="loan_period" value="{{ old('loan_period', 14) }}" 
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('loan_period') border-red-300 @enderror" 
                                       placeholder="Loan period in days" min="1" max="90">
                                @error('loan_period')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    Active Member
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('library.members.index') }}" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Add Member
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection