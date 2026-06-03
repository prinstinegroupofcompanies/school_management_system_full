@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Add Exam Mark</h1>
        <form method="POST" action="{{ route('admin.exams.marks.store') }}" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student</label>
                    <select name="student_id" id="student_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select student</option>
                        @foreach(\App\Models\Student::with('user')->get() as $s)
                            <option value="{{ $s->id }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>{{ $s->user->name ?? $s->student_id }}</option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="exam_schedule_id" class="block text-sm font-medium text-gray-700">Exam Schedule</label>
                    <select name="exam_schedule_id" id="exam_schedule_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select exam</option>
                        @foreach(\App\Models\ExamSchedule::with('examType')->get() as $es)
                            <option value="{{ $es->id }}" {{ old('exam_schedule_id') == $es->id ? 'selected' : '' }}>{{ $es->title }} ({{ $es->examType->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                    @error('exam_schedule_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                    <select name="subject_id" id="subject_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" id="class_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('academic_year')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="marks_obtained" class="block text-sm font-medium text-gray-700">Marks Obtained</label>
                    <input type="number" step="0.01" name="marks_obtained" id="marks_obtained" value="{{ old('marks_obtained') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('marks_obtained')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
                    <input type="number" step="0.01" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('total_marks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.exams.marks.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
