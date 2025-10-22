@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Exam</h1>
        <a href="{{ route('teacher.exams.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Exams
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('teacher.exams.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exam Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="mt-1 block w-full border-gray-300 rounded-md @error('title') border-red-500 @enderror" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exam Type</label>
                    <select name="exam_type" class="mt-1 block w-full border-gray-300 rounded-md @error('exam_type') border-red-500 @enderror" required>
                        <option value="">Select type</option>
                        @foreach($examTypes as $key => $type)
                            <option value="{{ $key }}" {{ old('exam_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('exam_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <select name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md @error('subject_id') border-red-500 @enderror" required>
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" class="mt-1 block w-full border-gray-300 rounded-md @error('class_id') border-red-500 @enderror" required>
                        <option value="">Select class</option>
                        @if($classes && $classes->count() > 0)
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                            @endforeach
                        @else
                            <option value="" disabled>No classes available</option>
                        @endif
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if($classes && $classes->count() == 0)
                        <p class="mt-1 text-sm text-red-600">No classes assigned to this teacher. Please contact administrator.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="datetime-local" name="start_time" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Marks</label>
                    <input type="number" name="total_marks" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Passing Marks</label>
                    <input type="number" name="passing_marks" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Instructions</label>
                <textarea name="instructions" rows="4" class="mt-1 block w-full border-gray-300 rounded-md"></textarea>
            </div>

            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" name="randomize_questions" id="randomize_questions" value="1" class="h-4 w-4 text-blue-600">
                    <label for="randomize_questions" class="ml-2 text-sm text-gray-700">Randomize question order</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="show_results_immediately" id="show_results_immediately" value="1" class="h-4 w-4 text-blue-600">
                    <label for="show_results_immediately" class="ml-2 text-sm text-gray-700">Show results immediately after submission</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="allow_review" id="allow_review" value="1" checked class="h-4 w-4 text-blue-600">
                    <label for="allow_review" class="ml-2 text-sm text-gray-700">Allow students to review their answers</label>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Create Exam
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
