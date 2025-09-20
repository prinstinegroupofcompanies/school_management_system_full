@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Grade</h1>
                    <p class="mt-2 text-gray-600">Modify grade details</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.grades.show', $grade ?? 1) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        View Grade
                    </a>
                    <a href="{{ route('admin.grades.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Grades
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form method="POST" action="{{ route('admin.grades.update', $grade ?? 1) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="raw_score" class="block text-sm font-medium text-gray-700 mb-2">Raw Score</label>
                        <input type="number" name="raw_score" id="raw_score" required min="0" 
                               value="{{ old('raw_score', $grade->raw_score ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('raw_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">Max Score</label>
                        <input type="number" name="max_score" id="max_score" required min="1" 
                               value="{{ old('max_score', $grade->max_score ?? 100) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('max_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="admin_comments" class="block text-sm font-medium text-gray-700 mb-2">Admin Comments</label>
                        <textarea name="admin_comments" id="admin_comments" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Administrative notes about this grade...">{{ old('admin_comments', $grade->admin_comments ?? '') }}</textarea>
                        @error('admin_comments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.grades.show', $grade ?? 1) }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Update Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
