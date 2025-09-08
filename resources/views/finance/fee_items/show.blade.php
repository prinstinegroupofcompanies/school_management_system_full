@extends('layouts.finance')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Fee Item Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('finance.fee-items.edit', $feeItem) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('finance.fee-items.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="text-muted small">Item Name</div>
                    <div class="h5">{{ $feeItem->item_name }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Status</div>
                    <span class="badge {{ $feeItem->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $feeItem->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Class</div>
                    <div>{{ optional($feeItem->classRoom)->name ?? 'All' }}</div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="text-muted small">Quantity</div>
                    <div class="h6">{{ $feeItem->quantity }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Unit Price</div>
                    <div class="h6">{{ number_format($feeItem->price_per_unit, 2) }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Total</div>
                    <div class="h6">{{ number_format($feeItem->total, 2) }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Term</div>
                    <div class="h6">{{ $feeItem->semester ?? 'All' }} {{ $feeItem->year ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


