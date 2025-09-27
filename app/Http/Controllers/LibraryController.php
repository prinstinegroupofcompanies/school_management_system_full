<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        return view('library.index');
    }

    public function books()
    {
        return view('library.books');
    }

    public function createBook()
    {
        return view('library.books.create');
    }

    public function storeBook(Request $request)
    {
        // Placeholder for book creation
        return redirect()->route('library.books')->with('success', 'Book created successfully');
    }

    public function members()
    {
        return view('library.members');
    }

    public function issued()
    {
        return view('library.issued');
    }
}