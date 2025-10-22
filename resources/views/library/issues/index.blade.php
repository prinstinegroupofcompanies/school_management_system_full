@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('library.index') }}">Library</a></li>
                        <li class="breadcrumb-item active">Book Issues</li>
                    </ol>
                </div>
                <h4 class="page-title">Book Issues</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Issues Management</h5>
                    <a href="{{ route('library.issues.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Issue New Book
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('library.issues.index') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search issues..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('library.issues.index') }}" class="d-flex">
                                <select name="status" class="form-control me-2">
                                    <option value="">All Status</option>
                                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                            </form>
                        </div>
                    </div>

                    <!-- Issues Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Issue #</th>
                                    <th>Book</th>
                                    <th>Member</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($issues as $issue)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $issue->issue_no ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $issue->book->title ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">by {{ $issue->book->author ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $issue->member->user->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $issue->member->member_id ?? 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $issue->issue_date ? \Carbon\Carbon::parse($issue->issue_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $issue->due_date ? \Carbon\Carbon::parse($issue->due_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @if($issue->status == 'issued')
                                            <span class="badge bg-warning">Issued</span>
                                        @elseif($issue->status == 'returned')
                                            <span class="badge bg-success">Returned</span>
                                        @elseif($issue->status == 'overdue')
                                            <span class="badge bg-danger">Overdue</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($issue->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('library.issues.show', $issue) }}" class="btn btn-sm btn-outline-info">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            @if($issue->status == 'issued')
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="returnBook({{ $issue->id }})">
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-book-open-page-variant mdi-48px"></i>
                                            <p class="mt-2">No book issues found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            @if($issues instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                Showing {{ $issues->firstItem() ?? 0 }} to {{ $issues->lastItem() ?? 0 }} of {{ $issues->total() }} entries
                            @else
                                Showing 0 to 0 of 0 entries
                            @endif
                        </div>
                        <div>
                            @if($issues instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                {{ $issues->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Return Book Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Return Book</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="returnForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Are you sure you want to return this book?</p>
                    <div class="form-group">
                        <label for="return_notes">Return Notes (Optional)</label>
                        <textarea class="form-control" id="return_notes" name="return_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Return Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function returnBook(issueId) {
    document.getElementById('returnForm').action = '/library/issues/' + issueId + '/return';
    $('#returnModal').modal('show');
}
</script>
@endpush