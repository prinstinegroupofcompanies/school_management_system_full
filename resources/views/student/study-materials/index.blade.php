@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Study Materials</h1>
                    <p class="text-gray-600 mt-2">Access learning resources for your subjects</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if($materials && $materials->count() > 0)
            <div class="grid gap-6">
                @foreach($materials as $material)
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($material->type === 'file')
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $material->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $material->subject->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                @if($material->description)
                                    <p class="text-gray-600 mt-3">{{ Str::limit($material->description, 200) }}</p>
                                @endif
                                
                                <div class="flex items-center space-x-4 mt-4 text-sm text-gray-500">
                                    <span>Uploaded: {{ $material->created_at->format('M j, Y') }}</span>
                                    @if($material->teacher)
                                        <span>By: {{ $material->teacher->user->name ?? 'Unknown' }}</span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($material->type) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ml-6 flex flex-col space-y-2">
                                <a href="{{ route('student.study-materials.show', $material) }}" 
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                    View Details
                                </a>
                                @if($material->type === 'file' && $material->file_path)
                                    <a href="{{ route('student.study-materials.download', $material) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                        Download
                                    </a>
                                @elseif($material->type === 'link' && $material->link)
                                    <a href="{{ $material->link }}" target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                        Open Link
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($materials->hasPages())
                <div class="mt-8">
                    {{ $materials->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <div class="max-w-md mx-auto">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No study materials</h3>
                    <p class="mt-1 text-sm text-gray-500">No study materials have been shared for your subjects yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
