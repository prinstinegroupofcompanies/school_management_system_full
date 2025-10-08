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
                        <li class="breadcrumb-item"><a href="{{ route('library.issues') }}">Issues</a></li>
                        <li class="breadcrumb-item active">Issue New Book</li>
                    </ol>
                </div>
                <h4 class="page-title">Issue New Book</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Book Issue Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('library.issues.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="book_id" class="form-label">Select Book <span class="text-danger">*</span></label>
                                    <select class="form-control @error('book_id') is-invalid @enderror" id="book_id" name="book_id" required>
                                        <option value="">Select Book</option>
                                        @foreach(\App\Models\Book::where('status', 'available')->get() as $book)
                                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                                {{ $book->title }} by {{ $book->author }} ({{ $book->isbn ?? 'No ISBN' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('book_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="member_id" class="form-label">Select Member <span class="text-danger">*</span></label>
                                    <select class="form-control @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                                        <option value="">Select Member</option>
                                        @foreach(\App\Models\LibraryMember::where('is_active', true)->with('user')->get() as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->user->name ?? 'N/A' }} ({{ $member->member_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('member_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="issue_date" class="form-label">Issue Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                           id="issue_date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+14 days'))) }}" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="issue_no" class="form-label">Issue Number</label>
                                    <input type="text" class="form-control @error('issue_no') is-invalid @enderror" 
                                           id="issue_no" name="issue_no" value="{{ old('issue_no', 'ISS' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT)) }}">
                                    @error('issue_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fine_amount" class="form-label">Fine Amount (if any)</label>
                                    <input type="number" step="0.01" class="form-control @error('fine_amount') is-invalid @enderror" 
                                           id="fine_amount" name="fine_amount" value="{{ old('fine_amount', 0) }}" min="0">
                                    @error('fine_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('library.issues') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Issue Book</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
