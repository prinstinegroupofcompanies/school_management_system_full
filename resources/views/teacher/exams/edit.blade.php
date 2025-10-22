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
                        <i class="fas fa-edit text-blue-600 mr-3"></i>
                        Edit Exam
                    </h1>
                    <p class="text-lg text-gray-600">Update exam details and settings</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('teacher.exams.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Exams
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="px-8 py-8">
                <form method="POST" action="{{ route('teacher.exams.update', $exam) }}" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" value="{{ old('title', $exam->title) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('title') border-red-300 @enderror" 
                                       required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Type <span class="text-red-500">*</span></label>
                                <select name="exam_type" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('exam_type') border-red-300 @enderror" 
                                        required>
                                    <option value="">Select type</option>
                                    @foreach($examTypes as $key => $type)
                                        <option value="{{ $key }}" {{ old('exam_type', $exam->exam_type) == $key ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('exam_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="4" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('description') border-red-300 @enderror"
                                      placeholder="Enter exam description...">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Subject and Class Selection -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-graduation-cap text-green-600 mr-3"></i>
                            Subject and Class
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                                <select name="subject_id" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('subject_id') border-red-300 @enderror" 
                                        required>
                                    <option value="">Select subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Class <span class="text-red-500">*</span></label>
                                <select name="class_id" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('class_id') border-red-300 @enderror" 
                                        required>
                                    <option value="">Select class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Exam Details -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-calendar text-purple-600 mr-3"></i>
                            Exam Details
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time <span class="text-red-500">*</span></label>
                                <input type="datetime-local" name="start_time" value="{{ old('start_time', $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('Y-m-d\TH:i') : '') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('start_time') border-red-300 @enderror" 
                                       required>
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) <span class="text-red-500">*</span></label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="480"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('duration_minutes') border-red-300 @enderror" 
                                       required>
                                @error('duration_minutes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks <span class="text-red-500">*</span></label>
                                <input type="number" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('total_marks') border-red-300 @enderror" 
                                       required>
                                @error('total_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Passing Marks <span class="text-red-500">*</span></label>
                                <input type="number" name="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('passing_marks') border-red-300 @enderror" 
                                       required>
                                @error('passing_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                            <textarea name="instructions" rows="4" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('instructions') border-red-300 @enderror"
                                      placeholder="Enter exam instructions for students...">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Exam Settings -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cog text-orange-600 mr-3"></i>
                            Exam Settings
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="randomize_questions" id="randomize_questions" value="1" 
                                       {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="randomize_questions" class="ml-2 text-sm text-gray-700">Randomize question order</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="show_results_immediately" id="show_results_immediately" value="1" 
                                       {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="show_results_immediately" class="ml-2 text-sm text-gray-700">Show results immediately after submission</label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" name="allow_review" id="allow_review" value="1" 
                                       {{ old('allow_review', $exam->allow_review) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="allow_review" class="ml-2 text-sm text-gray-700">Allow students to review their answers</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('teacher.exams.index') }}"
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i>
                            Update Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
