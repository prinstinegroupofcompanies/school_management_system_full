<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\LibraryMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LibraryController extends BaseController
{
    public function books(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view books')) {
                return $this->forbiddenResponse('You do not have permission to view books');
            }

            $query = Book::with(['category', 'subcategory']);

            if ($search = $this->getSearchQuery($request)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $books = $query->paginate($perPage);

            return $this->paginatedResponse($books, 'Books retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve books: ' . $e->getMessage());
        }
    }

    public function storeBook(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('create books')) {
                return $this->forbiddenResponse('You do not have permission to create books');
            }

            $rules = [
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|unique:books,isbn',
                'category_id' => 'required|exists:book_categories,id',
                'subcategory_id' => 'nullable|exists:book_subcategories,id',
                'publisher' => 'nullable|string|max:255',
                'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'edition' => 'nullable|string|max:50',
                'pages' => 'nullable|integer|min:1',
                'price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'copies' => 'required|integer|min:1',
                'available_copies' => 'required|integer|min:0',
                'location' => 'nullable|string|max:100',
                'description' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $book = Book::create([
                'title' => $validated['title'],
                'author' => $validated['author'],
                'isbn' => $validated['isbn'],
                'category_id' => $validated['category_id'],
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'publisher' => $validated['publisher'] ?? null,
                'publication_year' => $validated['publication_year'] ?? null,
                'edition' => $validated['edition'] ?? null,
                'pages' => $validated['pages'] ?? null,
                'price' => $validated['price'] ?? null,
                'currency' => $validated['currency'] ?? 'LRD',
                'copies' => $validated['copies'],
                'available_copies' => $validated['available_copies'],
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'active',
            ]);

            $book->load(['category', 'subcategory']);

            return $this->successResponse($book, 'Book created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create book: ' . $e->getMessage());
        }
    }

    public function showBook(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('view books')) {
                return $this->forbiddenResponse('You do not have permission to view books');
            }

            $book = Book::with(['category', 'subcategory'])->find($id);

            if (!$book) {
                return $this->notFoundResponse('Book not found');
            }

            return $this->successResponse($book, 'Book retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve book: ' . $e->getMessage());
        }
    }

    public function updateBook(Request $request, int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('edit books')) {
                return $this->forbiddenResponse('You do not have permission to edit books');
            }

            $book = Book::find($id);

            if (!$book) {
                return $this->notFoundResponse('Book not found');
            }

            $rules = [
                'title' => 'sometimes|required|string|max:255',
                'author' => 'sometimes|required|string|max:255',
                'isbn' => 'sometimes|required|string|unique:books,isbn,' . $id,
                'category_id' => 'sometimes|required|exists:book_categories,id',
                'subcategory_id' => 'nullable|exists:book_subcategories,id',
                'publisher' => 'nullable|string|max:255',
                'publication_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'edition' => 'nullable|string|max:50',
                'pages' => 'nullable|integer|min:1',
                'price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'copies' => 'sometimes|required|integer|min:1',
                'available_copies' => 'sometimes|required|integer|min:0',
                'location' => 'nullable|string|max:100',
                'description' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $book->update(array_filter([
                'title' => $validated['title'] ?? null,
                'author' => $validated['author'] ?? null,
                'isbn' => $validated['isbn'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'subcategory_id' => $validated['subcategory_id'] ?? null,
                'publisher' => $validated['publisher'] ?? null,
                'publication_year' => $validated['publication_year'] ?? null,
                'edition' => $validated['edition'] ?? null,
                'pages' => $validated['pages'] ?? null,
                'price' => $validated['price'] ?? null,
                'currency' => $validated['currency'] ?? null,
                'copies' => $validated['copies'] ?? null,
                'available_copies' => $validated['available_copies'] ?? null,
                'location' => $validated['location'] ?? null,
                'description' => $validated['description'] ?? null,
            ]));

            $book->load(['category', 'subcategory']);

            return $this->successResponse($book, 'Book updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update book: ' . $e->getMessage());
        }
    }

    public function destroyBook(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('delete books')) {
                return $this->forbiddenResponse('You do not have permission to delete books');
            }

            $book = Book::find($id);

            if (!$book) {
                return $this->notFoundResponse('Book not found');
            }

            $book->delete();

            return $this->successResponse(null, 'Book deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete book: ' . $e->getMessage());
        }
    }

    public function categories(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view books')) {
                return $this->forbiddenResponse('You do not have permission to view books');
            }

            $categories = BookCategory::with('subcategories')->get();

            return $this->successResponse($categories, 'Book categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve book categories: ' . $e->getMessage());
        }
    }

    public function issues(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('manage book issues')) {
                return $this->forbiddenResponse('You do not have permission to manage book issues');
            }

            $query = BookIssue::with(['book', 'member.user']);

            if ($search = $this->getSearchQuery($request)) {
                $query->whereHas('member.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $issues = $query->paginate($perPage);

            return $this->paginatedResponse($issues, 'Book issues retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve book issues: ' . $e->getMessage());
        }
    }

    public function storeIssue(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('manage book issues')) {
                return $this->forbiddenResponse('You do not have permission to manage book issues');
            }

            $rules = [
                'book_id' => 'required|exists:books,id',
                'member_id' => 'required|exists:library_members,id',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after:issue_date',
                'return_date' => 'nullable|date|after:issue_date',
                'fine_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ];

            $validated = $this->validateRequest($request, $rules);

            $issue = BookIssue::create([
                'book_id' => $validated['book_id'],
                'member_id' => $validated['member_id'],
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'return_date' => $validated['return_date'] ?? null,
                'fine_amount' => $validated['fine_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'status' => 'issued',
            ]);

            $issue->load(['book', 'member.user']);

            return $this->successResponse($issue, 'Book issue created successfully', 201);
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create book issue: ' . $e->getMessage());
        }
    }

    public function returnBook(int $id): JsonResponse
    {
        try {
            if (!$this->checkPermission('manage book issues')) {
                return $this->forbiddenResponse('You do not have permission to manage book issues');
            }

            $issue = BookIssue::find($id);

            if (!$issue) {
                return $this->notFoundResponse('Book issue not found');
            }

            $issue->update([
                'return_date' => now(),
                'status' => 'returned',
            ]);

            $issue->load(['book', 'member.user']);

            return $this->successResponse($issue, 'Book returned successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to return book: ' . $e->getMessage());
        }
    }

    public function members(Request $request): JsonResponse
    {
        try {
            if (!$this->checkPermission('view library members')) {
                return $this->forbiddenResponse('You do not have permission to view library members');
            }

            $query = LibraryMember::with(['user']);

            if ($search = $this->getSearchQuery($request)) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            $perPage = $this->getPerPage($request);
            $members = $query->paginate($perPage);

            return $this->paginatedResponse($members, 'Library members retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve library members: ' . $e->getMessage());
        }
    }

    public function statistics(): JsonResponse
    {
        try {
            if (!$this->checkPermission('view library reports')) {
                return $this->forbiddenResponse('You do not have permission to view library reports');
            }

            $stats = [
                'total_books' => Book::count(),
                'total_categories' => BookCategory::count(),
                'total_members' => LibraryMember::count(),
                'total_issues' => BookIssue::count(),
                'active_issues' => BookIssue::where('status', 'issued')->count(),
                'overdue_issues' => BookIssue::where('due_date', '<', now())
                    ->where('status', 'issued')
                    ->count(),
                'books_by_category' => Book::with('category')
                    ->selectRaw('category_id, count(*) as count')
                    ->groupBy('category_id')
                    ->get(),
            ];

            return $this->successResponse($stats, 'Library statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve library statistics: ' . $e->getMessage());
        }
    }
}
