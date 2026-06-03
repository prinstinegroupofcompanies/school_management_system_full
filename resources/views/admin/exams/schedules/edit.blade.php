@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Exam Schedule</h1>
        <form method="POST" action="{{ route('admin.exams.schedules.update', $schedule) }}" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $schedule->title) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="exam_type_id" class="block text-sm font-medium text-gray-700">Exam Type</label>
                    <select name="exam_type_id" id="exam_type_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($examTypes as $et)
                            <option value="{{ $et->id }}" {{ old('exam_type_id', $schedule->exam_type_id) == $et->id ? 'selected' : '' }}>{{ $et->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" id="class_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id', $schedule->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject (optional)</label>
                    <select name="subject_id" id="subject_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">— None —</option>
                        @foreach($subjects as $s)
                            <option value="{{ $s->id }}" {{ old('subject_id', $schedule->subject_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', $schedule->academic_year) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $schedule->start_date?->format('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $schedule->end_date?->format('Y-m-d')) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="venue" class="block text-sm font-medium text-gray-700">Venue</label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue', $schedule->venue) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach(['draft','published','ongoing','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $schedule->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.exams.schedules.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
