@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Exam Mark</h1>
        <form method="POST" action="{{ route('admin.exams.marks.update', $mark) }}" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            @method('PUT')
            <p class="text-gray-600">Student: <strong>{{ $mark->student->user->name ?? 'N/A' }}</strong> | Exam: <strong>{{ $mark->examSchedule->examType->name ?? 'N/A' }}</strong></p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="marks_obtained" class="block text-sm font-medium text-gray-700">Marks Obtained</label>
                    <input type="number" step="0.01" name="marks_obtained" id="marks_obtained" value="{{ old('marks_obtained', $mark->marks_obtained) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('marks_obtained')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
                    <input type="number" step="0.01" name="total_marks" id="total_marks" value="{{ old('total_marks', $mark->total_marks) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('total_marks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                    <input type="text" name="remarks" id="remarks" value="{{ old('remarks', $mark->remarks) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div class="md:col-span-2">
                    <label for="teacher_comments" class="block text-sm font-medium text-gray-700">Teacher Comments</label>
                    <textarea name="teacher_comments" id="teacher_comments" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('teacher_comments', $mark->teacher_comments) }}</textarea>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach(['draft','published','final','marked','approved','pending'] as $s)
                            <option value="{{ $s }}" {{ old('status', $mark->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.exams.marks.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
