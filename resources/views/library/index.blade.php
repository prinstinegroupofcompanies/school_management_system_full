@extends('layouts.app')

@section('title', 'Library Management')

@section('content')
<div class="card-premium">
    <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 2rem;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-book me-3"></i>Library Management</h3>
            <a href="{{ route('library.books.create') }}" class="btn-premium">
                <i class="fas fa-plus me-2"></i> Add Book
            </a>
        </div>
    </div>
    
    <div class="card-body p-4">
        <!-- Statistics Grid -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-primary rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-book text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">{{ $books->total() ?? 0 }}</h4>
                        <p class="text-gray-600 font-semibold">Total Books</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-secondary rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-layer-group text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">{{ $categories->count() ?? 0 }}</h4>
                        <p class="text-gray-600 font-semibold">Categories</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-success rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-book-open text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">0</h4>
                        <p class="text-gray-600 font-semibold">Available Books</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card-premium h-100">
                    <div class="card-body text-center">
                        <div class="h-16 w-16 bg-gradient-warning rounded-xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-friends text-white text-2xl"></i>
                        </div>
                        <h4 class="text-3xl font-bold text-gray-900 mb-1">0</h4>
                        <p class="text-gray-600 font-semibold">Active Borrowers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Books Section -->
        <div class="card-premium">
            <div class="card-header" style="background: var(--primary-gradient); color: white; padding: 1.5rem;">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>Books Collection</h4>
            </div>
            
            <div class="card-body p-0">
                @if(isset($books) && $books->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-premium mb-0">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-book me-2"></i>Title</th>
                                    <th><i class="fas fa-user me-2"></i>Author</th>
                                    <th><i class="fas fa-tag me-2"></i>Category</th>
                                    <th><i class="fas fa-barcode me-2"></i>ISBN</th>
                                    <th><i class="fas fa-check-circle me-2"></i>Available</th>
                                    <th><i class="fas fa-layer-group me-2"></i>Total</th>
                                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($books as $book)
                                <tr>
                                    <td><strong>{{ $book->title }}</strong></td>
                                    <td>{{ $book->author }}</td>
                                    <td>{{ $book->category->name ?? 'N/A' }}</td>
                                    <td><code>{{ $book->isbn }}</code></td>
                                    <td><span class="badge bg-success">{{ $book->available_copies }}</span></td>
                                    <td><span class="badge bg-info">{{ $book->total_copies }}</span></td>
                                    <td>
                                        <span class="status-badge-premium {{ $book->status }}">
                                            {{ ucfirst($book->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-premium btn-sm me-1">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn-premium btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
                        <h5 class="text-xl font-semibold text-gray-600 mb-2">No Books Found</h5>
                        <p class="text-gray-500 mb-4">Start building your library collection by adding the first book.</p>
                        <a href="{{ route('library.books.create') }}" class="btn-premium">
                            <i class="fas fa-plus me-2"></i> Add First Book
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection