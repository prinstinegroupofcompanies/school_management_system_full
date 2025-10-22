@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Library Books</h1>
                    <p class="text-gray-600 mt-2">Browse and search available books</p>
                </div>
                <a href="{{ route('student.library.my-books') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    My Books
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Search and Filter Section -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('student.library.books') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Books</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Search by title, author, or ISBN..." 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Books</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Books Grid -->
        @if($books->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($books as $book)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                        <!-- Book Cover -->
                        <div class="aspect-w-3 aspect-h-4 bg-gray-200 rounded-t-lg">
                            @if($book->cover_image)
                                <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-48 object-cover rounded-t-lg">
                            @else
                                <div class="w-full h-48 flex items-center justify-center bg-gray-100 rounded-t-lg">
                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Book Details -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $book->title }}</h3>
                            <p class="text-sm text-gray-600 mb-2">by {{ $book->author->name ?? 'Unknown Author' }}</p>
                            
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $book->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                                @if($book->category)
                                    <span class="text-xs text-gray-500">{{ $book->category }}</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                                <span>ISBN: {{ $book->isbn ?? 'N/A' }}</span>
                                <span>{{ $book->published_year ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex space-x-2">
                                <a href="{{ route('student.library.books.show', $book) }}" 
                                   class="flex-1 bg-indigo-600 text-white text-center py-2 px-3 rounded-md text-sm hover:bg-indigo-700 transition-colors">
                                    View Details
                                </a>
                                @if($book->status === 'available')
                                    <button class="bg-green-600 text-white py-2 px-3 rounded-md text-sm hover:bg-green-700 transition-colors"
                                            onclick="borrowBook({{ $book->id }})">
                                        Borrow
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 5.477 5.754 5 7.5 5s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No books found</h3>
                <p class="text-gray-600">Try adjusting your search criteria or check back later for new books.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function borrowBook(bookId) {
    if (confirm('Are you sure you want to borrow this book?')) {
        // Here you would implement the borrow functionality
        // For now, just show a message
        alert('Borrow request submitted! Please wait for approval.');
    }
}
</script>
@endpush
@endsection
