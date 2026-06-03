@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Student Grade Sheets</h1>
                    <p class="mt-2 text-gray-600">{{ $student->user->name ?? 'Student' }} — {{ $student->classRoom->name ?? 'N/A' }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md">Back to Student</a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">By Term / Semester</h2>
            <p class="text-sm text-gray-500 mb-4">View or download grade sheet for each term or semester. Printable.</p>
            @if($periods->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($periods as $p)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                            <span class="font-medium">{{ $p['period_name'] }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.students.grades.sheet', [$student, $p['year'], $p['semester']]) }}" class="text-sm text-blue-600 hover:underline">View / Print</a>
                                <a href="{{ route('admin.students.grades.download', [$student, $p['year'], $p['semester']]) }}" class="text-sm text-green-600 hover:underline">Download PDF</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No term/semester grades on file.</p>
            @endif
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-3">End of Year (Full Year Average &amp; Promotion)</h2>
            <p class="text-sm text-gray-500 mb-4">Full year grade sheet with final yearly average. Promotion eligibility: 70% and above.</p>
            @if($yearsWithGrades->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($yearsWithGrades as $yr)
                        <div class="bg-white border-2 border-amber-200 rounded-lg p-4 flex items-center justify-between bg-amber-50/30">
                            <span class="font-medium">Academic Year {{ $yr }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.students.grades.full-year', [$student, $yr]) }}" class="text-sm text-amber-700 hover:underline">View / Print</a>
                                <a href="{{ route('admin.students.grades.download-full-year', [$student, $yr]) }}" class="text-sm text-amber-800 hover:underline">Download PDF</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No full-year data on file.</p>
            @endif
        </div>
    </div>
</div>
@endsection
