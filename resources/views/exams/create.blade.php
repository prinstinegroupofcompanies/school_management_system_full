@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-xl font-semibold mb-4">Add Exam</h1>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('teacher.exams.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exam Type</label>
                    <select name="exam_type" class="mt-1 block w-full border rounded px-3 py-2" required>
                        <option value="First Semester Exam" {{ old('exam_type')==='First Semester Exam' ? 'selected' : '' }}>First Semester Exam</option>
                        <option value="Second Semester Exam" {{ old('exam_type')==='Second Semester Exam' ? 'selected' : '' }}>Second Semester Exam</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                        <select name="class_id" class="mt-1 block w-full border rounded px-3 py-2" required>
                            <option value="" disabled {{ old('class_id') ? '' : 'selected' }}>Select class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)old('class_id') === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                        <select name="subject_id" class="mt-1 block w-full border rounded px-3 py-2" required>
                            <option value="" disabled {{ old('subject_id') ? '' : 'selected' }}>Select subject</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}" {{ (string)old('subject_id') === (string)$s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" name="exam_date" value="{{ old('exam_date') }}" class="mt-1 block w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 block w-full border rounded px-3 py-2" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 block w-full border rounded px-3 py-2" required />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Marks</label>
                        <input type="number" name="total_marks" value="{{ old('total_marks') }}" class="mt-1 block w-full border rounded px-3 py-2" min="1" step="1" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Passing Marks</label>
                        <input type="number" name="passing_marks" value="{{ old('passing_marks') }}" class="mt-1 block w-full border rounded px-3 py-2" min="1" step="1" required />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Number (optional)</label>
                    <input type="text" name="room_number" value="{{ old('room_number') }}" class="mt-1 block w-full border rounded px-3 py-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions (optional)</label>
                    <textarea name="instructions" rows="3" class="mt-1 block w-full border rounded px-3 py-2">{{ old('instructions') }}</textarea>
                </div>

                <div class="pt-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Create Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


