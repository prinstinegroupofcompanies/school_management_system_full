@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $material->title }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('teacher.study-materials.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Back to Materials
            </a>
            <a href="{{ route('teacher.study-materials.edit', $material) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Edit
            </a>
        </div>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Material Details</h3>
                <dl class="space-y-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Subject</dt>
                        <dd class="text-sm text-gray-900">{{ $material->subject->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Class</dt>
                        <dd class="text-sm text-gray-900">{{ $material->class->name ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="text-sm text-gray-900">{{ ucfirst($material->type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                        <dd class="text-sm text-gray-900">{{ $material->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                </dl>
            </div>

            @if($material->description)
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                <p class="text-sm text-gray-700">{{ $material->description }}</p>
            </div>
            @endif
        </div>

        @if($material->type === 'link' && $material->link)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">External Link</h3>
                <a href="{{ $material->link }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    {{ $material->link }}
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </div>
        @endif

        @if($material->file_path)
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">File</h3>
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $material->file_name }}</div>
                        <div class="text-sm text-gray-500">{{ $material->file_size_formatted }}</div>
                    </div>
                    <div>
                        <a href="{{ Storage::url($material->file_path) }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Download
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="flex justify-end space-x-4">
            <form method="POST" action="{{ route('teacher.study-materials.destroy', $material) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this material?')">
                    Delete Material
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
