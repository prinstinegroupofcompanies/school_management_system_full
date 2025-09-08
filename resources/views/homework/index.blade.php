@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Homework</h1>
        @if(auth()->user()->user_type === 'teacher')
        <a href="{{ route('homework.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Create Homework
        </a>
        @endif
    </div>

    @if($homeworks->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($homeworks as $homework)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-gray-900">{{ $homework->title }}</p>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $homework->status_color }}-100 text-{{ $homework->status_color }}-800">
                                            {{ ucfirst($homework->status) }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <span>{{ $homework->subject->name }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $homework->class->name }}</span>
                                        <span class="mx-2">•</span>
                                        <span>Due: {{ $homework->due_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('homework.show', $homework) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View</a>
                                @if(auth()->user()->user_type === 'teacher' && $homework->teacher_id === auth()->id())
                                <a href="{{ route('homework.edit', $homework) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        
        <div class="mt-6">
            {{ $homeworks->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No homework</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new homework assignment.</p>
            @if(auth()->user()->user_type === 'teacher')
            <div class="mt-6">
                <a href="{{ route('homework.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Create Homework
                </a>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
