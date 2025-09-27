<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        return view('student.library.index');
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