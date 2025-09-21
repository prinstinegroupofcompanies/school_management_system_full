<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $fee->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 18px;
            margin-top: 15px;
        }
        .details-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .details-box {
            width: 45%;
        }
        .details-box h3 {
            margin-top: 0;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .fee-table th,
        .fee-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .fee-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            background-color: #e3f2fd;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ \App\Models\SystemSetting::get('school_name', config('app.name', 'School Management System')) }}</div>
        <div>{{ \App\Models\SystemSetting::get('school_address', 'School Address') }}</div>
        <div>{{ \App\Models\SystemSetting::get('school_city', '') }}, {{ \App\Models\SystemSetting::get('school_state', '') }} {{ \App\Models\SystemSetting::get('school_postal_code', '') }}</div>
        <div>Phone: {{ \App\Models\SystemSetting::get('school_phone', 'N/A') }} | Email: {{ \App\Models\SystemSetting::get('school_email', 'info@school.com') }}</div>
        <div class="invoice-title">STUDENT FEE INVOICE</div>
    </div>

    <div class="details-section">
        <div class="details-box">
            <h3>Billing Information</h3>
            <p><strong>Student Name:</strong> {{ $fee->student->first_name ?? '' }} {{ $fee->student->last_name ?? '' }}</p>
            <p><strong>Student ID:</strong> {{ $fee->student->student_id ?? $fee->student->admission_no ?? 'N/A' }}</p>
            <p><strong>Class:</strong> {{ $fee->student->classRoom->name ?? 'N/A' }}</p>
            <p><strong>Academic Year:</strong> {{ $fee->student->academic_year ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $fee->student->user->address ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $fee->student->user->phone ?? 'N/A' }}</p>
        </div>
        <div class="details-box">
            <h3>Invoice Information</h3>
            <p><strong>Invoice #:</strong> INV-{{ str_pad($fee->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Date:</strong> {{ now()->format('F j, Y') }}</p>
            <p><strong>Academic Period:</strong> {{ $fee->semester ?? 'All Semesters' }} - {{ $fee->year }}</p>
            <p><strong>Due Date:</strong> {{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('F j, Y') : 'N/A' }}</p>
        </div>
    </div>

    <table class="fee-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div>
                        <strong>{{ $fee->feeStructure->name ?? ($fee->fee_type ? ucfirst($fee->fee_type) : 'Tuition') }} Fee</strong>
                        <br>{{ $fee->semester ?? 'Academic Year' }} {{ $fee->year }}
                        <br>Class: {{ $fee->student->classRoom->name ?? 'N/A' }}
                        @if($fee->description)
                            <br><small>{{ $fee->description }}</small>
                        @endif
                    </div>
                </td>
                <td>${{ number_format($fee->total_amount, 2) }}</td>
                <td>
                    @if($fee->status === 'paid')
                        <span class="status status-paid">PAID</span>
                    @elseif($fee->status === 'partial')
                        <span class="status status-partial">PARTIAL</span>
                    @else
                        <span class="status status-unpaid">UNPAID</span>
                    @endif
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td><strong>Total Amount</strong></td>
                <td><strong>${{ number_format($fee->total_amount, 2) }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>Amount Paid</strong></td>
                <td><strong>${{ number_format($fee->paid_amount, 2) }}</strong></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td><strong>Outstanding Balance</strong></td>
                <td><strong>${{ number_format($fee->balance, 2) }}</strong></td>
                <td>
                    @if($fee->balance <= 0)
                        <span class="status status-paid">FULLY PAID</span>
                    @else
                        <span class="status status-unpaid">OUTSTANDING</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>

    @if($fee->balance > 0)
    <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <strong>Payment Instructions:</strong><br>
        Please make payment for the outstanding balance of ${{ number_format($fee->balance, 2) }} by {{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('F j, Y') : 'the due date' }}.
        Contact the finance office for payment methods and details.
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated invoice. For any queries, please contact the finance office.</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
