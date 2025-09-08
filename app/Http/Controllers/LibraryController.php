<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\LibraryMember;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Book::with(['category'])->paginate(15);
        $categories = BookCategory::all();
        return view('library.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = BookCategory::all();
        return view('library.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:255|unique:books',
            'category_id' => 'required|exists:book_categories,id',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'edition' => 'nullable|string|max:255',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        Book::create($request->all());

        return redirect()->route('library.index')
            ->with('success', 'Book added successfully.');
    }

    public function show(Book $book)
    {
        $book->load(['category', 'issues.member.user', 'issues.member.student', 'issues.member.teacher']);
        return view('library.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $categories = BookCategory::all();
        return view('library.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:255|unique:books,isbn,' . $book->id,
            'category_id' => 'required|exists:book_categories,id',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'edition' => 'nullable|string|max:255',
            'total_copies' => 'required|integer|min:1',
            'available_copies' => 'required|integer|min:0|lte:total_copies',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        $book->update($request->all());

        return redirect()->route('library.index')
            ->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('library.index')
            ->with('success', 'Book deleted successfully.');
    }

    public function categories()
    {
        $categories = BookCategory::withCount('books')->get();
        return view('library.categories', compact('categories'));
    }

    public function createCategory()
    {
        return view('library.create-category');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:book_categories',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        BookCategory::create($request->all());

        return redirect()->route('library.categories')
            ->with('success', 'Category created successfully.');
    }

    public function editCategory(BookCategory $category)
    {
        return view('library.edit-category', compact('category'));
    }

    public function updateCategory(Request $request, BookCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:book_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($request->all());

        return redirect()->route('library.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(BookCategory $category)
    {
        if ($category->books()->count() > 0) {
            return redirect()->route('library.categories')
                ->with('error', 'Cannot delete category with existing books.');
        }

        $category->delete();

        return redirect()->route('library.categories')
            ->with('success', 'Category deleted successfully.');
    }

    public function issues()
    {
        $issues = BookIssue::with(['book', 'member.user', 'member.student', 'member.teacher'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('library.issues', compact('issues'));
    }

    public function createIssue()
    {
        $books = Book::where('available_copies', '>', 0)->with('category')->get();
        $members = LibraryMember::with(['user', 'student', 'teacher'])->get();
        return view('library.create-issue', compact('books', 'members'));
    }

    public function storeIssue(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:library_members,id',
            'issue_date' => 'required|date|before_or_equal:today',
            'due_date' => 'required|date|after:issue_date',
            'notes' => 'nullable|string',
        ]);

        $book = Book::findOrFail($request->book_id);
        
        if ($book->available_copies <= 0) {
            return back()->withErrors(['book_id' => 'No copies available for this book.']);
        }

        DB::transaction(function () use ($request, $book) {
            BookIssue::create($request->all());
            $book->decrement('available_copies');
        });

        return redirect()->route('library.issues')
            ->with('success', 'Book issued successfully.');
    }

    public function showIssue(BookIssue $issue)
    {
        $issue->load(['book.category', 'member.user', 'member.student', 'member.teacher']);
        return view('library.show-issue', compact('issue'));
    }

    public function editIssue(BookIssue $issue)
    {
        $books = Book::with('category')->get();
        $members = LibraryMember::with(['user', 'student', 'teacher'])->get();
        return view('library.edit-issue', compact('issue', 'books', 'members'));
    }

    public function updateIssue(Request $request, BookIssue $issue)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:library_members,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'return_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string',
        ]);

        $oldBookId = $issue->book_id;
        $newBookId = $request->book_id;

        DB::transaction(function () use ($request, $issue, $oldBookId, $newBookId) {
            $issue->update($request->all());

            // Handle book copy changes
            if ($oldBookId != $newBookId) {
                Book::find($oldBookId)->increment('available_copies');
                Book::find($newBookId)->decrement('available_copies');
            }
        });

        return redirect()->route('library.issues')
            ->with('success', 'Book issue updated successfully.');
    }

    public function destroyIssue(BookIssue $issue)
    {
        DB::transaction(function () use ($issue) {
            $issue->delete();
            $issue->book->increment('available_copies');
        });

        return redirect()->route('library.issues')
            ->with('success', 'Book issue deleted successfully.');
    }

    public function returnBook(BookIssue $issue)
    {
        if ($issue->return_date) {
            return redirect()->route('library.issues')
                ->with('error', 'Book already returned.');
        }

        DB::transaction(function () use ($issue) {
            $issue->update(['return_date' => now()]);
            $issue->book->increment('available_copies');
        });

        return redirect()->route('library.issues')
            ->with('success', 'Book returned successfully.');
    }

    public function members()
    {
        $members = LibraryMember::with(['user', 'student', 'teacher'])->paginate(15);
        return view('library.members', compact('members'));
    }

    public function createMember()
    {
        $students = Student::doesntHave('libraryMember')->get();
        $teachers = Teacher::doesntHave('libraryMember')->get();
        return view('library.create-member', compact('students', 'teachers'));
    }

    public function storeMember(Request $request)
    {
        $request->validate([
            'user_type' => 'required|in:student,teacher',
            'user_id' => 'required|integer',
            'member_id' => 'required|string|max:255|unique:library_members',
            'join_date' => 'required|date|before_or_equal:today',
            'expiry_date' => 'required|date|after:join_date',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        // Verify user exists
        if ($request->user_type === 'student') {
            $user = Student::findOrFail($request->user_id);
        } else {
            $user = Teacher::findOrFail($request->user_id);
        }

        LibraryMember::create($request->all());

        return redirect()->route('library.members')
            ->with('success', 'Library member created successfully.');
    }

    public function showMember(LibraryMember $member)
    {
        $member->load(['user', 'student', 'teacher', 'issues.book']);
        return view('library.show-member', compact('member'));
    }

    public function editMember(LibraryMember $member)
    {
        $students = Student::all();
        $teachers = Teacher::all();
        return view('library.edit-member', compact('member', 'students', 'teachers'));
    }

    public function updateMember(Request $request, LibraryMember $member)
    {
        $request->validate([
            'user_type' => 'required|in:student,teacher',
            'user_id' => 'required|integer',
            'member_id' => 'required|string|max:255|unique:library_members,member_id,' . $member->id,
            'join_date' => 'required|date',
            'expiry_date' => 'required|date|after:join_date',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string',
        ]);

        $member->update($request->all());

        return redirect()->route('library.members')
            ->with('success', 'Library member updated successfully.');
    }

    public function destroyMember(LibraryMember $member)
    {
        if ($member->issues()->whereNull('return_date')->count() > 0) {
            return redirect()->route('library.members')
                ->with('error', 'Cannot delete member with active book issues.');
        }

        $member->delete();

        return redirect()->route('library.members')
            ->with('success', 'Library member deleted successfully.');
    }

    public function overdue()
    {
        $overdueIssues = BookIssue::where('due_date', '<', now())
            ->whereNull('return_date')
            ->with(['book', 'member.user', 'member.student', 'member.teacher'])
            ->paginate(15);
        
        return view('library.overdue', compact('overdueIssues'));
    }

    public function reports()
    {
        $totalBooks = Book::count();
        $totalIssues = BookIssue::count();
        $activeIssues = BookIssue::whereNull('return_date')->count();
        $overdueIssues = BookIssue::where('due_date', '<', now())
            ->whereNull('return_date')
            ->count();
        
        $popularBooks = Book::withCount('issues')
            ->orderBy('issues_count', 'desc')
            ->limit(10)
            ->get();
        
        $categoryStats = BookCategory::withCount('books')
            ->orderBy('books_count', 'desc')
            ->get();
        
        $monthlyIssues = BookIssue::selectRaw('strftime("%m", created_at) as month, COUNT(*) as total')
            ->whereRaw('strftime("%Y", created_at) = ?', [date('Y')])
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('library.reports', compact(
            'totalBooks',
            'totalIssues',
            'activeIssues',
            'overdueIssues',
            'popularBooks',
            'categoryStats',
            'monthlyIssues'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $category = $request->get('category');
        $status = $request->get('status');

        $books = Book::query()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('author', 'like', "%{$query}%")
                      ->orWhere('isbn', 'like', "%{$query}%");
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category_id', $category);
            })
            ->when($status, function ($q) use ($status) {
                if ($status === 'available') {
                    $q->where('available_copies', '>', 0);
                } elseif ($status === 'unavailable') {
                    $q->where('available_copies', 0);
                }
            })
            ->with('category')
            ->paginate(15);

        $categories = BookCategory::all();
        
        return view('library.search', compact('books', 'categories', 'query', 'category', 'status'));
    }
}
