<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Book::with(['category', 'subcategory']);

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%")
                      ->orWhere('publisher', 'like', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                switch ($request->status) {
                    case 'available':
                        $query->where('available_copies', '>', 0);
                        break;
                    case 'out_of_stock':
                        $query->where('available_copies', 0);
                        break;
                    case 'reserved':
                        $query->where('reserved_copies', '>', 0);
                        break;
                }
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('is_digital', $request->type === 'digital');
            }

            $books = $query->orderBy('created_at', 'desc')->paginate(15);
            $categories = BookCategory::with('subcategories')->get();

            return view('library.books.index', compact('books', 'categories'));
        } catch (\Exception $e) {
            \Log::error('BookController index error: ' . $e->getMessage());
            
            $books = new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 15, 1, ['path' => request()->url()]);
            $categories = collect();
            
            return view('library.books.index', compact('books', 'categories'))
                ->with('error', 'Failed to load books. Please try again.');
        }
    }

    public function create()
    {
        try {
            $categories = BookCategory::with('subcategories')->get();
            return view('library.books.create', compact('categories'));
        } catch (\Exception $e) {
            \Log::error('BookController create error: ' . $e->getMessage());
            return redirect()->route('library.books.index')
                ->with('error', 'Failed to load form. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'publisher' => 'nullable|string|max:255',
                'isbn' => 'nullable|string|max:20|unique:books,isbn',
                'edition' => 'nullable|string|max:50',
                'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'language' => 'nullable|string|max:50',
                'category_id' => 'required|exists:book_categories,id',
                'subcategory_id' => 'nullable|exists:book_subcategories,id',
                'is_digital' => 'required|boolean',
                'total_copies' => 'required|integer|min:1',
                'available_copies' => 'nullable|integer|min:0',
                'price' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'tags' => 'nullable|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'file_path' => 'nullable|file|mimes:pdf,epub,mobi,doc,docx|max:10240',
                'status' => 'required|in:available,maintenance,inactive',
            ]);

            $bookData = $request->only([
                'title', 'author', 'publisher', 'isbn', 'edition', 'publication_year',
                'language', 'category_id', 'subcategory_id', 'is_digital', 'total_copies',
                'available_copies', 'price', 'description', 'status'
            ]);

            // Handle tags
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $bookData['tags'] = array_filter($tags);
            }

            // Set available copies if not provided
            if (!$request->filled('available_copies')) {
                $bookData['available_copies'] = $bookData['total_copies'];
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $coverPath = $coverImage->store('book-covers', 'public');
                $bookData['cover_image'] = $coverPath;
            }

            // Handle file upload for digital books
            if ($request->hasFile('file_path') && $request->is_digital) {
                $file = $request->file('file_path');
                $filePath = $file->store('digital-books', 'public');
                $bookData['file_path'] = $filePath;
                $bookData['file_type'] = $file->getClientOriginalExtension();
                $bookData['file_size'] = $file->getSize();
            }

            // Set default values
            $bookData['borrowed_copies'] = 0;
            $bookData['reserved_copies'] = 0;
            $bookData['views_count'] = 0;
            $bookData['downloads_count'] = 0;
            $bookData['rating'] = 0;
            $bookData['rating_count'] = 0;
            $bookData['is_active'] = true;
            $bookData['currency'] = 'LRD';

            $book = Book::create($bookData);

            return redirect()->route('library.books.index')
                ->with('success', 'Book added successfully.');

        } catch (\Exception $e) {
            \Log::error('BookController store error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add book. Please try again.');
        }
    }

    public function show(Book $book)
    {
        try {
            $book->load(['category', 'subcategory', 'bookIssues.member.user']);
            
            // Get recent issues
            $recentIssues = $book->bookIssues()
                ->with(['member.user'])
                ->latest()
                ->limit(10)
                ->get();

            // Get statistics
            $stats = [
                'total_issues' => $book->bookIssues()->count(),
                'current_issues' => $book->bookIssues()->where('status', 'issued')->count(),
                'total_views' => $book->views_count,
                'total_downloads' => $book->downloads_count,
                'average_rating' => $book->rating,
            ];

            return view('library.books.show', compact('book', 'recentIssues', 'stats'));
        } catch (\Exception $e) {
            \Log::error('BookController show error: ' . $e->getMessage());
            return redirect()->route('library.books.index')
                ->with('error', 'Failed to load book details.');
        }
    }

    public function edit(Book $book)
    {
        try {
            $categories = BookCategory::with('subcategories')->get();
            $subcategories = $book->category ? $book->category->subcategories : collect();
            
            return view('library.books.edit', compact('book', 'categories', 'subcategories'));
        } catch (\Exception $e) {
            \Log::error('BookController edit error: ' . $e->getMessage());
            return redirect()->route('library.books.index')
                ->with('error', 'Failed to load book for editing.');
        }
    }

    public function update(Request $request, Book $book)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'publisher' => 'nullable|string|max:255',
                'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
                'edition' => 'nullable|string|max:50',
                'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'language' => 'nullable|string|max:50',
                'category_id' => 'required|exists:book_categories,id',
                'subcategory_id' => 'nullable|exists:book_subcategories,id',
                'is_digital' => 'required|boolean',
                'total_copies' => 'required|integer|min:1',
                'available_copies' => 'nullable|integer|min:0',
                'price' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'tags' => 'nullable|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'file_path' => 'nullable|file|mimes:pdf,epub,mobi,doc,docx|max:10240',
                'status' => 'required|in:available,maintenance,inactive',
            ]);

            $bookData = $request->only([
                'title', 'author', 'publisher', 'isbn', 'edition', 'publication_year',
                'language', 'category_id', 'subcategory_id', 'is_digital', 'total_copies',
                'available_copies', 'price', 'description', 'status'
            ]);

            // Handle tags
            if ($request->filled('tags')) {
                $tags = array_map('trim', explode(',', $request->tags));
                $bookData['tags'] = array_filter($tags);
            }

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                // Delete old cover image
                if ($book->cover_image) {
                    Storage::disk('public')->delete($book->cover_image);
                }
                
                $coverImage = $request->file('cover_image');
                $coverPath = $coverImage->store('book-covers', 'public');
                $bookData['cover_image'] = $coverPath;
            }

            // Handle file upload for digital books
            if ($request->hasFile('file_path') && $request->is_digital) {
                // Delete old file
                if ($book->file_path) {
                    Storage::disk('public')->delete($book->file_path);
                }
                
                $file = $request->file('file_path');
                $filePath = $file->store('digital-books', 'public');
                $bookData['file_path'] = $filePath;
                $bookData['file_type'] = $file->getClientOriginalExtension();
                $bookData['file_size'] = $file->getSize();
            }

            $book->update($bookData);

            return redirect()->route('library.books.index')
                ->with('success', 'Book updated successfully.');

        } catch (\Exception $e) {
            \Log::error('BookController update error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update book. Please try again.');
        }
    }

    public function destroy(Book $book)
    {
        try {
            // Delete associated files
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }

            $book->delete();

            return redirect()->route('library.books.index')
                ->with('success', 'Book deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('BookController destroy error: ' . $e->getMessage());
            return redirect()->route('library.books.index')
                ->with('error', 'Failed to delete book. Please try again.');
        }
    }

    public function download(Book $book)
    {
        try {
            if (!$book->is_digital || !$book->file_path) {
                return redirect()->back()->with('error', 'Digital file not available.');
            }

            // Increment download count
            $book->incrementDownloads();

            return Storage::disk('public')->download($book->file_path, $book->title . '.' . $book->file_type);

        } catch (\Exception $e) {
            \Log::error('BookController download error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download book.');
        }
    }
}
