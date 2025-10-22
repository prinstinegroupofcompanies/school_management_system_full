<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\LibraryMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.');
            }

            // Get library statistics
            $stats = [
                'total_books' => Book::count(),
                'available_books' => Book::where('status', 'available')->count(),
                'my_borrowed' => BookIssue::where('student_id', $student->id)
                    ->where('status', 'borrowed')
                    ->count(),
                'overdue_books' => BookIssue::where('student_id', $student->id)
                    ->where('status', 'borrowed')
                    ->where('due_date', '<', now())
                    ->count(),
            ];

            // Get student's borrowed books
            $borrowedBooks = BookIssue::with(['book', 'book.author'])
                ->where('student_id', $student->id)
                ->where('status', 'borrowed')
                ->orderBy('due_date', 'asc')
                ->get();

            // Get recent book issues
            $recentIssues = BookIssue::with(['book'])
                ->where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Get available books (recently added)
            $availableBooks = Book::with(['author'])
                ->where('status', 'available')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('student.library.index', compact(
                'stats', 
                'borrowedBooks', 
                'recentIssues', 
                'availableBooks'
            ));
        } catch (\Exception $e) {
            \Log::error('Student LibraryController index error: ' . $e->getMessage());
            
            // Fallback data
            $stats = [
                'total_books' => 0,
                'available_books' => 0,
                'my_borrowed' => 0,
                'overdue_books' => 0,
            ];
            $borrowedBooks = collect();
            $recentIssues = collect();
            $availableBooks = collect();
            
            return view('student.library.index', compact(
                'stats', 
                'borrowedBooks', 
                'recentIssues', 
                'availableBooks'
            ));
        }
    }

    public function books(Request $request)
    {
        try {
            $query = Book::with(['author']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('isbn', 'like', '%' . $request->search . '%')
                      ->orWhere('author', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by category
            if ($request->has('category') && $request->category) {
                $query->where('category', $request->category);
            }

            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            $books = $query->orderBy('created_at', 'desc')->paginate(12);

            $categories = Book::distinct()->pluck('category')->filter();

            return view('student.library.books', compact('books', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Student LibraryController books error: ' . $e->getMessage());
            
            $books = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(),
                0,
                12,
                1,
                ['path' => request()->url()]
            );
            $categories = collect();
            
            return view('student.library.books', compact('books', 'categories'));
        }
    }

    public function show(Book $book)
    {
        try {
            $book->load(['author']);
            
            // Check if student has borrowed this book
            $user = Auth::user();
            $student = $user->student;
            $isBorrowed = false;
            $borrowRecord = null;
            
            if ($student) {
                $borrowRecord = BookIssue::where('book_id', $book->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'borrowed')
                    ->first();
                $isBorrowed = $borrowRecord ? true : false;
            }

            return view('student.library.show', compact('book', 'isBorrowed', 'borrowRecord'));
        } catch (\Exception $e) {
            \Log::error('Student LibraryController show error: ' . $e->getMessage());
            return redirect()->route('student.library.index')
                ->with('error', 'Book not found.');
        }
    }

    public function myBooks()
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.');
            }

            $borrowedBooks = BookIssue::with(['book', 'book.author'])
                ->where('student_id', $student->id)
                ->where('status', 'borrowed')
                ->orderBy('due_date', 'asc')
                ->get();

            $returnedBooks = BookIssue::with(['book', 'book.author'])
                ->where('student_id', $student->id)
                ->where('status', 'returned')
                ->orderBy('returned_at', 'desc')
                ->limit(10)
                ->get();

            return view('student.library.my-books', compact('borrowedBooks', 'returnedBooks'));
        } catch (\Exception $e) {
            \Log::error('Student LibraryController myBooks error: ' . $e->getMessage());
            
            $borrowedBooks = collect();
            $returnedBooks = collect();
            
            return view('student.library.my-books', compact('borrowedBooks', 'returnedBooks'));
        }
    }

    public function search(Request $request)
    {
        try {
            $user = Auth::user();
            $student = $user->student;
            
            if (!$student) {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Student profile not found.');
            }

            // Get search parameters
            $search = $request->get('search', '');
            $category = $request->get('category', '');
            $author = $request->get('author', '');

            // Build search query
            $query = Book::with(['author']);

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($category) {
                $query->where('category', $category);
            }

            if ($author) {
                $query->whereHas('author', function($q) use ($author) {
                    $q->where('name', 'like', "%{$author}%");
                });
            }

            $books = $query->orderBy('title')->paginate(12);
            $categories = Book::distinct()->pluck('category')->filter();

            return view('student.library.search', compact('books', 'categories', 'search', 'category', 'author'));
        } catch (\Exception $e) {
            \Log::error('Student LibraryController search error: ' . $e->getMessage());
            
            $books = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 0, 12, 1, ['path' => request()->url()]
            );
            $categories = collect();
            $search = '';
            $category = '';
            $author = '';
            
            return view('student.library.search', compact('books', 'categories', 'search', 'category', 'author'));
        }
    }
}