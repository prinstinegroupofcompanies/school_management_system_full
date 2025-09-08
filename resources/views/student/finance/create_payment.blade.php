@extends('layouts.student')

@section('content')
<div class="container">
    <h1>Submit Payment</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="mb-3">
        <strong>Invoice:</strong> {{ $fee->semester ?? 'All' }} - {{ $fee->year }} | Balance: {{ number_format($fee->balance, 2) }}
    </div>
    <div class="mb-3">
        <p><strong>Bank:</strong> {{ $bankDetails['bank_name'] ?? '' }} ({{ $bankDetails['bank_account'] ?? '' }})</p>
        <p><strong>Mobile Money:</strong> {{ $mobileMoney['provider'] ?? '' }} - {{ $mobileMoney['number'] ?? '' }}</p>
    </div>
    <form method="POST" action="{{ route('student.finance.store-payment', $fee) }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Amount</label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="{{ old('amount') }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Payment Method</label>
                <select name="payment_method" class="form-select" required>
                    <option value="Bank" @if(old('payment_method')==='Bank') selected @endif>Bank</option>
                    <option value="Mobile Money" @if(old('payment_method')==='Mobile Money') selected @endif>Mobile Money</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Transaction Reference</label>
                <input type="text" name="transaction_reference" class="form-control" value="{{ old('transaction_reference') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ old('date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label">Details (optional)</label>
                <input type="text" name="details" class="form-control" value="{{ old('details') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Receipt (jpg/jpeg/png/pdf, max 2MB)</label>
            <input type="file" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit Payment</button>
        <a href="{{ route('student.finance.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection


