<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\LibraryMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LibraryController extends Controller
{
    public function index()
    {
        try {
            // Get library statistics
            $stats = [
                'total_books' => Book::count(),
                'available_books' => Book::where('available_copies', '>', 0)->count(),
                'borrowed_books' => BookIssue::where('status', 'issued')->count(),
                'total_members' => LibraryMember::where('is_active', true)->count(),
                'active_issues' => BookIssue::where('status', 'issued')->count(),
                'overdue_books' => BookIssue::where('status', 'issued')
                    ->where('due_date', '<', now())
                    ->count(),
                'total_fines' => BookIssue::where('fine_amount', '>', 0)
                    ->where('fine_paid', false)
                    ->sum('fine_amount'),
            ];

            // Get recent book issues
            $recentIssues = BookIssue::with(['book', 'member.user'])
                ->latest('issue_date')
                ->limit(10)
                ->get();

            // Get popular books
            $popularBooks = Book::with('category')
                ->orderBy('views_count', 'desc')
                ->orderBy('downloads_count', 'desc')
                ->limit(5)
                ->get();

            // Get overdue books
            $overdueBooks = BookIssue::with(['book', 'member.user'])
                ->where('status', 'issued')
                ->where('due_date', '<', now())
                ->orderBy('due_date')
                ->limit(10)
                ->get();

            // Get book categories
            $categories = BookCategory::withCount('books')->get();

            return view('library.index', compact(
                'stats',
                'recentIssues',
                'popularBooks',
                'overdueBooks',
                'categories'
            ));
        } catch (\Exception $e) {
            \Log::error('LibraryController index error: ' . $e->getMessage());
            
            // Fallback data if database issues
            $stats = [
                'total_books' => 0,
                'available_books' => 0,
                'borrowed_books' => 0,
                'total_members' => 0,
                'active_issues' => 0,
                'overdue_books' => 0,
                'total_fines' => 0,
            ];
            
            $recentIssues = collect();
            $popularBooks = collect();
            $overdueBooks = collect();
            $categories = collect();
            
            return view('library.index', compact(
                'stats',
                'recentIssues',
                'popularBooks',
                'overdueBooks',
                'categories'
            ));
        }
    }

    public function books(Request $request)
    {
        try {
            $query = Book::with(['category', 'subcategory']);

            // Search functionality
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
                    case 'unavailable':
                        $query->where('available_copies', 0);
                        break;
                    case 'digital':
                        $query->where('is_digital', true);
                        break;
                    case 'physical':
                        $query->where('is_digital', false);
                        break;
                }
            }

            // Sort options
            $sortBy = $request->get('sort', 'title');
            $sortOrder = $request->get('order', 'asc');
            
            switch ($sortBy) {
                case 'title':
                    $query->orderBy('title', $sortOrder);
                    break;
                case 'author':
                    $query->orderBy('author', $sortOrder);
                    break;
                case 'publication_year':
                    $query->orderBy('publication_year', $sortOrder);
                    break;
                case 'rating':
                    $query->orderBy('rating', $sortOrder);
                    break;
                case 'views':
                    $query->orderBy('views_count', $sortOrder);
                    break;
                default:
                    $query->orderBy('title', 'asc');
            }

            $books = $query->paginate(20);
            $categories = BookCategory::withCount('books')->get();

            return view('library.books.index', compact('books', 'categories'));
        } catch (\Exception $e) {
            \Log::error('LibraryController books error: ' . $e->getMessage());
            $books = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $categories = collect();
            return view('library.books.index', compact('books', 'categories'));
        }
    }

    public function createBook()
    {
        try {
            $categories = BookCategory::where('is_active', true)->get();
            $subcategories = BookSubcategory::where('is_active', true)->get();
            return view('library.books.index.create', compact('categories', 'subcategories'));
        } catch (\Exception $e) {
            \Log::error('LibraryController createBook error: ' . $e->getMessage());
            $categories = collect();
            $subcategories = collect();
            return view('library.books.index.create', compact('categories', 'subcategories'));
        }
    }

    public function storeBook(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'isbn' => 'nullable|string|max:50|unique:books,isbn',
                'author' => 'required|string|max:255',
                'publisher' => 'nullable|string|max:255',
                'edition' => 'nullable|string|max:50',
                'publication_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'description' => 'nullable|string',
                'summary' => 'nullable|string',
                'pages' => 'nullable|integer|min:1',
                'language' => 'nullable|string|max:50',
                'category_id' => 'required|exists:book_categories,id',
                'subcategory_id' => 'nullable|exists:book_subcategories,id',
                'location' => 'nullable|string|max:255',
                'total_copies' => 'required|integer|min:1',
                'price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'status' => 'required|string|in:available,unavailable,maintenance',
                'is_digital' => 'boolean',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:255',
            ]);

            $bookData = $request->all();
            $bookData['available_copies'] = $request->total_copies;
            $bookData['borrowed_copies'] = 0;
            $bookData['reserved_copies'] = 0;
            $bookData['is_active'] = true;
            $bookData['views_count'] = 0;
            $bookData['downloads_count'] = 0;
            $bookData['rating'] = 0;
            $bookData['rating_count'] = 0;

            Book::create($bookData);

            return redirect()->route('library.books.index')->with('success', 'Book created successfully');
        } catch (\Exception $e) {
            \Log::error('LibraryController storeBook error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create book. Please try again.');
        }
    }

    public function members(Request $request)
    {
        try {
            $query = LibraryMember::with('user');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('card_number', 'like', "%{$search}%");
            }

            // Filter by member type
            if ($request->filled('member_type')) {
                $query->where('member_type', $request->member_type);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $members = $query->orderBy('created_at', 'desc')->paginate(20);

            // Get member statistics
            $memberStats = [
                'total_members' => LibraryMember::count(),
                'active_members' => LibraryMember::where('is_active', true)->count(),
                'suspended_members' => LibraryMember::where('status', 'suspended')->count(),
                'expired_members' => LibraryMember::where('expiry_date', '<', now())->count(),
                'members_with_fines' => LibraryMember::where('fine_balance', '>', 0)->count(),
            ];

            return view('library.members.index', compact('members', 'memberStats'));
        } catch (\Exception $e) {
            \Log::error('LibraryController members error: ' . $e->getMessage());
            $members = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $memberStats = [
                'total_members' => 0,
                'active_members' => 0,
                'suspended_members' => 0,
                'expired_members' => 0,
                'members_with_fines' => 0,
            ];
            return view('library.members.index', compact('members', 'memberStats'));
        }
    }

    public function issued(Request $request)
    {
        try {
            $query = BookIssue::with(['book', 'member.user', 'issuedBy']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('issue_no', 'like', "%{$search}%")
                      ->orWhereHas('book', function($bookQuery) use ($search) {
                          $bookQuery->where('title', 'like', "%{$search}%")
                                   ->orWhere('isbn', 'like', "%{$search}%");
                      })
                      ->orWhereHas('member.user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('issue_date', [$request->start_date, $request->end_date]);
            }

            // Filter overdue books
            if ($request->filled('overdue') && $request->overdue) {
                $query->where('status', 'issued')
                      ->where('due_date', '<', now());
            }

            $issues = $query->orderBy('issue_date', 'desc')->paginate(20);

            // Get issue statistics
            $issueStats = [
                'total_issues' => BookIssue::count(),
                'active_issues' => BookIssue::where('status', 'issued')->count(),
                'returned_issues' => BookIssue::where('status', 'returned')->count(),
                'overdue_issues' => BookIssue::where('status', 'issued')
                    ->where('due_date', '<', now())
                    ->count(),
                'total_fines' => BookIssue::where('fine_amount', '>', 0)->sum('fine_amount'),
                'unpaid_fines' => BookIssue::where('fine_amount', '>', 0)
                    ->where('fine_paid', false)
                    ->sum('fine_amount'),
            ];

            return view('library.issued', compact('issues', 'issueStats'));
        } catch (\Exception $e) {
            \Log::error('LibraryController issued error: ' . $e->getMessage());
            $issues = collect()->paginate(20);
            $issueStats = [
                'total_issues' => 0,
                'active_issues' => 0,
                'returned_issues' => 0,
                'overdue_issues' => 0,
                'total_fines' => 0,
                'unpaid_fines' => 0,
            ];
            return view('library.issued', compact('issues', 'issueStats'));
        }
    }

    public function showBook(Book $book)
    {
        try {
            $book->load(['category', 'subcategory', 'bookIssues.member.user']);
            
            // Increment view count
            $book->incrementViews();
            
            // Get related books
            $relatedBooks = Book::where('category_id', $book->category_id)
                ->where('id', '!=', $book->id)
                ->limit(5)
                ->get();
            
            return view('library.books.index.show', compact('book', 'relatedBooks'));
        } catch (\Exception $e) {
            \Log::error('LibraryController showBook error: ' . $e->getMessage());
            return redirect()->route('library.books.index')
                ->with('error', 'Book not found.');
        }
    }

    public function issueBook(Request $request)
    {
        try {
            $request->validate([
                'book_id' => 'required|exists:books,id',
                'member_id' => 'required|exists:library_members,id',
                'due_date' => 'required|date|after:today',
                'issue_notes' => 'nullable|string',
            ]);

            $book = Book::findOrFail($request->book_id);
            $member = LibraryMember::findOrFail($request->member_id);

            // Check if book is available
            if ($book->available_copies <= 0) {
                return redirect()->back()->with('error', 'Book is not available for issue.');
            }

            // Check if member can borrow
            if (!$member->can_borrow) {
                return redirect()->back()->with('error', 'Member cannot borrow books at this time.');
            }

            // Check if member has reached borrowing limit
            if ($member->current_books_borrowed >= $member->max_books_allowed) {
                return redirect()->back()->with('error', 'Member has reached maximum borrowing limit.');
            }

            DB::transaction(function () use ($request, $book, $member) {
                // Create book issue record
                BookIssue::create([
                    'issue_no' => 'LIB-' . str_pad(BookIssue::count() + 1, 6, '0', STR_PAD_LEFT),
                    'book_id' => $request->book_id,
                    'member_id' => $request->member_id,
                    'issued_by' => auth()->id(),
                    'issue_date' => now(),
                    'due_date' => $request->due_date,
                    'status' => 'issued',
                    'issue_notes' => $request->issue_notes,
                ]);

                // Update book availability
                $book->decrement('available_copies');
                $book->increment('borrowed_copies');

                // Update member's borrowed count
                $member->incrementBorrowedBooks();
            });

            return redirect()->route('library.issued')->with('success', 'Book issued successfully.');
        } catch (\Exception $e) {
            \Log::error('LibraryController issueBook error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to issue book. Please try again.');
        }
    }

    public function returnBook(BookIssue $issue)
    {
        try {
            if ($issue->status !== 'issued') {
                return redirect()->back()->with('error', 'Book is not currently issued.');
            }

            DB::transaction(function () use ($issue) {
                // Mark as returned
                $issue->markAsReturned(auth()->user());

                // Update member's borrowed count
                $issue->member->decrementBorrowedBooks();
            });

            return redirect()->route('library.issued')->with('success', 'Book returned successfully.');
        } catch (\Exception $e) {
            \Log::error('LibraryController returnBook error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to return book. Please try again.');
        }
    }

    public function createMember()
    {
        try {
            return view('library.members.create');
        } catch (\Exception $e) {
            \Log::error('LibraryController createMember error: ' . $e->getMessage());
            return view('library.members.create');
        }
    }

    public function issues()
    {
        try {
            $issues = collect();
            return view('library.issues.index', compact('issues'));
        } catch (\Exception $e) {
            \Log::error('LibraryController issues error: ' . $e->getMessage());
            $issues = collect();
            return view('library.issues.index', compact('issues'));
        }
    }

    public function createIssue()
    {
        try {
            return view('library.issues.create');
        } catch (\Exception $e) {
            \Log::error('LibraryController createIssue error: ' . $e->getMessage());
            return view('library.issues.create');
        }
    }
}