@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Teacher</h1>
                <p class="text-gray-600 mt-2">Create a new teacher account and profile</p>
            </div>
            <a href="{{ route('admin.teachers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Teachers
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <form action="{{ route('admin.teachers.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Personal Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                                   required>
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                                   required>
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   required>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Employee ID -->
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Employee ID *</label>
                            <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror" 
                                   required>
                            @error('employee_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Qualification -->
                        <div>
                            <label for="qualification" class="block text-sm font-medium text-gray-700 mb-2">Qualification</label>
                            <input type="text" id="qualification" name="qualification" value="{{ old('qualification') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('qualification') border-red-500 @enderror">
                            @error('qualification')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Department -->
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <select id="department_id" name="department_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('department_id') border-red-500 @enderror">
                                <option value="">Select a department</option>
                                @if(isset($departments))
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('department_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Employment Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Joining Date -->
                        <div>
                            <label for="joining_date" class="block text-sm font-medium text-gray-700 mb-2">Joining Date *</label>
                            <input type="date" id="joining_date" name="joining_date" value="{{ old('joining_date') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('joining_date') border-red-500 @enderror" 
                                   required>
                            @error('joining_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Basic Salary -->
                        <div>
                            <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-2">Basic Salary *</label>
                            <input type="number" id="basic_salary" name="basic_salary" value="{{ old('basic_salary') }}" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('basic_salary') border-red-500 @enderror" 
                                   required>
                            @error('basic_salary')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assignment Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Assignments</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Subjects -->
                        <div>
                            <label for="subjects" class="block text-sm font-medium text-gray-700 mb-2">Subjects</label>
                            <select id="subjects" name="subjects[]" multiple size="8"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subjects') border-red-500 @enderror">
                                @isset($subjects)
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ collect(old('subjects', []))->contains($subject->id) ? 'selected' : '' }}>
                                            {{ $subject->name }} ({{ $subject->class->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                            @error('subjects')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Classes -->
                        <div>
                            <label for="classes" class="block text-sm font-medium text-gray-700 mb-2">Classes</label>
                            <select id="classes" name="classes[]" multiple size="8"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('classes') border-red-500 @enderror">
                                @isset($classes)
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ collect(old('classes', []))->contains($class->id) ? 'selected' : '' }}>
                                            {{ $class->name }} (Session: {{ $class->session ?? 'A' }})
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple.</p>
                            @error('classes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Class Teacher Assignment -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Class Teacher Assignment</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Class Teacher Classes -->
                        <div>
                            <label for="class_teacher_classes" class="block text-sm font-medium text-gray-700 mb-2">Class Teacher For</label>
                            <select id="class_teacher_classes" name="class_teacher_classes[]" multiple size="6"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_teacher_classes') border-red-500 @enderror">
                                @isset($classes)
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ collect(old('class_teacher_classes', []))->contains($class->id) ? 'selected' : '' }}>
                                            {{ $class->name }} (Session: {{ $class->session ?? 'A' }})
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Select classes where this teacher will be the class teacher. Hold Ctrl/Cmd to select multiple.</p>
                            @error('class_teacher_classes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                      placeholder="Enter teacher's address...">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
