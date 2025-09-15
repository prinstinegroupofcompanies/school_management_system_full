@extends('layouts.app')

@section('title', 'Library Search')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Library Search</h1>
                    <p class="mt-2 text-gray-600">Find books and resources in the library</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('student.library.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Library
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <form method="GET" action="{{ route('student.library.search') }}" class="flex space-x-4">
                <div class="flex-1">
                    <label for="query" class="sr-only">Search books</label>
                    <input type="text" 
                           name="query" 
                           id="query" 
                           value="{{ $query }}"
                           placeholder="Search by title, author, or ISBN..."
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
            </form>
        </div>

        <!-- Search Results -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if($query)
                            Search Results for "{{ $query }}"
                        @else
                            All Available Books
                        @endif
                    </h3>
                    <span class="text-sm text-gray-500">
                        {{ $books->total() }} {{ Str::plural('book', $books->total()) }} found
                    </span>
                </div>
            </div>

            @if($books->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($books as $book)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start space-x-4">
                        <!-- Book Cover Placeholder -->
                        <div class="flex-shrink-0">
                            <div class="w-16 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 19 16.5 19c-1.746 0-3.332-.477-4.5-1.253"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Book Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium text-gray-900 mb-1">
                                        {{ $book->title }}
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-2">by {{ $book->author }}</p>
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            ISBN: {{ $book->isbn }}
                                        </span>
                                        @if($book->category)
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            {{ $book->category->name ?? 'Uncategorized' }}
                                        </span>
                                        @endif
                                    </div>

                                    @if($book->description)
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ Str::limit($book->description, 200) }}
                                    </p>
                                    @endif

                                    <div class="flex items-center space-x-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Available
                                        </span>
                                        @if($book->published_year)
                                        <span class="text-sm text-gray-500">
                                            Published: {{ $book->published_year }}
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="flex-shrink-0 ml-4">
                                    <form method="POST" action="{{ route('student.library.borrow', $book->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Borrow Book
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($books->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $books->links() }}
            </div>
            @endif

            @else
            <!-- No Results -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0112 4c-2.34 0-4.29 1.009-5.824 2.709"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No books found</h3>
                <p class="text-gray-500 mb-4">
                    @if($query)
                        No books match your search for "{{ $query }}". Try different keywords or browse all books.
                    @else
                        No books are currently available in the library.
                    @endif
                </p>
                @if($query)
                <a href="{{ route('student.library.search') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    View All Books
                </a>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('query');
    if (searchInput) {
        searchInput.focus();
    }
});

// Confirm book borrowing
document.addEventListener('submit', function(e) {
    if (e.target.matches('form[action*="borrow"]')) {
        e.preventDefault();
        if (confirm('Are you sure you want to borrow this book?')) {
            e.target.submit();
        }
    }
});
</script>
@endsection
