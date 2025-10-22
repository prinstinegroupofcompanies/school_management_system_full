@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Books</h1>
                    <p class="text-gray-600 mt-2">Manage your borrowed books and view history</p>
                </div>
                <a href="{{ route('student.library.books') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Browse Books
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Currently Borrowed Books -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Currently Borrowed Books</h2>
                <p class="text-sm text-gray-600">Books you currently have borrowed</p>
            </div>
            
            @if($borrowedBooks->count() > 0)
                <div class="p-6">
                    <div class="grid gap-4">
                        @foreach($borrowedBooks as $issue)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $issue->book->title }}</h3>
                                        <p class="text-sm text-gray-600">by {{ $issue->book->author->name ?? 'Unknown Author' }}</p>
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Borrowed: {{ $issue->issue_date ? \Carbon\Carbon::parse($issue->issue_date)->format('M d, Y') : 'N/A' }}</span>
                                            <span>Due: {{ $issue->due_date ? \Carbon\Carbon::parse($issue->due_date)->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $issue->due_date && \Carbon\Carbon::parse($issue->due_date)->isPast() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $issue->due_date && \Carbon\Carbon::parse($issue->due_date)->isPast() ? 'Overdue' : 'On Time' }}
                                        </span>
                                        <button class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors"
                                                onclick="returnBook({{ $issue->id }})">
                                            Return
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-6 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No borrowed books</h3>
                    <p class="text-gray-600">You don't have any books borrowed at the moment.</p>
                </div>
            @endif
        </div>

        <!-- Returned Books History -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Returned Books History</h2>
                <p class="text-sm text-gray-600">Books you have returned recently</p>
            </div>
            
            @if($returnedBooks->count() > 0)
                <div class="p-6">
                    <div class="grid gap-4">
                        @foreach($returnedBooks as $issue)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $issue->book->title }}</h3>
                                        <p class="text-sm text-gray-600">by {{ $issue->book->author->name ?? 'Unknown Author' }}</p>
                                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                            <span>Borrowed: {{ $issue->issue_date ? \Carbon\Carbon::parse($issue->issue_date)->format('M d, Y') : 'N/A' }}</span>
                                            <span>Returned: {{ $issue->returned_at ? \Carbon\Carbon::parse($issue->returned_at)->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Returned
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-6 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No returned books</h3>
                    <p class="text-gray-600">You haven't returned any books yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function returnBook(issueId) {
    if (confirm('Are you sure you want to return this book?')) {
        // Here you would implement the return functionality
        // For now, just show a message
        alert('Return request submitted! Please return the book to the library.');
    }
}
</script>
@endpush
@endsection
