@extends('layouts.finance')

@section('content')
<div class="container">
    <h1>Pending Payments</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student</th>
                <th>Class</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Submitted</th>
                <th>Receipt</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pending as $p)
            <tr>
                <td>{{ $p->student->user->name ?? 'Student' }}</td>
                <td>{{ optional($p->student->class)->name }}</td>
                <td>{{ number_format($p->amount, 2) }}</td>
                <td>{{ $p->payment_method }}</td>
                <td>{{ $p->transaction_reference }}</td>
                <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    @if($p->receipt_path)
                        <a href="{{ asset('storage/'.$p->receipt_path) }}" target="_blank">View</a>
                    @endif
                </td>
                <td>
                    <form method="POST" action="{{ route('finance.payments.approve', $p) }}" style="display:inline-block">
                        @csrf
                        <button class="btn btn-sm btn-success" onclick="return confirm('Approve this payment?')">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('finance.payments.reject', $p) }}" style="display:inline-block">
                        @csrf
                        <input type="hidden" name="reason" value="Insufficient details">
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Reject this payment?')">Reject</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center">No pending payments.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $pending->links() }}
</div>
@endsection


