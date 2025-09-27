<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $student = $user->student;
        } catch (\Exception $e) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student profile not available. Please contact administrator.');
        }
        
        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student record not found. Please contact administrator.');
        }

        // Get real-time library statistics
        $libraryStats = [
            'total_books' => \App\Models\Book::count(),
            'available_books' => \App\Models\Book::where('status', 'available')->count(),
            'borrowed_books' => \App\Models\BookIssue::where('status', 'borrowed')->count(),
            'my_borrowed' => \App\Models\BookIssue::where('student_id', $student->id)->where('status', 'borrowed')->count(),
        ];

        // Get student's borrowed books
        $myBooks = \App\Models\BookIssue::with(['book'])
            ->where('student_id', $student->id)
            ->where('status', 'borrowed')
            ->get();

        // Get recent books
        $recentBooks = \App\Models\Book::where('status', 'available')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('student.library.index', compact('libraryStats', 'myBooks', 'recentBooks'));
    }

    public function search(Request $request)
    {
        return view('student.library.search');
    }

    public function books()
    {
        return view('student.library.books');
    }

    public function myBooks()
    {
        return view('student.library.my-books');
    }

    public function requestBook($bookId)
    {
        // Placeholder for book request
        return redirect()->route('student.library.my-books')->with('success', 'Book requested successfully');
    }
}