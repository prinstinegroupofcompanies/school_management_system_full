<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class LibraryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Student record not found');
        }

        // Mock library data for students
        $books = [
            [
                'id' => 1,
                'title' => 'Mathematics for Grade 10',
                'author' => 'Dr. John Smith',
                'isbn' => '978-1234567890',
                'category' => 'Mathematics',
                'available' => true,
                'due_date' => null,
            ],
            [
                'id' => 2,
                'title' => 'English Literature',
                'author' => 'Jane Doe',
                'isbn' => '978-0987654321',
                'category' => 'English',
                'available' => false,
                'due_date' => '2024-09-15',
            ],
            [
                'id' => 3,
                'title' => 'Science Fundamentals',
                'author' => 'Dr. Robert Johnson',
                'isbn' => '978-1122334455',
                'category' => 'Science',
                'available' => true,
                'due_date' => null,
            ],
        ];

        $borrowedBooks = [
            [
                'id' => 2,
                'title' => 'English Literature',
                'author' => 'Jane Doe',
                'borrow_date' => '2024-08-15',
                'due_date' => '2024-09-15',
                'status' => 'borrowed',
            ],
        ];

        $libraryStats = [
            'total_books' => 1500,
            'available_books' => 1200,
            'borrowed_books' => 300,
            'my_borrowed' => count($borrowedBooks),
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
        
        // Mock search results
        $books = [
            [
                'id' => 1,
                'title' => 'Mathematics for Grade 10',
                'author' => 'Dr. John Smith',
                'isbn' => '978-1234567890',
                'category' => 'Mathematics',
                'available' => true,
            ],
            [
                'id' => 3,
                'title' => 'Science Fundamentals',
                'author' => 'Dr. Robert Johnson',
                'isbn' => '978-1122334455',
                'category' => 'Science',
                'available' => true,
            ],
        ];

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
