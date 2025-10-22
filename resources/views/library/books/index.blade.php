@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-book text-blue-500 mr-3"></i>
                    Library Books Management
                </h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('library.books.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Book
                    </a>
                    <a href="{{ route('library.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Library
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Books Management</h3>
            </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('library.books') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search books..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('library.books') }}" class="d-flex">
                                <select name="category" class="form-control me-2">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->books_count }})
                                        </option>
                                    @endforeach
                                </select>
                                <select name="status" class="form-control me-2">
                                    <option value="">All Status</option>
                                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                            </form>
                        </div>
                    </div>

                    <!-- Books Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cover</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                <tr>
                                    <td>
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                                 alt="{{ $book->title }}" class="img-thumbnail" style="width: 50px; height: 70px;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 70px;">
                                                <i class="mdi mdi-book mdi-24px text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $book->title }}</strong>
                                            @if($book->edition)
                                                <br><small class="text-muted">Edition: {{ $book->edition }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $book->author }}</td>
                                    <td>{{ $book->isbn ?? 'N/A' }}</td>
                                    <td>
                                        @if($book->category)
                                            <span class="badge bg-info">{{ $book->category->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($book->status == 'available')
                                            <span class="badge bg-success">Available</span>
                                        @elseif($book->status == 'borrowed')
                                            <span class="badge bg-warning">Borrowed</span>
                                        @elseif($book->status == 'maintenance')
                                            <span class="badge bg-danger">Maintenance</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($book->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('library.books.show', $book) }}" class="btn btn-sm btn-outline-info">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('library.books.edit', $book) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('library.books.destroy', $book) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this book?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-book-open-page-variant mdi-48px"></i>
                                            <p class="mt-2">No books found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $books->firstItem() ?? 0 }} to {{ $books->lastItem() ?? 0 }} of {{ $books->total() }} entries
                        </div>
                        <div>
                            {{ $books->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection