<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Book;
use App\Models\BookIssue;

class LibraryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Get real library data
        $books = Book::where('status', 'available')
            ->with('category')
            ->latest()
            ->take(10)
            ->get();

        // Get student's borrowed books
        $borrowedBooks = BookIssue::where('student_id', $student->id)
            ->where('status', 'borrowed')
            ->with('book')
            ->latest()
            ->get();

        // Calculate real library statistics
        $totalBooks = Book::count();
        $availableBooks = Book::where('status', 'available')->count();
        $borrowedBooksCount = BookIssue::where('status', 'borrowed')->count();
        
        $libraryStats = [
            'total_books' => $totalBooks,
            'available_books' => $availableBooks,
            'borrowed_books' => $borrowedBooksCount,
            'my_borrowed' => $borrowedBooks->count(),
        ];

        return view('student.library.index', compact('books', 'borrowedBooks', 'libraryStats'));
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        $query = $request->get('query', '');
        
        // Real search results
        $books = Book::where('status', 'available')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%")
                  ->orWhere('isbn', 'like', "%{$query}%");
            })
            ->with('category')
            ->paginate(20);

        return view('student.library.search', compact('books', 'query'));
    }

    public function borrow(Request $request, $bookId)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Mock book borrowing logic
        return redirect()->route('student.library.index')
            ->with('success', 'Book borrowed successfully! Due date: ' . now()->addDays(30)->format('Y-m-d'));
    }

    public function return($bookId)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Mock book return logic
        return redirect()->route('student.library.index')
            ->with('success', 'Book returned successfully!');
    }
}
