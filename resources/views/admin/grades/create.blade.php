@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Grade</h1>
                    <p class="mt-2 text-gray-600">Add a new grade entry to the system</p>
                </div>
                <a href="{{ route('admin.grades.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Grades
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('admin.grades.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <select name="student_id" id="student_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Student</option>
                            @foreach($students ?? [] as $student)
                            <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->admission_number }})</option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select name="subject_id" id="subject_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Subject</option>
                            @foreach($subjects ?? [] as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assessment_title" class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                        <input type="text" name="assessment_title" id="assessment_title" required value="{{ old('assessment_title') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('assessment_title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="assessment_type" class="block text-sm font-medium text-gray-700 mb-2">Assessment Type</label>
                        <select name="assessment_type" id="assessment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="assignment">Assignment</option>
                            <option value="quiz">Quiz</option>
                            <option value="midterm">Midterm</option>
                            <option value="final">Final</option>
                            <option value="project">Project</option>
                        </select>
                        @error('assessment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="raw_score" class="block text-sm font-medium text-gray-700 mb-2">Raw Score</label>
                        <input type="number" name="raw_score" id="raw_score" required min="0" value="{{ old('raw_score') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('raw_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">Max Score</label>
                        <input type="number" name="max_score" id="max_score" required min="1" value="{{ old('max_score', 100) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('max_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="teacher_comments" class="block text-sm font-medium text-gray-700 mb-2">Teacher Comments</label>
                    <textarea name="teacher_comments" id="teacher_comments" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Optional comments about the assessment...">{{ old('teacher_comments') }}</textarea>
                    @error('teacher_comments')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.grades.index') }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Create Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
