@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Student Attendance - {{ $class->name }} ({{ $date }})</h1>
    <form method="POST" action="{{ route('attendance.student.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="date" value="{{ $date }}">
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Student</th>
                        <th class="p-2 text-left">Status</th>
                        <th class="p-2 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr class="border-t">
                        <td class="p-2">{{ $student->user->name ?? 'N/A' }}</td>
                        <td class="p-2">
                            <select name="attendance[{{ $loop->index }}][status]" class="border rounded px-2 py-1">
                                @foreach(['present','absent','late','excused','dropout'] as $st)
                                    <option value="{{ $st }}" {{ ($existingAttendance[$student->id] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="attendance[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                        </td>
                        <td class="p-2">
                            <input type="text" name="attendance[{{ $loop->index }}][remarks]" value="" class="border rounded px-2 py-1 w-full">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Attendance</button>
        </div>
    </form>
</div>
@endsection


