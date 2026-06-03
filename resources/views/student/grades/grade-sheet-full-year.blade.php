@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">End of Year Grade Sheet</h1>
                    <p class="mt-2 text-gray-600">Academic Year {{ $year }} – {{ $student->user->name }}</p>
                </div>
                <div class="flex space-x-3 no-print">
                    <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md">Print</button>
                    @if(request()->routeIs('admin.*'))
                        <a href="{{ route('admin.students.grades', $student) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md">Back to Grade Sheets</a>
                        <a href="{{ route('admin.students.grades.download-full-year', [$student, $year]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md">Download PDF</a>
                    @else
                        <a href="{{ route('student.grades.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md">Back to Periods</a>
                        <a href="{{ route('student.grades.download-full-year', ['year' => $year]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md">Download PDF</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl rounded-lg overflow-hidden border border-gray-300">
            <div class="p-8">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold">{{ $school->name ?? config('app.name') }}</h2>
                    <p class="text-gray-600">{{ $school->address ?? '' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="space-y-2">
                        <div class="flex"><span class="font-semibold w-32">Student:</span><span>{{ $student->user->name }}</span></div>
                        <div class="flex"><span class="font-semibold w-32">Class:</span><span>{{ $student->classRoom->name ?? 'N/A' }}</span></div>
                        <div class="flex"><span class="font-semibold w-32">Academic Year:</span><span>{{ $year }}</span></div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex"><span class="font-semibold w-40">Yearly Average:</span><span class="font-bold">{{ number_format($stats['yearly_average'], 1) }}%</span></div>
                        <div class="flex"><span class="font-semibold w-40">Promotion (70%+):</span>
                            <span class="font-semibold {{ $stats['eligible_for_promotion'] ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $stats['eligible_for_promotion'] ? 'Eligible' : 'Not eligible' }}
                            </span>
                        </div>
                    </div>
                </div>

                <h3 class="text-lg font-bold mb-4">Grades by Term / Semester</h3>
                @foreach($bySemester as $sem => $semGrades)
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-800 mb-2">{{ $sem == 1 ? 'Semester 1 (Term 1)' : ($sem == 2 ? 'Semester 2 (Term 2)' : "Period {$sem}") }}</h4>
                        <div class="border border-gray-200 rounded overflow-hidden">
                            <table class="min-w-full">
                                <thead class="bg-gray-100"><tr><th class="px-4 py-2 text-left">Subject</th><th class="px-4 py-2 text-right">Average</th></tr></thead>
                                <tbody>
                                    @foreach($semGrades as $g)
                                        <tr class="border-t border-gray-200"><td class="px-4 py-2">{{ $g->subject->name ?? 'N/A' }}</td><td class="px-4 py-2 text-right">{{ $g->year_avg ? number_format($g->year_avg, 1) : '-' }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div class="border-t-2 border-gray-300 mt-6 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="font-bold">Final Yearly Average (all terms):</span>
                        <span class="font-bold text-lg">{{ number_format($stats['yearly_average'], 1) }}%</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Promotion requires a final yearly average of 70% or above. This student is {{ $stats['eligible_for_promotion'] ? 'eligible' : 'not eligible' }} for promotion.</p>
                </div>

                <div class="mt-8 flex justify-end">
                    @if($adminSignature)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($adminSignature) }}" alt="Signature" class="h-14 w-28 object-contain border-b border-gray-400">
                    @else
                        <div class="h-14 w-28 border-b border-gray-400"></div>
                    @endif
                    <div class="ml-2 text-xs">Authorized Signature</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
