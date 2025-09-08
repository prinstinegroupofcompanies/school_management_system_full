<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary { margin: 10px 0; }
        .totals { margin-top: 8px; width: 100%; }
        .totals td { padding: 4px 6px; }
        .right { text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
    </head>
<body>
    <div class="header">
        <div>
            @if(!empty($schoolLogoUrl))
                <img src="{{ $schoolLogoUrl }}" alt="Logo" style="height:50px;margin-bottom:6px;">
            @endif
            <h2>{{ $schoolName ?? 'School Fee Invoice' }}</h2>
            <div>Date: {{ now()->format('Y-m-d') }}</div>
        </div>
        <div>
            <div>Invoice #: {{ $invoiceNo ?? ('INV-' . $studentFee->id) }}</div>
            <div>Student: {{ $student->full_name ?? $student->user->name }}</div>
            <div>ID: {{ $student->student_id ?? $student->id }}</div>
            <div>Class: {{ optional($student->class)->name }}</div>
            <div>Term: {{ $studentFee->semester ?? 'All' }} - {{ $studentFee->year }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php($lines = $studentFee->fee_breakdown ?? [])
            @foreach($lines as $line)
            <tr>
                <td>{{ $line['item_name'] }}</td>
                <td>{{ $line['quantity'] }}</td>
                <td>{{ number_format($line['price_per_unit'], 2) }}</td>
                <td>{{ number_format($line['total'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="right"><strong>Total:</strong></td>
            <td class="right" style="width:140px;">{{ number_format($studentFee->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="right"><strong>Paid:</strong></td>
            <td class="right">{{ number_format($studentFee->paid_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="right"><strong>Balance:</strong></td>
            <td class="right">{{ number_format($studentFee->balance, 2) }}</td>
        </tr>
    </table>

    <div class="summary">
        <h4>Payment Options</h4>
        <p><strong>Bank:</strong> {{ $bankDetails['bank_name'] ?? '' }} ({{ $bankDetails['bank_account'] ?? '' }})</p>
        <p><strong>Mobile Money:</strong> {{ $mobileMoney['provider'] ?? '' }} - {{ $mobileMoney['number'] ?? '' }}</p>
    </div>

    @if(!empty($note))
        <div class="summary">
            <strong>Note:</strong>
            <p>{{ $note }}</p>
        </div>
    @endif
    
</body>
</html>


