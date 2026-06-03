@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Add Exam Type</h1>
        <form method="POST" action="{{ route('admin.exams.types.store') }}" class="bg-white shadow rounded-lg p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Code *</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required maxlength="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Type *</label>
                    <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @foreach(['written','oral','practical','online','mixed'] as $t)
                            <option value="{{ $t }}" {{ old('type') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700">Total marks *</label>
                    <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks') }}" min="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('total_marks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="passing_marks" class="block text-sm font-medium text-gray-700">Passing marks *</label>
                    <input type="number" step="0.01" name="passing_marks" id="passing_marks" value="{{ old('passing_marks') }}" min="0" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('passing_marks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="weightage_percentage" class="block text-sm font-medium text-gray-700">Weightage % *</label>
                    <input type="number" name="weightage_percentage" id="weightage_percentage" value="{{ old('weightage_percentage', 100) }}" min="1" max="100" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('weightage_percentage')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex items-center gap-4">
                <label class="inline-flex items-center">
                    <input type="hidden" name="is_compulsory" value="0">
                    <input type="checkbox" name="is_compulsory" value="1" {{ old('is_compulsory') ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Compulsory</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="hidden" name="counts_for_final" value="0">
                    <input type="checkbox" name="counts_for_final" value="1" {{ old('counts_for_final', true) ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Counts for final</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.exams.types.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
