@extends('layouts.finance')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Create Fee Item</h1>
        <a href="{{ route('finance.fee-items.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('finance.fee-items.store') }}" class="card p-3 shadow-sm">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" name="item_name" value="{{ old('item_name') }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" min="1" class="form-control" name="quantity" value="{{ old('quantity', 1) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Price per unit</label>
                <input type="number" step="0.01" min="0" class="form-control" name="price_per_unit" value="{{ old('price_per_unit', 0) }}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Total (auto if empty)</label>
                <input type="number" step="0.01" min="0" class="form-control" name="total" value="{{ old('total') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Class (optional)</label>
                <select class="form-select" name="class_id">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @if(old('class_id')==$class->id) selected @endif>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Semester (optional)</label>
                <input type="text" class="form-control" name="semester" value="{{ old('semester') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Year (optional)</label>
                <input type="number" min="2000" max="2100" class="form-control" name="year" value="{{ old('year', $currentYear) }}">
            </div>
        </div>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @if(old('is_active', 1)) checked @endif>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('finance.fee-items.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection


