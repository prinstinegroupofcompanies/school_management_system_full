@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $material->title }}</h1>
                    <p class="text-gray-600 mt-2">{{ $material->subject->name ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('student.study-materials.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Materials
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <!-- Material Info -->
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $material->title }}</h2>
                            <p class="text-gray-600 mt-2">{{ $material->subject->name ?? 'N/A' }}</p>
                            
                            @if($material->description)
                                <div class="mt-4">
                                    <h3 class="text-sm font-medium text-gray-900 mb-2">Description</h3>
                                    <p class="text-gray-700">{{ $material->description }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="ml-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($material->type) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Material Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Material Information</h3>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm text-gray-500">Subject</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $material->subject->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Teacher</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $material->teacher->user->name ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Type</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($material->type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Uploaded</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $material->created_at->format('F j, Y g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Actions</h3>
                        <div class="space-y-3">
                            @if($material->type === 'file' && $material->file_path)
                                <a href="{{ route('student.study-materials.download', $material) }}" 
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download File
                                </a>
                            @elseif($material->type === 'link' && $material->link)
                                <a href="{{ $material->link }}" target="_blank"
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Open Link
                                </a>
                            @endif
                            
                            @if($material->type === 'text')
                                <div class="text-sm text-gray-500">
                                    This is a text-based material. View the content below.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content Display -->
                @if($material->type === 'text' && $material->content)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Content</h3>
                        <div class="prose prose-sm max-w-none">
                            {!! nl2br(e($material->content)) !!}
                        </div>
                    </div>
                @elseif($material->type === 'link' && $material->link)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">External Resource</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-2">This material links to an external resource:</p>
                            <a href="{{ $material->link }}" target="_blank" class="text-blue-600 hover:text-blue-800 break-all">
                                {{ $material->link }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
