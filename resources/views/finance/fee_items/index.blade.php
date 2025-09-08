@extends('layouts.finance')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Fee Items</h1>
            <p class="text-muted mb-0">Manage tuition, books, uniforms, and other billable items.</p>
        </div>
        <a class="btn btn-primary" href="{{ route('finance.fee-items.create') }}">
            <i class="bi bi-plus-lg me-1"></i> Add Item
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="row g-3" method="GET">
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @if(request('class_id')==$class->id) selected @endif>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <input type="text" name="semester" class="form-control" placeholder="Semester" value="{{ request('semester') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-control" placeholder="Year" value="{{ request('year') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="active" class="form-select">
                        <option value="">All</option>
                        <option value="1" @if(request('active')==='1') selected @endif>Active</option>
                        <option value="0" @if(request('active')==='0') selected @endif>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse($feeItems as $item)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title mb-0">{{ $item->item_name }}</h5>
                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="text-muted small mb-3">
                        <div>Class: <strong>{{ optional($item->classRoom)->name ?? 'All' }}</strong></div>
                        <div>Term: <strong>{{ $item->semester ?? 'All' }} {{ $item->year ?? '' }}</strong></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <div>
                            <div class="small text-muted">Qty × Unit</div>
                            <div><strong>{{ $item->quantity }}</strong> × <strong>{{ number_format($item->price_per_unit, 2) }}</strong></div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted">Total</div>
                            <div class="h5 mb-0">{{ number_format($item->total, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('finance.fee-items.show', $item) }}" class="btn btn-outline-primary btn-sm">View</a>
                    <a href="{{ route('finance.fee-items.edit', $item) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                    <form action="{{ route('finance.fee-items.destroy', $item) }}" method="POST" class="ms-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this fee item?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">No fee items found.</div>
        </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $feeItems->withQueryString()->links() }}
    </div>
</div>
@endsection


