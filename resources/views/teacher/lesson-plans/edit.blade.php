@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Edit Lesson Plan</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('teacher.lesson-plans.show', $lessonPlan) }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Lesson Plan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('teacher.lesson-plans.update', $lessonPlan) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Basic Information</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700">Lesson Title *</label>
                                        <input type="text" name="title" id="title" value="{{ old('title', $lessonPlan->title) }}" required
                                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('title') border-red-300 @enderror">
                                        @error('title')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                                        <textarea name="description" id="description" rows="3" required
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('description') border-red-300 @enderror">{{ old('description', $lessonPlan->description) }}</textarea>
                                        @error('description')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject *</label>
                                            <select name="subject_id" id="subject_id" required
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('subject_id') border-red-300 @enderror">
                                                <option value="">Select Subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}" {{ old('subject_id', $lessonPlan->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('subject_id')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="class_id" class="block text-sm font-medium text-gray-700">Class *</label>
                                            <select name="class_id" id="class_id" required
                                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('class_id') border-red-300 @enderror">
                                                <option value="">Select Class</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}" {{ old('class_id', $lessonPlan->class_id) == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('class_id')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label for="lesson_date" class="block text-sm font-medium text-gray-700">Lesson Date *</label>
                                            <input type="date" name="lesson_date" id="lesson_date" value="{{ old('lesson_date', $lessonPlan->lesson_date ? $lessonPlan->lesson_date->format('Y-m-d') : '') }}" required
                                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('lesson_date') border-red-300 @enderror">
                                            @error('lesson_date')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $lessonPlan->start_time ? \Carbon\Carbon::createFromFormat('H:i:s', $lessonPlan->start_time)->format('H:i') : '') }}" required
                                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('start_time') border-red-300 @enderror">
                                            @error('start_time')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $lessonPlan->end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $lessonPlan->end_time)->format('H:i') : '') }}" required
                                                   class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('end_time') border-red-300 @enderror">
                                            @error('end_time')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lesson Details -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Lesson Details</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="objectives" class="block text-sm font-medium text-gray-700">Learning Objectives *</label>
                                        <textarea name="objectives" id="objectives" rows="4" required
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('objectives') border-red-300 @enderror">{{ old('objectives', $lessonPlan->objectives) }}</textarea>
                                        @error('objectives')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="materials_needed" class="block text-sm font-medium text-gray-700">Materials Needed *</label>
                                        <textarea name="materials_needed" id="materials_needed" rows="3" required
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('materials_needed') border-red-300 @enderror">{{ old('materials_needed', $lessonPlan->materials_needed) }}</textarea>
                                        @error('materials_needed')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="activities" class="block text-sm font-medium text-gray-700">Activities *</label>
                                        <textarea name="activities" id="activities" rows="4" required
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('activities') border-red-300 @enderror">{{ old('activities', $lessonPlan->activities) }}</textarea>
                                        @error('activities')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="assessment" class="block text-sm font-medium text-gray-700">Assessment *</label>
                                        <textarea name="assessment" id="assessment" rows="3" required
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('assessment') border-red-300 @enderror">{{ old('assessment', $lessonPlan->assessment) }}</textarea>
                                        @error('assessment')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="homework" class="block text-sm font-medium text-gray-700">Homework *</label>
                                        <textarea name="homework" id="homework" rows="3" required
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('homework') border-red-300 @enderror">{{ old('homework', $lessonPlan->homework) }}</textarea>
                                        @error('homework')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                        <textarea name="notes" id="notes" rows="3"
                                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('notes') border-red-300 @enderror">{{ old('notes', $lessonPlan->notes) }}</textarea>
                                        @error('notes')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div class="mt-8">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Attachments</h3>
                        
                        <!-- Current Attachments -->
                        @if($lessonPlan->attachments && count($lessonPlan->attachments) > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Current Attachments</h4>
                            <ul class="space-y-2">
                                @foreach($lessonPlan->attachments as $index => $attachment)
                                <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $attachment['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- New Attachments -->
                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">Add New Files</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="attachments" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload files</span>
                                            <input id="attachments" name="attachments[]" type="file" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG up to 2MB each</p>
                                </div>
                            </div>
                            @error('attachments')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('teacher.lesson-plans.show', $lessonPlan) }}" 
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Lesson Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
