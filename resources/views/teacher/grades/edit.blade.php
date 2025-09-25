@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Grade</h1>
                    <p class="mt-2 text-gray-600">Update grade information for {{ $grade->student->user->name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('teacher.grades.show', $grade) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Grade Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Student Information -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500">Student Name</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->student->user->name }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Admission Number</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->student->admission_number }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Class</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->class->name }}</div>
                </div>
            </div>
        </div>

        <!-- Subject Information -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subject Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500">Subject</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->subject->name }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Academic Year</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $grade->academic_year }} - Semester {{ $grade->semester }}</div>
                </div>
            </div>
        </div>

        <!-- Grade Edit Form -->
        <form action="{{ route('teacher.grades.update', $grade) }}" method="POST" class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <!-- Semester 1 Grades -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Semester 1 Grades
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="sem1_p1" class="block text-sm font-medium text-gray-700 mb-1">Period 1</label>
                            <input type="number" name="sem1_p1" id="sem1_p1" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem1_p1') border-red-500 @enderror" 
                                   value="{{ old('sem1_p1', $grade->sem1_p1) }}" min="0" max="100" step="0.01">
                            @error('sem1_p1')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sem1_p2" class="block text-sm font-medium text-gray-700 mb-1">Period 2</label>
                            <input type="number" name="sem1_p2" id="sem1_p2" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem1_p2') border-red-500 @enderror" 
                                   value="{{ old('sem1_p2', $grade->sem1_p2) }}" min="0" max="100" step="0.01">
                            @error('sem1_p2')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sem1_p3" class="block text-sm font-medium text-gray-700 mb-1">Period 3</label>
                            <input type="number" name="sem1_p3" id="sem1_p3" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem1_p3') border-red-500 @enderror" 
                                   value="{{ old('sem1_p3', $grade->sem1_p3) }}" min="0" max="100" step="0.01">
                            @error('sem1_p3')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sem1_exam" class="block text-sm font-medium text-gray-700 mb-1">Semester 1 Exam</label>
                            <input type="number" name="sem1_exam" id="sem1_exam" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem1_exam') border-red-500 @enderror" 
                                   value="{{ old('sem1_exam', $grade->sem1_exam) }}" min="0" max="100" step="0.01">
                            @error('sem1_exam')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Semester 2 Grades -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                        Semester 2 Grades
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="sem2_p4" class="block text-sm font-medium text-gray-700 mb-1">Period 4</label>
                            <input type="number" name="sem2_p4" id="sem2_p4" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem2_p4') border-red-500 @enderror" 
                                   value="{{ old('sem2_p4', $grade->sem2_p4) }}" min="0" max="100" step="0.01">
                            @error('sem2_p4')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sem2_p5" class="block text-sm font-medium text-gray-700 mb-1">Period 5</label>
                            <input type="number" name="sem2_p5" id="sem2_p5" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem2_p5') border-red-500 @enderror" 
                                   value="{{ old('sem2_p5', $grade->sem2_p5) }}" min="0" max="100" step="0.01">
                            @error('sem2_p5')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sem2_p6" class="block text-sm font-medium text-gray-700 mb-1">Period 6</label>
                            <input type="number" name="sem2_p6" id="sem2_p6" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem2_p6') border-red-500 @enderror" 
                                   value="{{ old('sem2_p6', $grade->sem2_p6) }}" min="0" max="100" step="0.01">
                            @error('sem2_p6')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="sem2_exam" class="block text-sm font-medium text-gray-700 mb-1">Semester 2 Exam</label>
                            <input type="number" name="sem2_exam" id="sem2_exam" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('sem2_exam') border-red-500 @enderror" 
                                   value="{{ old('sem2_exam', $grade->sem2_exam) }}" min="0" max="100" step="0.01">
                            @error('sem2_exam')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Selection -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror">
                    <option value="pending" {{ old('status', $grade->status) == 'pending' ? 'selected' : '' }}>Submit for Approval</option>
                    <option value="draft" {{ old('status', $grade->status) == 'draft' ? 'selected' : '' }}>Save as Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Choose "Submit for Approval" to send for admin review, or "Save as Draft" to keep as draft.</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-save mr-2"></i>
                        Update Grade
                    </button>
                    <a href="{{ route('teacher.grades.show', $grade) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                </div>
                
                @if($grade->status !== 'approved')
                    <form action="{{ route('teacher.grades.destroy', $grade) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this grade? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Grade
                        </button>
                    </form>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate averages when grades are entered
    const gradeInputs = document.querySelectorAll('input[type="number"]');
    
    function calculateAverages() {
        // Calculate Semester 1 average
        const sem1Grades = [
            parseFloat(document.getElementById('sem1_p1').value) || 0,
            parseFloat(document.getElementById('sem1_p2').value) || 0,
            parseFloat(document.getElementById('sem1_p3').value) || 0,
            parseFloat(document.getElementById('sem1_exam').value) || 0
        ].filter(grade => grade > 0);
        
        // Calculate Semester 2 average
        const sem2Grades = [
            parseFloat(document.getElementById('sem2_p4').value) || 0,
            parseFloat(document.getElementById('sem2_p5').value) || 0,
            parseFloat(document.getElementById('sem2_p6').value) || 0,
            parseFloat(document.getElementById('sem2_exam').value) || 0
        ].filter(grade => grade > 0);
        
        // You could display calculated averages here if needed
        // For now, the backend will handle the calculations
    }
    
    gradeInputs.forEach(input => {
        input.addEventListener('input', calculateAverages);
    });
});
</script>
@endsection
